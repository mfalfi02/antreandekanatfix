{{-- resources/views/auth/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layar Display - Sistem Antrean Dekanat</title>
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

        .dashboard-card {
            perspective: 1000px;
        }

        .dashboard-inner {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(15px);
            border-radius: 1.5rem;
            padding: 2rem;
            text-align: center;
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .dashboard-inner:hover {
            transform: rotateY(10deg) scale(1.05);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.35);
        }

        .dashboard-inner i {
            font-size: 2.5rem;
            color: #7C3AED; /* ikon ungu */
        }

        .btn-glass {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(12px);
            padding: 0.75rem 2rem;
            border-radius: 1rem;
            font-weight: 600;
            color: #6D28D9; /* teks ungu */
            transition: all 0.3s ease;
        }

        .btn-glass:hover {
            transform: scale(1.05);
            background: rgba(255, 255, 255, 0.35);
        }

        .text-purple-light {
            color: #EDE9FE;
        }
    </style>
</head>

<body>
    <div class="max-w-4xl w-full px-4 text-center">

        {{-- Logo dan Judul --}}
        <div class="mb-8">
            <img src="{{ asset('images/logokampus.png') }}" alt="Logo Universitas"
                class="mx-auto mb-4 w-24 h-24 object-contain">
            <h1 class="text-4xl font-extrabold text-white drop-shadow-lg">Sistem Antrean Dekanat</h1>
            <p class="text-purple-light mt-2">Universitas Widya Dharma Pontianak</p>
            <p class="text-white/80 mt-4">Layanan antrean Fakultas Teknologi Informasi.</p>
        </div>

        {{-- Display Card --}}
        <div class="grid place-items-center mb-8">
            <a href="{{ route('display') }}" class="dashboard-card">
                <div class="dashboard-inner">
                    <i class="fa-solid fa-desktop mb-4"></i>
                    <h2 class="text-lg font-semibold text-purple-900 mb-1 drop-shadow">Layar Display</h2>
                    <p class="text-purple-700 text-sm drop-shadow">Tampilkan antrean saat ini</p>
                </div>
            </a>
        </div>

        {{-- Login Button --}}
        <div class="flex justify-center">
            <a href="{{ route('login') }}" class="btn-glass">
                <i class="fa-solid fa-right-to-bracket mr-2"></i> Login Sistem
            </a>
        </div>

    </div>
</body>

</html>
