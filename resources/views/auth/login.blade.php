<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Antrean Dekanat - Login</title>
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
            -webkit-backdrop-filter: blur(14px);
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

        @media (max-width: 640px) {
            body {
                padding: .75rem;
            }

            .glass-panel {
                border-radius: 1rem;
            }

            .glass-panel,
            .glass-panel * {
                -webkit-transform: translateZ(0);
                transform: translateZ(0);
            }

            .glass-panel img {
                width: 3rem !important;
                height: 3rem !important;
            }

            .glass-panel h1 {
                font-size: 1.4rem;
                line-height: 1.15;
            }

            .glass-panel p {
                word-break: break-word;
            }

            .soft-input {
                padding: .7rem .9rem .7rem 2.4rem;
                font-size: .95rem;
            }
        }
    </style>
</head>

<body class="p-3 md:p-8">
    <div class="min-h-[calc(100vh-1.5rem)] grid place-items-center">
        <div class="w-full max-w-md glass-panel p-5 md:p-8 text-white">
            <div class="text-center mb-4 md:mb-5">
                <img src="{{ asset('images/logosistem.jpeg') }}" alt="Logo Sistem"
                    class="mx-auto mb-3 w-12 h-12 md:w-16 md:h-16 object-contain shrink-0">
                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-cyan-100">Login Sistem</p>
                <h1 class="mt-2 text-xl md:text-3xl font-extrabold">Sistem Antrean Dekanat</h1>
                <p class="mt-1 text-sm text-cyan-50/90">Silahkan Masuk Sesuai Role Anda.</p>
            </div>

            <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
                @csrf

                <div class="space-y-1.5">
                    <label for="kode" class="text-sm font-medium text-cyan-50">Kode Pengguna</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-white/80">
                            <i class="fa-solid fa-id-card"></i>
                        </span>
                        <input type="text" name="kode" id="kode" value="{{ old('kode') }}" placeholder="Contoh: DSN001"
                            required autocomplete="off" class="soft-input">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label for="password" class="text-sm font-medium text-cyan-50">Password</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-white/80">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="password" placeholder="Masukkan password" required
                            autocomplete="off" class="soft-input pr-11">
                        <button type="button" id="togglePassword"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-white/90 hover:text-cyan-100">
                            <i class="fa-solid fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                @if (session('error'))
                    <div class="rounded-lg border border-rose-200/70 bg-rose-100/90 px-3 py-2 text-sm text-rose-700">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="rounded-lg border border-rose-200/70 bg-rose-100/90 px-3 py-2 text-sm text-rose-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <button type="submit"
                    class="w-full py-2.5 rounded-xl bg-white text-indigo-700 font-bold hover:bg-indigo-50 transition text-sm sm:text-base">
                    Masuk
                </button>

                @if (session('success'))
                    <div class="rounded-lg border border-emerald-200/70 bg-emerald-100/90 px-3 py-2 text-sm text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="text-center">
                    <a href="{{ route('password.request') }}"
                        class="text-sm font-semibold text-cyan-100 hover:text-white transition">
                        Lupa password?
                    </a>
                </div>

                <a href="{{ url('/') }}"
                    class="block w-full py-2.5 rounded-xl border border-white/35 bg-white/15 text-white text-center font-semibold hover:bg-white/25 transition text-sm sm:text-base">
                    Kembali
                </a>
            </form>
        </div>
    </div>

    <script>
        // Tombol ini hanya mengubah visibilitas password tanpa mempengaruhi alur login ke backend.
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (togglePassword && passwordInput && eyeIcon) {
            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);

                if (type === 'password') {
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');
                } else {
                    eyeIcon.classList.remove('fa-eye');
                    eyeIcon.classList.add('fa-eye-slash');
                }
            });
        }
    </script>

    @include('partials.pwa-scripts')
</body>

</html>
