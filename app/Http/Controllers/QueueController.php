<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\QueueStatusUpdated;
use App\Models\RuangAntri;
use App\Models\User;
use App\Models\Service;
use App\Models\Queue;
use App\Services\GeofenceService;
use App\Services\QueueDashboardService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

/**
 * Menangani siklus antrean pejabat dan mengarahkan hasilnya ke JSON respons, event realtime, atau state ruang yang tersimpan.
 */
class QueueController extends Controller
{
    /**
     * Mengubah status ruang antrean pejabat lalu mengembalikan JSON untuk dashboard, display, dan listener realtime.
     */
    public function toggleQueue(Request $request)
    {
        $request->merge([
            'expected_jam_buka' => $this->normalizeIndoTime($request->input('expected_jam_buka')),
            'expected_jam_tutup' => $this->normalizeIndoTime($request->input('expected_jam_tutup')),
        ]);

        $request->validate([
            'status' => 'required|in:open,closed,occupied',
            'service_id' => 'nullable|exists:services,id',
            'service_ids' => 'nullable|array',
            'service_ids.*' => 'integer|exists:services,id',
            'service_scope' => 'nullable|in:all,single,multiple',
            'expected_jam_buka' => 'nullable|date_format:H:i',
            'expected_jam_tutup' => 'nullable|date_format:H:i',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'accuracy' => 'nullable|numeric|min:0',
        ]);

        $user = Auth::user();
        if (!$this->isPejabat($user)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $status = $request->status;
        if (in_array($status, ['open', 'occupied'], true)) {
            if ($response = app(GeofenceService::class)->validateRequest($request, 'membuka antrean')) {
                return $response;
            }
        }
        $serviceScope = $request->input('service_scope', 'single');
        $selectedServiceIds = $this->resolveSelectedServiceIds($request, $serviceScope);
        if ($serviceScope !== 'all') {
            $serviceScope = count($selectedServiceIds) > 1 ? 'multiple' : 'single';
        }
        $expectedJamTutupInput = $request->input('expected_jam_tutup');
        $now = Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();
        $nowTime = $now->format('H:i:s');

        if (in_array($status, ['open', 'occupied'], true) && $serviceScope !== 'all' && count($selectedServiceIds) === 0) {
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
            if ($serviceScope === 'all') {
                $ruang->service_id = null;
                $ruang->service_ids = null;
            } else {
                $ruang->service_ids = $selectedServiceIds;
                $ruang->service_id = count($selectedServiceIds) === 1 ? $selectedServiceIds[0] : null;
            }
            if (!$ruang->jam_buka_ruang_antri) {
                $ruang->jam_buka_ruang_antri = $nowTime;
            }

            $ruang->expected_jam_buka_ruang_antri = $ruang->jam_buka_ruang_antri;

            $closeTime = Carbon::createFromFormat('H:i', $expectedJamTutupInput);
            $currentTime = Carbon::createFromFormat('H:i:s', $nowTime);
            if ($closeTime->lessThanOrEqualTo($currentTime)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Perkiraan jam tutup harus lebih besar dari jam sekarang.',
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
            'service' => $this->formatServicePayload($status, $this->resolveRoomServices($ruang)),
            'waktu' => $this->formatWaktuPayload($ruang),
            'queue_status_label' => match ($status) {
                'open' => 'Antrean Dibuka',
                'occupied' => 'Melayani',
                default => 'Antrean Ditutup',
            },
            'updated_at' => optional($ruang->updated_at)->toDateTimeString(),
        ]);
    }

    /**
     * Mengembalikan JSON status satu pejabat atau ringkasan semua pejabat aktif untuk dipakai dashboard dan polling client.
     */
    public function status(Request $request)
    {
        $kode = $request->query('kode');
        if (!$kode && Auth::check() && $this->isPejabat(Auth::user())) {
            $kode = Auth::user()->kode;
        }

        $period = $this->resolveStatusPeriod($request);

        if ($kode) {
            $detail = $this->statusDetailForUser($kode, $period);

            return response()->json([
                'success' => true,
                'kode_dosen' => $kode,
                'queue_status' => $detail['queue_status'],
                'queue_status_label' => $this->labelForStatus($detail['queue_status']),
                'service' => $detail['service'],
                'waktu' => $detail['waktu'],
            ]);
        }

        $statuses = $this->statusesForPejabat($period);
        $globalStatus = $this->globalStatusFromCollection(collect($statuses)->pluck('queue_status'));

        return response()->json([
            'success' => true,
            'queue_status' => $globalStatus,
            'queue_status_label' => $this->labelForStatus($globalStatus),
            'per_user_statuses' => $statuses,
            'period' => $period ? [
                'month' => (int) $period['month'],
                'year' => (int) $period['year'],
                'label' => $period['label'],
            ] : null,
        ]);
    }

    /**
     * Memproses panggilan antrean milik pejabat yang sedang login lalu mengirim JSON dan event ke UI lain.
     */
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

        $nextRoomStatus = 'closed';
        if ($ruang) {
            if ($ruang->status_ruang !== 'closed') {
                $ruang->status_ruang = 'occupied';
                $ruang->save();
                $nextRoomStatus = 'occupied';
            }
        }

        try {
            event(new QueueStatusUpdated($user->kode, $nextRoomStatus, [
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
            'queue_status' => $nextRoomStatus,
        ]);
    }

    /**
     * Menandai antrean selesai, memperbarui status ruang, lalu mengirim hasilnya ke dashboard dan listener realtime.
     */
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

        if ($ruang) {
            if ($ruang->status_ruang !== 'closed') {
                $ruang->status_ruang = $nextRoomStatus;
                $ruang->save();
            } else {
                $nextRoomStatus = 'closed';
            }
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

    /**
     * Menarik daftar antrean milik user yang login dan mengarahkannya ke dashboard role masing-masing dalam bentuk JSON.
     */
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
            $dashboardService = app(QueueDashboardService::class);
            $payload = $dashboardService->pejabatDashboardPayload($user, $today);
            $queues = $payload['queues'];
            $historyQueues = $payload['history_queues'];

            return response()->json([
                'success' => true,
                'role' => 'pejabat',
                'queues' => $queues,
                'priority_queues' => $payload['priority_queues'],
                'history_queues' => $historyQueues,
                'stats' => $payload['stats'],
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

    /**
     * Menentukan apakah user login boleh masuk ke alur pejabat dan membuka aksi ruang antrean.
     */
    private function isPejabat(?User $user): bool
    {
        return $user && $user->role === 'pejabat';
    }

    /**
     * Mengambil detail status ruang pejabat untuk dipakai sebagai sumber data JSON pada dashboard dan API status.
     */
    private function statusDetailForUser(string $kode, ?array $period = null): array
    {
        $query = RuangAntri::query()
            ->where('kode_dosen', $kode)
            ->latest('updated_at');

        if ($period) {
            $query->whereBetween('tanggal_buka_ruang_antri', [
                $period['start']->toDateString(),
                $period['end']->toDateString(),
            ]);
        } else {
            $query->whereDate('tanggal_buka_ruang_antri', Carbon::now('Asia/Jakarta')->toDateString());
        }

        $record = $query->first();

        $services = $this->resolveRoomServices($record);

        return [
            'queue_status' => $record?->status_ruang ?? 'closed',
            'service' => $this->formatServicePayload($record?->status_ruang ?? 'closed', $services),
            'waktu' => $record ? $this->formatWaktuPayload($record) : null,
        ];
    }

    /**
     * Mengubah koleksi status pejabat menjadi array JSON yang langsung bisa dirender frontend.
     */
    private function statusesForPejabat(?array $period = null): array
    {
        $users = User::query()
            ->where('role', 'pejabat')
            ->where('status', 'aktif')
            ->get(['kode', 'name', 'role']);

        return $users->map(function (User $user) use ($period) {
            $detail = $this->statusDetailForUser($user->kode, $period);
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

    /**
     * Memvalidasi periode dari query string agar report dan status hanya mengarah ke bulan/tahun yang sah.
     */
    private function resolveStatusPeriod(Request $request): ?array
    {
        $month = (int) $request->query('month', 0);
        $year = (int) $request->query('year', 0);

        if ($month < 1 || $month > 12 || $year < 2000 || $year > 2100) {
            return null;
        }

        $start = Carbon::create($year, $month, 1, 0, 0, 0, 'Asia/Jakarta')->startOfMonth();
        $end = (clone $start)->endOfMonth();

        return [
            'month' => $month,
            'year' => $year,
            'start' => $start,
            'end' => $end,
            'label' => $this->monthLabel($month) . ' ' . $year,
        ];
    }

    /**
     * Mengubah angka bulan menjadi label bahasa Indonesia untuk heading report dan response period.
     */
    private function monthLabel(int $month): string
    {
        return match ($month) {
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
            default => '-',
        };
    }

    /**
     * Menentukan status global untuk dashboard gabungan sebelum dikirim ke UI utama.
     */
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

    /**
     * Mengubah status teknis menjadi label UI agar output JSON lebih mudah dibaca user.
     */
    private function labelForStatus(string $status): string
    {
        return match ($status) {
            'open' => 'Antrean Dibuka',
            'occupied' => 'Melayani',
            default => 'Antrean Ditutup',
        };
    }

    /**
     * Membentuk payload layanan yang dikirim ke frontend saat status ruang berubah.
     */
    private function formatServicePayload(string $status, $services): ?array
    {
        if ($services->count() > 0) {
            $ids = $services->pluck('id')->values()->all();
            return [
                'id' => count($ids) === 1 ? $ids[0] : null,
                'ids' => $ids,
                'nama_layanan' => $services->pluck('nama_layanan')->implode(', '),
            ];
        }

        if (in_array($status, ['open', 'occupied'], true)) {
            return [
                'id' => null,
                'ids' => [],
                'nama_layanan' => 'Semua Jenis Layanan',
            ];
        }

        return null;
    }

    /**
     * Mengambil daftar layanan yang dipilih dari request sebelum data ruang disimpan atau dipublish.
     */
    private function resolveSelectedServiceIds(Request $request, string $serviceScope): array
    {
        if ($serviceScope === 'all') {
            return [];
        }

        $serviceIds = $request->input('service_ids', []);
        if (!is_array($serviceIds)) {
            $serviceIds = [];
        }

        $serviceIds = array_values(array_unique(array_map(
            'intval',
            array_filter($serviceIds, fn ($id) => $id !== null && $id !== '')
        )));

        if (count($serviceIds) === 0 && $request->filled('service_id')) {
            $serviceIds = [(int) $request->input('service_id')];
        }

        return $serviceIds;
    }

    /**
     * Mengambil layanan aktif pada ruang antrean supaya payload status mengarah ke nama layanan yang benar.
     */
    private function resolveRoomServices(?RuangAntri $ruang)
    {
        if (!$ruang) {
            return collect();
        }

        $ids = is_array($ruang->service_ids) ? $ruang->service_ids : [];
        $ids = array_values(array_unique(array_map('intval', array_filter($ids, fn ($id) => $id !== null && $id !== ''))));

        if (count($ids) === 0 && $ruang->service_id) {
            $ids = [(int) $ruang->service_id];
        }

        if (count($ids) === 0) {
            return collect();
        }

        $servicesById = Service::query()
            ->whereIn('id', $ids)
            ->get(['id', 'nama_layanan'])
            ->keyBy('id');

        return collect($ids)
            ->map(fn (int $id) => $servicesById->get($id))
            ->filter()
            ->values();
    }

    /**
     * Menyusun payload waktu yang dikirim ke frontend, display, dan sinkronisasi dashboard.
     */
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

    /**
     * Menormalkan input waktu Indonesia agar valid sebelum dipakai menyimpan status ruang.
     */
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
