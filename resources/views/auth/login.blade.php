<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Antrean Dekanat - Login</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background: linear-gradient(135deg, #8B5CF6, #A78BFA, #E9D5FF);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', sans-serif;
        }

        .login-card {
            perspective: 1000px;
        }

        .login-inner {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(15px);
            border-radius: 2rem;
            padding: 3rem 2rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            text-align: center;
        }

        .login-inner:hover {
            transform: rotateY(10deg) rotateX(5deg) scale(1.05);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5);
        }

        input:focus {
            outline: none;
            border-color: #7C3AED;
            box-shadow: 0 0 0 2px rgba(124, 58, 237, 0.3);
            background: rgba(255, 255, 255, 0.4);
        }

        .placeholder-white {
            color: rgba(255, 255, 255, 0.7);
        }

        .btn-gradient {
            background: linear-gradient(to right, #7C3AED, #EC4899, #6366F1);
            color: #fff;
        }

        .btn-gradient:hover {
            transform: scale(1.05);
        }

        .btn-back {
            background: rgba(255, 255, 255, 0.3);
            color: #fff;
        }

        .btn-back:hover {
            background: rgba(255, 255, 255, 0.4);
            transform: scale(1.05);
        }
    </style>
</head>

<body>
    <div class="login-card w-full max-w-md">
        <div class="login-inner">

            {{-- Judul --}}
            <h1 class="text-3xl font-extrabold text-white mb-2 drop-shadow-lg">Sistem Antrean Dekanat</h1>
            <p class="text-white/80 mb-8">Universitas Widya Dharma Pontianak</p>

            {{-- Form Login --}}
            <form action="{{ route('login.submit') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Kode -->
                <div class="relative">
                    <input type="text" name="kode" id="kode" value="{{ old('kode') }}"
                        placeholder="Masukkan Kode Anda" required autocomplete="off"
                        class="w-full p-4 rounded-xl bg-white/30 placeholder-white/70 text-white font-semibold 
                        border border-white/30 focus:border-white focus:bg-white/40 transition outline-none pr-12">
                    <i class="fa-solid fa-id-card absolute right-4 top-4 text-white/80"></i>
                </div>

                <!-- Password dengan Show/Hide -->
                <div class="relative">
                    <input type="password" name="password" id="password" placeholder="Masukkan Password" required
                        autocomplete="off"
                        class="w-full p-4 rounded-xl bg-white/30 placeholder-white/70 text-white font-semibold 
                        border border-white/30 focus:border-white focus:bg-white/40 transition outline-none pr-12">
                    <i class="fa-solid fa-lock absolute right-12 top-4 text-white/80"></i>
                    <button type="button" id="togglePassword"
                        class="absolute right-4 top-4 text-white/80 focus:outline-none">
                        <i class="fa-solid fa-eye" id="eyeIcon"></i>
                    </button>
                </div>

                <!-- Error Messages -->
                @if (session('error'))
                    <div class="text-red-400 text-sm font-medium text-center">
                        {{ session('error') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="text-red-400 text-sm font-medium text-center">
                        {{ $errors->first() }}
                    </div>
                @endif

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full py-3 btn-gradient font-bold rounded-2xl shadow-lg transform transition-all duration-300 focus:outline-none">
                    Masuk
                </button>

                <!-- Back Button -->
                <a href="{{ url('/') }}"
                    class="block w-full mt-3 py-3 btn-back font-semibold rounded-2xl shadow transform transition-all duration-300 text-center">
                    ← Kembali
                </a>
            </form>

            <p class="text-white/70 mt-6 text-sm">
                Lupa password? <a href="#" class="text-white underline">Klik di sini</a>
            </p>

        </div>
    </div>

    {{-- Script Show/Hide Password --}}
    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        togglePassword.addEventListener('click', function () {
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
    </script>
</body>

</html>
