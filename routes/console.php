<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\Queue;
use App\Models\RuangAntri;
use Illuminate\Support\Carbon;

// Command bawaan Laravel untuk menampilkan kutipan inspirasi di CLI.
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Backfill data historis agar queue lama punya kode dosen yang bisa dipakai laporan dan audit.
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

// Menata ulang nomor antrean per dosen agar urutan harian tetap konsisten.
Artisan::command('queues:renumber-per-dosen {--date=} {--dry-run}', function () {
    $dateFilter = $this->option('date') ?: Carbon::now('Asia/Jakarta')->toDateString();
    $dryRun = (bool) $this->option('dry-run');

    try {
        Carbon::createFromFormat('Y-m-d', $dateFilter);
    } catch (\Throwable $e) {
        $this->error('Format --date harus YYYY-MM-DD.');
        return self::FAILURE;
    }

    $baseQuery = Queue::query()
        ->whereDate('created_at', $dateFilter)
        ->whereNotNull('kode_dosen');

    $total = (clone $baseQuery)->count();
    if ($total === 0) {
        $this->info("Tidak ada queue pada tanggal {$dateFilter}.");
        return self::SUCCESS;
    }

    $this->info("Renumber antrean per dosen untuk tanggal {$dateFilter}...");
    if ($dryRun) {
        $this->comment('Mode dry-run aktif, tidak ada perubahan database.');
    }

    $groups = (clone $baseQuery)
        ->select('kode_dosen')
        ->groupBy('kode_dosen')
        ->pluck('kode_dosen');

    $updated = 0;
    $unchanged = 0;

    foreach ($groups as $kodeDosen) {
        $queues = Queue::query()
            ->whereDate('created_at', $dateFilter)
            ->where('kode_dosen', $kodeDosen)
            ->orderBy('created_at')
            ->orderBy('id')
            ->get(['id', 'nomor_antrian']);

        $nextNumber = 1;
        foreach ($queues as $queue) {
            if ((int) $queue->nomor_antrian === $nextNumber) {
                $unchanged++;
                $nextNumber++;
                continue;
            }

            if (!$dryRun) {
                DB::table('queues')
                    ->where('id', $queue->id)
                    ->update([
                        'nomor_antrian' => $nextNumber,
                        'updated_at' => now(),
                    ]);
            }

            $updated++;
            $nextNumber++;
        }
    }

    $this->newLine();
    $this->info('Renumber selesai.');
    $this->line('Updated  : ' . $updated);
    $this->line('Unchanged: ' . $unchanged);
    $this->line('Total    : ' . ($updated + $unchanged));

    return self::SUCCESS;
})->purpose('Renumber queues.nomor_antrian per kode_dosen per hari');

// Membersihkan antrean aktif lama yang sudah lewat tanggal hari ini supaya status tetap valid.
Artisan::command('queues:cleanup-stale-active {--date=} {--kode-user=} {--kode-dosen=} {--dry-run}', function () {
    $dateFilter = $this->option('date') ?: Carbon::now('Asia/Jakarta')->toDateString();
    $kodeUser = trim((string) $this->option('kode-user'));
    $kodeDosen = trim((string) $this->option('kode-dosen'));
    $dryRun = (bool) $this->option('dry-run');

    try {
        Carbon::createFromFormat('Y-m-d', $dateFilter);
    } catch (\Throwable $e) {
        $this->error('Format --date harus YYYY-MM-DD.');
        return self::FAILURE;
    }

    $query = Queue::query()
        ->whereIn('status', ['menunggu', 'diproses'])
        ->whereDate('created_at', '<', $dateFilter)
        ->orderBy('created_at')
        ->orderBy('id');

    if ($kodeUser !== '') {
        $query->where('kode_user', $kodeUser);
    }

    if ($kodeDosen !== '') {
        $query->where('kode_dosen', $kodeDosen);
    }

    $total = (clone $query)->count();
    if ($total === 0) {
        $this->info('Tidak ada antrean aktif lama yang perlu dibersihkan.');
        return self::SUCCESS;
    }

    $this->info("Menemukan {$total} antrean aktif lama yang akan ditutup sebagai batal.");
    if ($dryRun) {
        $this->comment('Mode dry-run aktif, tidak ada perubahan database.');
    }

    $updated = 0;
    $query->chunkById(200, function ($queues) use (&$updated, $dryRun) {
        foreach ($queues as $queue) {
            if (!$dryRun) {
                DB::table('queues')
                    ->where('id', $queue->id)
                    ->update([
                        'status' => 'batal',
                        'updated_at' => now(),
                    ]);
            }
            $updated++;
        }
    });

    $this->newLine();
    $this->info('Cleanup selesai.');
    $this->line('Updated : ' . $updated);
    $this->line('Mode    : ' . ($dryRun ? 'dry-run' : 'apply'));

    return self::SUCCESS;
})->purpose('Menutup antrean aktif lama yang sudah lewat tanggal hari ini');
