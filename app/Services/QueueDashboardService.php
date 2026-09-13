<?php

namespace App\Services;

use App\Models\Queue;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Menyusun data antrean yang dipakai dashboard pejabat agar urutan FIFO, estimasi, dan prioritas selalu konsisten.
 */
class QueueDashboardService
{
    private const PRIORITY_WAIT_THRESHOLD = 10;

    /**
     * Mengambil payload lengkap dashboard pejabat untuk hari ini.
     */
    public function pejabatDashboardPayload(User $user, ?string $date = null): array
    {
        $today = $date ?: Carbon::now('Asia/Jakarta')->toDateString();

        $queues = $this->activeQueuesForPejabat($user->kode, $today);
        $historyQueues = $this->historyQueuesForPejabat($user->kode, $today);
        $queuesWithEstimate = $this->applyEstimatedWaitMinutes($queues);

        $currentServing = $queuesWithEstimate->firstWhere('status', 'diproses');
        $priorityQueues = $this->priorityQueues($queuesWithEstimate);

        return [
            'queues' => $queuesWithEstimate->values(),
            'priority_queues' => $priorityQueues->values(),
            'history_queues' => $historyQueues->values(),
            'stats' => [
                'active' => $queuesWithEstimate->whereIn('status', ['menunggu', 'diproses'])->count(),
                'completed' => $queuesWithEstimate->where('status', 'selesai')->count(),
                'current_queue_number' => $currentServing?->nomor_antrian,
                'current_service_estimate' => $currentServing?->service?->est,
            ],
        ];
    }

    /**
     * Mengambil antrean aktif hari ini lalu memastikan urutannya FIFO.
     */
    public function activeQueuesForPejabat(string $kodeDosen, ?string $date = null): Collection
    {
        $targetDate = $date ?: Carbon::now('Asia/Jakarta')->toDateString();

        return Queue::query()
            ->with(['user', 'service'])
            ->where('kode_dosen', $kodeDosen)
            ->whereDate('created_at', $targetDate)
            ->whereIn('status', ['menunggu', 'diproses', 'selesai'])
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * Mengambil riwayat hari sebelumnya untuk dashboard pejabat.
     */
    public function historyQueuesForPejabat(string $kodeDosen, ?string $date = null): Collection
    {
        $targetDate = $date ?: Carbon::now('Asia/Jakarta')->toDateString();

        return Queue::query()
            ->with(['user', 'service'])
            ->where('kode_dosen', $kodeDosen)
            ->whereDate('created_at', '<', $targetDate)
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();
    }

    /**
     * Menghitung estimasi tunggu per antrean berdasarkan urutan masuk dan estimasi layanan sebelumnya.
     */
    public function applyEstimatedWaitMinutes(Collection $queues): Collection
    {
        $activeByDosen = $queues
            ->whereIn('status', ['menunggu', 'diproses'])
            ->groupBy('kode_dosen');

        $waitMap = [];

        foreach ($activeByDosen as $rows) {
            $accumulator = 0;

            foreach ($rows as $row) {
                $isInProgress = $row->status === 'diproses';
                $waitMap[$row->id] = $isInProgress ? 0 : $accumulator;
                $accumulator += (int) ($row->service?->est ?? 0);
            }
        }

        return $queues->map(function (Queue $queue) use ($waitMap) {
            $queue->estimated_wait_minutes = (int) ($waitMap[$queue->id] ?? 0);

            return $queue;
        });
    }

    /**
     * Menyaring antrean prioritas, yaitu milik pengantre dosen atau yang estimasi tunggunya paling pendek.
     */
    public function priorityQueues(Collection $queues): Collection
    {
        $priority = $queues
            ->whereIn('status', ['menunggu', 'diproses'])
            ->filter(function (Queue $queue) {
                $isDosen = ($queue->user?->role ?? null) === 'dosen';
                $isFastQueue = (int) ($queue->estimated_wait_minutes ?? 0) <= self::PRIORITY_WAIT_THRESHOLD;

                return $isDosen || $isFastQueue;
            })
            ->sort(function (Queue $left, Queue $right) {
                $leftScore = ($left->user?->role ?? null) === 'dosen' ? 0 : 1;
                $rightScore = ($right->user?->role ?? null) === 'dosen' ? 0 : 1;

                if ($leftScore !== $rightScore) {
                    return $leftScore <=> $rightScore;
                }

                $waitCompare = ((int) ($left->estimated_wait_minutes ?? 0)) <=> ((int) ($right->estimated_wait_minutes ?? 0));
                if ($waitCompare !== 0) {
                    return $waitCompare;
                }

                $createdCompare = strcmp(
                    (string) optional($left->created_at)->toDateTimeString(),
                    (string) optional($right->created_at)->toDateTimeString()
                );

                if ($createdCompare !== 0) {
                    return $createdCompare;
                }

                return (int) $left->id <=> (int) $right->id;
            });

        return $priority->values();
    }
}
