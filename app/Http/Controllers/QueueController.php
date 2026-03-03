<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\QueueStatusUpdated;
use App\Models\RuangAntri;
use App\Models\User;
use App\Models\Service;
use App\Models\Queue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

class QueueController extends Controller
{
    public function toggleQueue(Request $request)
    {
        $request->merge([
            'expected_jam_buka' => $this->normalizeIndoTime($request->input('expected_jam_buka')),
            'expected_jam_tutup' => $this->normalizeIndoTime($request->input('expected_jam_tutup')),
        ]);

        $request->validate([
            'status' => 'required|in:open,closed,occupied',
            'service_id' => 'nullable|exists:services,id',
            'service_scope' => 'nullable|in:all,single',
            'expected_jam_buka' => 'nullable|date_format:H:i',
            'expected_jam_tutup' => 'nullable|date_format:H:i',
        ]);

        $user = Auth::user();
        if (!$this->isPejabat($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $status = $request->status;
        $serviceId = $request->input('service_id');
        $serviceScope = $request->input('service_scope', 'single');
        $expectedJamTutupInput = $request->input('expected_jam_tutup');
        $now = Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();
        $nowTime = $now->format('H:i:s');

        if (in_array($status, ['open', 'occupied'], true) && $serviceScope !== 'all' && !$serviceId) {
            return response()->json([
                'success' => false,
                'message' => 'Jenis layanan harus dipilih atau pilih Semua Jenis Layanan.',
            ], 422);
        }

        if (in_array($status, ['open', 'occupied'], true) && !$expectedJamTutupInput) {
            return response()->json([
                'success' => false,
                'message' => 'Perkiraan jam tutup wajib diisi sebelum membuka antrean.',
            ], 422);
        }

        $ruang = null;
        if ($status === 'closed') {
            // Saat menutup, prioritaskan menutup sesi aktif (jam_tutup masih null)
            $ruang = RuangAntri::query()
                ->where('kode_dosen', $user->kode)
                ->whereNull('jam_tutup_ruang_antri')
                ->latest('updated_at')
                ->first();
        }

        if (!$ruang) {
            $ruang = RuangAntri::firstOrNew([
                'kode_dosen' => $user->kode,
                'tanggal_buka_ruang_antri' => $today,
            ]);
        }

        $ruang->status_ruang = $status;
        if (in_array($status, ['open', 'occupied'], true)) {
            $ruang->service_id = $serviceScope === 'all' ? null : ($serviceId ?: null);
            if (!$ruang->jam_buka_ruang_antri) {
                $ruang->jam_buka_ruang_antri = $nowTime;
            }

            $ruang->expected_jam_buka_ruang_antri = $ruang->jam_buka_ruang_antri;

            $openTime = Carbon::createFromFormat('H:i:s', $ruang->jam_buka_ruang_antri);
            $closeTime = Carbon::createFromFormat('H:i', $expectedJamTutupInput);
            if ($closeTime->lessThanOrEqualTo($openTime)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Perkiraan jam tutup harus lebih besar dari jam buka.',
                ], 422);
            }

            $ruang->expected_jam_tutup_ruang_antri = $closeTime->format('H:i:s');
            $ruang->jam_tutup_ruang_antri = null;
        }

        if ($status === 'closed') {
            if (!$ruang->jam_buka_ruang_antri) {
                $ruang->jam_buka_ruang_antri = $nowTime;
            }
            if (!$ruang->expected_jam_buka_ruang_antri) {
                $ruang->expected_jam_buka_ruang_antri = $ruang->jam_buka_ruang_antri;
            }
            if (!$ruang->expected_jam_tutup_ruang_antri) {
                $ruang->expected_jam_tutup_ruang_antri = $nowTime;
            }
            $ruang->jam_tutup_ruang_antri = $nowTime;
        }

        $ruang->save();

        try {
            event(new QueueStatusUpdated($user->kode, $status, [
                'event' => 'room_status_toggled',
            ]));
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json([
            'success' => true,
            'kode_dosen' => $user->kode,
            'queue_status' => $status,
            'service' => $this->formatServicePayload(
                $status,
                $ruang->service_id ? Service::query()->find($ruang->service_id, ['id', 'nama_layanan']) : null
            ),
            'waktu' => $this->formatWaktuPayload($ruang),
            'queue_status_label' => match ($status) {
                'open' => 'Antrean Dibuka',
                'occupied' => 'Melayani',
                default => 'Antrean Ditutup',
            },
            'updated_at' => optional($ruang->updated_at)->toDateTimeString(),
        ]);
    }

    public function status()
    {
        $kode = request()->query('kode');
        if (!$kode && Auth::check() && $this->isPejabat(Auth::user())) {
            $kode = Auth::user()->kode;
        }

        if ($kode) {
            $detail = $this->statusDetailForUser($kode);

            return response()->json([
                'success' => true,
                'kode_dosen' => $kode,
                'queue_status' => $detail['queue_status'],
                'queue_status_label' => $this->labelForStatus($detail['queue_status']),
                'service' => $detail['service'],
                'waktu' => $detail['waktu'],
            ]);
        }

        $statuses = $this->statusesForPejabat();
        $globalStatus = $this->globalStatusFromCollection(collect($statuses)->pluck('queue_status'));

        return response()->json([
            'success' => true,
            'queue_status' => $globalStatus,
            'queue_status_label' => $this->labelForStatus($globalStatus),
            'per_user_statuses' => $statuses,
        ]);
    }

    public function callQueue(Queue $queue)
    {
        $user = Auth::user();
        if (!$this->isPejabat($user)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($queue->kode_dosen !== $user->kode) {
            return response()->json([
                'success' => false,
                'message' => 'Antrean ini bukan antrean Anda.',
            ], 403);
        }

        if ($queue->status !== 'menunggu') {
            return response()->json([
                'success' => false,
                'message' => 'Antrean ini tidak bisa dipanggil karena statusnya bukan menunggu.',
            ], 422);
        }

        $queue->loadMissing(['service:id,nama_layanan', 'user:kode,name']);
        $queue->update(['status' => 'diproses']);

        $today = Carbon::now('Asia/Jakarta')->toDateString();
        $ruang = RuangAntri::query()
            ->where('kode_dosen', $user->kode)
            ->whereDate('tanggal_buka_ruang_antri', $today)
            ->latest('updated_at')
            ->first();

        if ($ruang) {
            $ruang->status_ruang = 'occupied';
            $ruang->save();
        }

        try {
            event(new QueueStatusUpdated($user->kode, 'occupied', [
                'event' => 'queue_called',
                'queue_id' => $queue->id,
                'nomor_antrian' => $queue->nomor_antrian,
                'kode_user' => $queue->kode_user,
                'mahasiswa_name' => $queue->user?->name,
                'service_name' => $queue->service?->nama_layanan,
                'dosen_name' => $user->name,
            ]));
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json([
            'success' => true,
            'queue' => $queue->load(['user', 'service']),
            'queue_status' => 'occupied',
        ]);
    }

    public function completeQueue(Queue $queue)
    {
        $user = Auth::user();
        if (!$this->isPejabat($user)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        if ($queue->kode_dosen !== $user->kode) {
            return response()->json([
                'success' => false,
                'message' => 'Antrean ini bukan antrean Anda.',
            ], 403);
        }

        if (!in_array($queue->status, ['diproses', 'menunggu'], true)) {
            return response()->json([
                'success' => false,
                'message' => 'Antrean ini tidak bisa diselesaikan.',
            ], 422);
        }

        $queue->update(['status' => 'selesai']);

        $today = Carbon::now('Asia/Jakarta')->toDateString();
        $ruang = RuangAntri::query()
            ->where('kode_dosen', $user->kode)
            ->whereDate('tanggal_buka_ruang_antri', $today)
            ->latest('updated_at')
            ->first();

        $hasInProgress = Queue::query()
            ->where('kode_dosen', $user->kode)
            ->whereDate('created_at', $today)
            ->where('status', 'diproses')
            ->exists();
        $nextRoomStatus = $hasInProgress ? 'occupied' : 'open';

        if ($ruang && $ruang->status_ruang !== 'closed') {
            $ruang->status_ruang = $nextRoomStatus;
            $ruang->save();
        }

        try {
            event(new QueueStatusUpdated($user->kode, $nextRoomStatus, [
                'event' => 'queue_completed',
                'queue_id' => $queue->id,
            ]));
        } catch (\Throwable $e) {
            report($e);
        }

        return response()->json([
            'success' => true,
            'queue' => $queue->load(['user', 'service']),
            'queue_status' => $nextRoomStatus,
        ]);
    }

    public function myQueues()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $today = Carbon::now('Asia/Jakarta')->toDateString();

        if ($user->role === 'pejabat') {
            $queues = Queue::query()
                ->with(['user', 'service'])
                ->where('kode_dosen', $user->kode)
                ->whereDate('created_at', $today)
                ->latest('created_at')
                ->get();
            $historyQueues = Queue::query()
                ->with(['user', 'service'])
                ->where('kode_dosen', $user->kode)
                ->whereDate('created_at', '<', $today)
                ->latest('created_at')
                ->get();

            $currentServing = $queues->firstWhere('status', 'diproses');

            return response()->json([
                'success' => true,
                'role' => 'pejabat',
                'queues' => $queues,
                'history_queues' => $historyQueues,
                'stats' => [
                    'active' => $queues->whereIn('status', ['menunggu', 'diproses'])->count(),
                    'completed' => $queues->where('status', 'selesai')->count(),
                    'current_queue_number' => $currentServing?->nomor_antrian,
                    'current_service_estimate' => $currentServing?->service?->est,
                ],
            ]);
        }

        if (in_array($user->role, ['mahasiswa', 'dosen'], true)) {
            $queues = Queue::query()
                ->with(['service', 'dosen'])
                ->where('kode_user', $user->kode)
                ->whereDate('created_at', $today)
                ->latest('created_at')
                ->get();
            $historyQueues = Queue::query()
                ->with(['service', 'dosen'])
                ->where('kode_user', $user->kode)
                ->whereDate('created_at', '<', $today)
                ->latest('created_at')
                ->get();

            // Estimasi tunggu dihitung dari total estimasi service pengantre aktif sebelumnya (per dosen, hari ini).
            $activeByDosen = Queue::query()
                ->with('service:id,est')
                ->whereDate('created_at', $today)
                ->whereIn('status', ['menunggu', 'diproses'])
                ->orderBy('created_at', 'asc')
                ->get()
                ->groupBy('kode_dosen');

            $waitMap = [];
            foreach ($activeByDosen as $kodeDosen => $rows) {
                $acc = 0;
                foreach ($rows as $row) {
                    $isInProgress = $row->status === 'diproses';
                    $waitMap[$row->id] = $isInProgress ? 0 : $acc;
                    $acc += (int) ($row->service?->est ?? 0);
                }
            }

            $queues = $queues->map(function (Queue $queue) use ($waitMap) {
                $queue->estimated_wait_minutes = (int) ($waitMap[$queue->id] ?? 0);
                return $queue;
            })->values();

            return response()->json([
                'success' => true,
                'role' => $user->role,
                'queues' => $queues,
                'history_queues' => $historyQueues,
            ]);
        }

        return response()->json([
            'success' => true,
            'role' => $user->role,
            'queues' => [],
        ]);
    }

    private function isPejabat(?User $user): bool
    {
        return $user && $user->role === 'pejabat';
    }

    private function statusDetailForUser(string $kode): array
    {
        $record = RuangAntri::query()
            ->with('service:id,nama_layanan')
            ->where('kode_dosen', $kode)
            ->whereDate('tanggal_buka_ruang_antri', Carbon::now('Asia/Jakarta')->toDateString())
            ->latest('updated_at')
            ->first();

        return [
            'queue_status' => $record?->status_ruang ?? 'closed',
            'service' => $this->formatServicePayload($record?->status_ruang ?? 'closed', $record?->service),
            'waktu' => $record ? $this->formatWaktuPayload($record) : null,
        ];
    }

    private function statusesForPejabat(): array
    {
        $users = User::query()
            ->where('role', 'pejabat')
            ->where('status', 'aktif')
            ->get(['kode', 'name', 'role']);

        return $users->map(function (User $user) {
            $detail = $this->statusDetailForUser($user->kode);
            $status = $detail['queue_status'];
            return [
                'kode' => $user->kode,
                'name' => $user->name,
                'role' => $user->role,
                'queue_status' => $status,
                'queue_status_label' => $this->labelForStatus($status),
                'service' => $detail['service'],
                'waktu' => $detail['waktu'],
            ];
        })->values()->all();
    }

    private function globalStatusFromCollection($statuses): string
    {
        if ($statuses->contains('occupied')) {
            return 'occupied';
        }

        if ($statuses->contains('open')) {
            return 'open';
        }

        return 'closed';
    }

    private function labelForStatus(string $status): string
    {
        return match ($status) {
            'open' => 'Antrean Dibuka',
            'occupied' => 'Melayani',
            default => 'Antrean Ditutup',
        };
    }

    private function formatServicePayload(string $status, ?Service $service): ?array
    {
        if ($service) {
            return [
                'id' => $service->id,
                'nama_layanan' => $service->nama_layanan,
            ];
        }

        if (in_array($status, ['open', 'occupied'], true)) {
            return [
                'id' => null,
                'nama_layanan' => 'Semua Jenis Layanan',
            ];
        }

        return null;
    }

    private function formatWaktuPayload(RuangAntri $ruang): array
    {
        return [
            'tanggal' => $ruang->tanggal_buka_ruang_antri,
            'expected_jam_buka' => $ruang->expected_jam_buka_ruang_antri,
            'expected_jam_tutup' => $ruang->expected_jam_tutup_ruang_antri,
            'jam_buka' => $ruang->jam_buka_ruang_antri,
            'jam_tutup' => $ruang->jam_tutup_ruang_antri,
        ];
    }

    private function normalizeIndoTime(?string $value): ?string
    {
        if (!$value) {
            return null;
        }

        $time = trim($value);
        $time = str_ireplace(['WIB', 'wib', ' '], '', $time);
        $time = str_replace('.', ':', $time);

        if (preg_match('/^\d{2}:\d{2}$/', $time) === 1) {
            return $time;
        }

        return $value;
    }
}
