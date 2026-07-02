<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Sistem Antrean Dekanat</title>
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
            background: rgba(255, 255, 255, .16);
            border: 1px solid rgba(255, 255, 255, .32);
            box-shadow: 0 24px 45px -30px rgba(15, 23, 42, .65);
            backdrop-filter: blur(14px);
            border-radius: 1.35rem;
        }

        .soft-input {
            width: 100%;
            border: 1px solid rgba(255, 255, 255, .42);
            border-radius: .8rem;
            background: rgba(255, 255, 255, .2);
            padding: .75rem .95rem .75rem 2.55rem;
            color: #ffffff;
            font-weight: 500;
            transition: border-color .2s ease, box-shadow .2s ease, background-color .2s ease;
        }

        .soft-input::placeholder {
            color: rgba(255, 255, 255, .7);
        }

        .soft-input:focus {
            outline: none;
            border-color: rgba(191, 219, 254, .95);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, .2);
            background: rgba(255, 255, 255, .26);
        }
    </style>
</head>

<body class="p-4 md:p-8">
    <div class="min-h-[calc(100vh-2rem)] grid place-items-center">
        <div class="w-full max-w-md glass-panel p-6 md:p-8 text-white">
            <div class="text-center mb-6">
                <img src="{{ asset('images/logosistem.jpeg') }}" alt="Logo Sistem"
                    class="mx-auto mb-3 w-16 h-16 object-contain">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-100">Reset Password</p>
                <h1 class="mt-2 text-2xl md:text-3xl font-extrabold">Lupa Password</h1>
                <p class="mt-1 text-sm text-cyan-50/90">Masukkan email yang terdaftar untuk menerima link reset.</p>
            </div>

            @if (session('status'))
                <div class="mb-4 rounded-lg border border-emerald-200/70 bg-emerald-100/90 px-3 py-2 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-rose-200/70 bg-rose-100/90 px-3 py-2 text-sm text-rose-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
                @csrf

                <div class="space-y-1.5">
                    <label for="email" class="text-sm font-medium text-cyan-50">Email</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-white/80">
                            <i class="fa-solid fa-envelope"></i>
                        </span>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            placeholder="nama@kampus.ac.id" required autocomplete="email" class="soft-input">
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-2.5 rounded-xl bg-white text-indigo-700 font-bold hover:bg-indigo-50 transition">
                    Kirim Link Reset
                </button>

                <a href="{{ route('login') }}"
                    class="block w-full py-2.5 rounded-xl border border-white/35 bg-white/15 text-white text-center font-semibold hover:bg-white/25 transition">
                    Kembali ke Login
                </a>
            </form>
        </div>
    </div>

    @include('partials.pwa-scripts')
</body>

</html>
