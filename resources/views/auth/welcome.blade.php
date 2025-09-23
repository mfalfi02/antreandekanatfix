{{-- resources/views/auth/welcome.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Antrean Dekanat</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background: linear-gradient(135deg, #6EE7B7, #3B82F6, #9333EA);
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
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(12px);
            border-radius: 1.5rem;
            padding: 2rem;
            text-align: center;
            transition: transform 0.4s ease, box-shadow 0.4s ease;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .dashboard-inner:hover {
            transform: rotateY(10deg) scale(1.05);
            box-shadow: 0 20px 50px rgba(0,0,0,0.35);
        }

        .dashboard-inner i {
            font-size: 2.5rem;
        }

        .btn-glass {
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 0.75rem 2rem;
            border-radius: 1rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-glass:hover {
            transform: scale(1.05);
            background: rgba(255,255,255,0.3);
        }
    </style>
</head>
<body>
    <div class="max-w-6xl w-full px-4">
        <div class="text-center mb-8 text-white">
            <h1 class="text-4xl font-extrabold drop-shadow-lg">✨ Sistem Antrean Dekanat</h1>
            <p class="text-lg mt-2 drop-shadow-sm">Universitas Widya Dharma Pontianak</p>
            <p class="text-white/80 mt-4">Pilih dashboard sesuai dengan peran Anda untuk mengakses sistem antrean dekanat</p>
        </div>

        {{-- Dashboard Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $cards = [
                    ['route'=>'dashboard.admin','icon'=>'fa-gear','title'=>'Dashboard Admin','desc'=>'Kelola pengguna, layanan, dan laporan','color'=>'text-blue-500'],
                    ['route'=>'dosen.dashboard','icon'=>'fa-chalkboard-user','title'=>'Dashboard Dosen','desc'=>'Kelola antrean, panggil mahasiswa','color'=>'text-green-500'],
                    ['route'=>'mahasiswa.dashboard','icon'=>'fa-graduation-cap','title'=>'Dashboard Mahasiswa','desc'=>'Ambil nomor antrean','color'=>'text-orange-500'],
                    ['route'=>'display','icon'=>'fa-desktop','title'=>'Layar Display','desc'=>'Tampilan informasi antrean','color'=>'text-red-500'],
                ];
            @endphp

            @foreach($cards as $card)
            <a href="{{ route($card['route']) }}" class="dashboard-card">
                <div class="dashboard-inner">
                    <i class="fa-solid {{ $card['icon'] }} {{ $card['color'] }} mb-4"></i>
                    <h2 class="text-lg font-semibold text-white mb-1 drop-shadow">{{ $card['title'] }}</h2>
                    <p class="text-white/80 text-sm drop-shadow">{{ $card['desc'] }}</p>
                </div>
            </a>
            @endforeach
        </div>

        {{-- Action Buttons --}}
        <div class="mt-12 flex justify-center space-x-6">
            <button class="btn-glass text-white">Demo Queue Management</button>
            <a href="{{ route('login') }}" class="btn-glass text-white">Login Sistem</a>
        </div>

        <p class="text-white/60 text-xs mt-8 text-center">Untuk menggunakan fitur lengkap, silakan hubungkan dengan Supabase terlebih dahulu</p>
    </div>
</body>
</html>
