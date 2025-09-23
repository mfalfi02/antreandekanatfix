<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Antrean Dekanat - Login</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background: linear-gradient(135deg, #6EE7B7, #3B82F6, #9333EA);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Card hover 3D effect */
        .login-card {
            perspective: 1000px;
        }

        .login-inner {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(15px);
            border-radius: 2rem;
            padding: 3rem 2rem;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            transition: transform 0.4s ease, box-shadow 0.4s ease;
        }

        .login-inner:hover {
            transform: rotateY(10deg) rotateX(5deg) scale(1.05);
            box-shadow: 0 30px 60px rgba(0,0,0,0.5);
        }

        input:focus {
            outline: none;
            border-color: #6366F1;
            box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.3);
        }
    </style>
</head>
<body>
    <div class="login-card w-full max-w-md">
        <div class="login-inner text-center">
            <h1 class="text-3xl font-extrabold text-white mb-2 drop-shadow-lg">✨ Sistem Antrean Dekanat</h1>
            <p class="text-white/80 mb-8">Universitas Widya Dharma Pontianak</p>

            <form action="{{ route('login.submit') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Identifier -->
                <div class="relative">
                    <label for="identifier" class="sr-only">Email / NIM / Kode Dosen</label>
                    <input type="text" name="identifier" id="identifier" placeholder="Email / NIM / Kode Dosen" class="w-full p-4 rounded-xl bg-white/30 placeholder-white/70 text-white font-semibold border border-white/30 focus:border-white focus:bg-white/40 transition">
                    <i class="fa-solid fa-user absolute right-4 top-4 text-white/80"></i>
                </div>

                <!-- Password -->
                <div class="relative">
                    <label for="password" class="sr-only">Password</label>
                    <input type="password" name="password" id="password" placeholder="Password" class="w-full p-4 rounded-xl bg-white/30 placeholder-white/70 text-white font-semibold border border-white/30 focus:border-white focus:bg-white/40 transition">
                    <i class="fa-solid fa-lock absolute right-4 top-4 text-white/80"></i>
                </div>

                <!-- Error -->
                @if($errors->any())
                    <div class="text-red-400 text-sm font-medium">{{ $errors->first() }}</div>
                @endif

                <!-- Submit -->
                <button type="submit" class="w-full py-3 bg-gradient-to-r from-purple-500 via-pink-500 to-indigo-500 text-white font-bold rounded-2xl shadow-lg hover:scale-105 transform transition-all duration-300">
                    Masuk
                </button>

                <!-- Back Button -->
                    <a href="{{ url('/') }}" 
                    class="block w-full mt-3 py-3 bg-white/30 text-white font-semibold rounded-2xl shadow hover:bg-white/40 hover:scale-105 transform transition-all duration-300 text-center">
                        ← Kembali
                    </a>
            </form>

            <p class="text-white/70 mt-6 text-sm">Lupa password? <a href="#" class="text-white underline">Klik di sini</a></p>
        </div>
    </div>
</body>
</html>
