<?php

namespace App\Http\Controllers;

use App\Events\QueueStatusUpdated;
use App\Models\Queue;
use App\Models\RuangAntri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function joinQueue(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        if (!in_array($user->role, ['mahasiswa', 'dosen'], true)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Hanya pengantre yang dapat mengambil nomor antrean.',
            ], 403);
        }

        $request->validate([
            'dean_id' => 'required|exists:users,kode',
            'service_id' => 'required|exists:services,id',
        ]);

        $todayJakarta = Carbon::now('Asia/Jakarta')->toDateString();
        $queue = null;
        $deanRoomStatus = 'closed';

        DB::transaction(function () use ($request, $user, $todayJakarta, &$queue, &$deanRoomStatus) {
            $activeQueueToSameDean = Queue::query()
                ->where('kode_user', $user->kode)
                ->where('kode_dosen', $request->dean_id)
                ->whereDate('created_at', $todayJakarta)
                ->whereIn('status', ['menunggu', 'diproses'])
                ->lockForUpdate()
                ->first();

            if ($activeQueueToSameDean) {
                abort(response()->json([
                    'status' => 'error',
                    'message' => 'Anda masih memiliki antrean aktif pada pejabat ini. Selesaikan dulu antrean sebelumnya.',
                ], 422));
            }

            $deanRoom = RuangAntri::query()
                ->where('kode_dosen', $request->dean_id)
                ->whereDate('tanggal_buka_ruang_antri', $todayJakarta)
                ->latest('updated_at')
                ->lockForUpdate()
                ->first();

            $deanRoomStatus = $deanRoom?->status_ruang ?? 'closed';

            // Default dianggap tutup kalau belum pernah buka antrean hari ini.
            if (!in_array($deanRoomStatus, ['open', 'occupied'], true)) {
                abort(response()->json([
                    'status' => 'error',
                    'message' => 'Ruangan dosen yang dipilih sedang tutup. Silakan pilih dosen lain.',
                ], 422));
            }

            $openedServiceIds = is_array($deanRoom?->service_ids) ? $deanRoom->service_ids : [];
            $openedServiceIds = array_values(array_unique(array_map('intval', array_filter(
                $openedServiceIds,
                fn ($id) => $id !== null && $id !== ''
            ))));

            if (count($openedServiceIds) > 0) {
                if (!in_array((int) $request->service_id, $openedServiceIds, true)) {
                    abort(response()->json([
                        'status' => 'error',
                        'message' => 'Jenis layanan tidak sesuai dengan layanan yang sedang dibuka dosen tersebut.',
                    ], 422));
                }
            } elseif ($deanRoom?->service_id && (int) $deanRoom->service_id !== (int) $request->service_id) {
                abort(response()->json([
                    'status' => 'error',
                    'message' => 'Jenis layanan tidak sesuai dengan layanan yang sedang dibuka dosen tersebut.',
                ], 422));
            }

            // Nomor antrean dihitung per dosen untuk hari berjalan.
            // Gunakan count agar tidak ikut loncat akibat data historis outlier.
            $existingCount = Queue::query()
                ->where('kode_dosen', $request->dean_id)
                ->whereDate('created_at', $todayJakarta)
                ->lockForUpdate()
                ->count();

            $queue = Queue::create([
                'kode_user' => $user->kode,
                'kode_dosen' => $request->dean_id,
                'service_id' => $request->service_id,
                'nomor_antrian' => $existingCount + 1,
                'status' => 'menunggu',
            ]);
        });

        try {
            event(new QueueStatusUpdated($request->dean_id, $deanRoomStatus ?? 'open', [
                'event' => 'queue_joined',
                'queue_id' => $queue->id,
                'nomor_antrian' => $queue->nomor_antrian,
                'kode_user' => $queue->kode_user,
                'mahasiswa_name' => $queue->user?->name,
                'service_name' => $queue->service?->nama_layanan,
            ]));
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json([
            'status' => 'ok',
            'queue' => $queue->load(['user', 'service', 'dosen']),
        ]);
    }
}
