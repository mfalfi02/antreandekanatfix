<?php

namespace App\Http\Controllers;

use App\Events\QueueStatusUpdated;
use App\Models\Queue;
use App\Models\RuangAntri;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $request->validate([
            'dean_id' => 'required|exists:users,kode',
            'service_id' => 'required|exists:services,id',
        ]);

        $todayJakarta = Carbon::now('Asia/Jakarta')->toDateString();
        $deanRoom = RuangAntri::query()
            ->where('kode_dosen', $request->dean_id)
            ->whereDate('tanggal_buka_ruang_antri', $todayJakarta)
            ->latest('updated_at')
            ->first();
        $deanRoomStatus = $deanRoom?->status_ruang;

        // Default dianggap tutup kalau belum pernah buka antrean hari ini.
        if (!in_array($deanRoomStatus, ['open', 'occupied'], true)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Ruangan dosen yang dipilih sedang tutup. Silakan pilih dosen lain.',
            ], 422);
        }

        if ($deanRoom?->service_id && (int) $deanRoom->service_id !== (int) $request->service_id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Jenis layanan tidak sesuai dengan layanan yang sedang dibuka dosen tersebut.',
            ], 422);
        }

        // Nomor antrean dihitung per dosen untuk hari berjalan.
        $lastQueue = Queue::query()
            ->where('kode_dosen', $request->dean_id)
            ->whereDate('created_at', $todayJakarta)
            ->max('nomor_antrian');

        $queue = Queue::create([
            'kode_user' => $user->kode,
            'kode_dosen' => $request->dean_id,
            'service_id' => $request->service_id,
            'nomor_antrian' => ($lastQueue ?? 0) + 1,
            'status' => 'menunggu',
        ]);

        try {
            event(new QueueStatusUpdated($request->dean_id, $deanRoomStatus ?? 'open', [
                'event' => 'queue_joined',
                'queue_id' => $queue->id,
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
