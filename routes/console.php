<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Queue;
use App\Models\RuangAntri;
use Illuminate\Support\Carbon;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('queues:backfill-kode-dosen {--date=} {--dry-run}', function () {
    $dateFilter = $this->option('date');
    $dryRun = (bool) $this->option('dry-run');

    if ($dateFilter) {
        try {
            Carbon::createFromFormat('Y-m-d', $dateFilter);
        } catch (\Throwable $e) {
            $this->error('Format --date harus YYYY-MM-DD.');
            return self::FAILURE;
        }
    }

    $query = Queue::query()
        ->whereNull('kode_dosen')
        ->orderBy('id');

    if ($dateFilter) {
        $query->whereDate('created_at', $dateFilter);
    }

    $total = (clone $query)->count();
    if ($total === 0) {
        $this->info('Tidak ada data queues yang perlu di-backfill.');
        return self::SUCCESS;
    }

    $this->info('Memproses ' . $total . ' queue(s)...');
    if ($dryRun) {
        $this->comment('Mode dry-run aktif, tidak ada perubahan database.');
    }

    $updated = 0;
    $skipped = 0;
    $noMatch = 0;

    $query->chunkById(200, function ($queues) use (&$updated, &$skipped, &$noMatch, $dryRun) {
        foreach ($queues as $queue) {
            $createdAt = optional($queue->created_at)?->setTimezone('Asia/Jakarta');
            if (!$createdAt || !$queue->service_id) {
                $skipped++;
                continue;
            }

            $queueDate = $createdAt->toDateString();
            $queueTime = $createdAt->format('H:i:s');

            $sessions = RuangAntri::query()
                ->whereDate('tanggal_buka_ruang_antri', $queueDate)
                ->where(function ($q) use ($queue) {
                    $q->where('service_id', $queue->service_id)
                        ->orWhereNull('service_id');
                })
                ->get();

            if ($sessions->isEmpty()) {
                $noMatch++;
                continue;
            }

            $match = $sessions
                ->filter(function ($session) use ($queueTime) {
                    $open = $session->jam_buka_ruang_antri ?? $session->expected_jam_buka_ruang_antri;
                    $close = $session->jam_tutup_ruang_antri ?? $session->expected_jam_tutup_ruang_antri;

                    if (!$open || $open > $queueTime) {
                        return false;
                    }

                    if ($close && $close < $queueTime) {
                        return false;
                    }

                    return true;
                })
                ->sortBy([
                    fn ($session) => $session->service_id === $queue->service_id ? 0 : 1,
                    ['jam_buka_ruang_antri', 'desc'],
                    ['updated_at', 'desc'],
                ])
                ->first();

            if (!$match) {
                $fallback = $sessions
                    ->where('service_id', $queue->service_id)
                    ->sortByDesc('updated_at')
                    ->first();
                $match = $fallback ?: $sessions->sortByDesc('updated_at')->first();
            }

            if (!$match || !$match->kode_dosen) {
                $noMatch++;
                continue;
            }

            if (!$dryRun) {
                $queue->kode_dosen = $match->kode_dosen;
                $queue->save();
            }
            $updated++;
        }
    });

    $this->newLine();
    $this->info('Backfill selesai.');
    $this->line('Updated : ' . $updated);
    $this->line('No Match: ' . $noMatch);
    $this->line('Skipped : ' . $skipped);

    return self::SUCCESS;
})->purpose('Backfill queues.kode_dosen untuk data historis berdasarkan ruang antrean');
