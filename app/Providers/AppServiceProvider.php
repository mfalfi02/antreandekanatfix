<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Mendaftarkan layanan aplikasi jika nanti ada binding atau singleton yang perlu dipasang.
     */
    public function register(): void
    {
    }

    /**
     * Menyiapkan aplikasi saat boot agar tempat inisialisasi global tetap terpusat.
     */
    public function boot(): void
    {
    }
}
