<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selamat Datang - Sistem Antrean Dekanat</title>
    @vite('resources/css/app.css')
    @include('partials.pwa-head')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background:
                radial-gradient(circle at 10% -8%, rgba(76, 29, 149, .35), transparent 36%),
                radial-gradient(circle at 90% -10%, rgba(14, 165, 233, .22), transparent 34%),
                linear-gradient(135deg, #1e1b4b 0%, #4338ca 44%, #0ea5e9 100%);
            min-height: 100vh;
        }

        .glass-panel {
            background: rgba(255, 255, 255, .14);
            border: 1px solid rgba(255, 255, 255, .3);
            box-shadow: 0 24px 45px -30px rgba(15, 23, 42, .65);
            backdrop-filter: blur(14px);
            border-radius: 1.4rem;
        }

        .glass-card {
            background: rgba(255, 255, 255, .18);
            border: 1px solid rgba(255, 255, 255, .32);
            border-radius: 1rem;
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
        }

        .glass-card:hover {
            transform: translateY(-3px) scale(1.01);
            border-color: rgba(255, 255, 255, .52);
            box-shadow: 0 20px 36px -26px rgba(15, 23, 42, .75);
        }
    </style>
</head>

<body class="p-4 md:p-8">
    <div class="min-h-[calc(100vh-2rem)] max-w-5xl mx-auto grid place-items-center">
        <div class="glass-panel w-full p-6 md:p-10 text-white">
            <div class="text-center max-w-2xl mx-auto">
                <img src="{{ asset('images/logosistem.jpeg') }}" alt="Logo Sistem"
                    class="mx-auto mb-4 w-20 h-20 md:w-24 md:h-24 object-contain">

                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-100">Sistem Antrean</p>
                <h1 class="mt-2 text-3xl md:text-5xl font-extrabold leading-tight">Dekanat Fakultas Teknologi Informasi</h1>
                <p class="mt-3 text-cyan-50/90">Universitas Widya Dharma Pontianak</p>
                <p class="mt-2 text-sm text-cyan-50/80">Pantau antrean realtime atau masuk ke sistem untuk pengelolaan layanan.</p>
            </div>

            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('display') }}" class="glass-card p-5">
                    <div class="flex items-start gap-3">
                        <span class="h-10 w-10 rounded-full bg-white/30 text-white flex items-center justify-center">
                            <i class="fa-solid fa-desktop"></i>
                        </span>
                        <div>
                            <p class="font-semibold text-white">Layar Display</p>
                            <p class="text-sm text-cyan-50/85">Tampilkan status antrean saat ini.</p>
                        </div>
                    </div>
                    <p class="mt-4 text-sm font-semibold text-white">Buka Display <i class="fa-solid fa-arrow-right ml-1"></i></p>
                </a>

                <a href="{{ route('login') }}" class="glass-card p-5">
                    <div class="flex items-start gap-3">
                        <span class="h-10 w-10 rounded-full bg-white/30 text-white flex items-center justify-center">
                            <i class="fa-solid fa-right-to-bracket"></i>
                        </span>
                        <div>
                            <p class="font-semibold text-white">Login Sistem</p>
                            <p class="text-sm text-cyan-50/85">Masuk ke dashboard sesuai role.</p>
                        </div>
                    </div>
                    <p class="mt-4 text-sm font-semibold text-white">Masuk Sekarang <i class="fa-solid fa-arrow-right ml-1"></i></p>
                </a>
            </div>
        </div>
    </div>

    @include('partials.pwa-scripts')
</body>

</html>
