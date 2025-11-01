<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Antrean Dekanat - Dashboard Dosen</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body { background-color: #f0f4f8; } /* bg-gray-100 */
    </style>
</head>
<body class="p-8">

<div class="max-w-7xl mx-auto space-y-6">

    {{-- Header --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Dashboard Dosen</h1>
            <p class="text-gray-600">Selamat datang, {{ $data['user']->name }}</p>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-sm">Status Ruangan:</span>
            <span id="room-status" class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-red-100 text-red-800">
                Tutup
            </span>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit"
                    class="px-4 py-2 rounded-lg bg-red-600 text-white font-medium hover:bg-red-700 transition flex items-center gap-2">
                    <i class="fa fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>

    {{-- Kontrol Antrean --}}
    <div class="bg-white p-6 rounded-xl shadow-lg">
        <h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2 mb-4">
            <i class="fa-solid fa-play-circle text-gray-600"></i> Kontrol Antrean
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700">Jenis Layanan</label>
                <select id="service-select" class="mt-2 block w-full pl-3 pr-10 py-2 border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Pilih jenis layanan</option>
                    <option value="all">Semua jenis layanan</option>
                    @foreach ($data['services'] as $service)
                        <option value="{{ $service->id }}">{{ $service->nama_layanan }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center space-x-2">
                <button type="button" id="open-queue-btn-1" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">Buka Antrean</button>
                <button type="button" id="close-queue-btn-1" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition hidden">Tutup Antrean</button>
            </div>

            <div>
                <button type="button" id="open-queue-btn-2" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition">Buka Antrean</button>
                <button type="button" id="close-queue-btn-2" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-red-700 transition hidden">Tutup Antrean</button>
            </div>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-lg flex items-center gap-4">
            <i class="fa-solid fa-users text-3xl text-gray-600"></i>
            <div>
                <p class="text-2xl font-bold">{{ $data['activeQueues'] }}</p>
                <p class="text-sm text-gray-500">Menunggu</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-lg flex items-center gap-4">
            <i class="fa-solid fa-check-circle text-3xl text-green-600"></i>
            <div>
                <p class="text-2xl font-bold">{{ $data['completedQueues'] }}</p>
                <p class="text-sm text-gray-500">Selesai</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-lg flex items-center gap-4">
            <i class="fa-solid fa-clock text-3xl text-yellow-600"></i>
            <div>
                <p class="text-2xl font-bold">-</p>
                <p class="text-sm text-gray-500">Nomor Saat Ini</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-lg flex items-center gap-4">
            <i class="fa-solid fa-hourglass-half text-3xl text-gray-600"></i>
            <div>
                <p class="text-lg font-bold">0 menit</p>
                <p class="text-sm text-gray-500">Estimasi Tunggu</p>
            </div>
        </div>
    </div>

    {{-- Antrean Aktif dan Selesai --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Antrean Aktif --}}
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <div class="flex justify-between mb-4">
                <h2 class="text-xl font-semibold text-gray-900">Antrean Aktif</h2>
                <button id="complete-service-btn" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition hidden">
                    <i class="fa-solid fa-check-circle mr-2"></i> Selesai Layani
                </button>
            </div>
            <div id="active-queue-list" class="space-y-3 max-h-96 overflow-y-auto">
                @forelse ($data['myQueues'] as $queue)
                    @if($queue->status === 'Menunggu')
                        <div id="queue-item-{{ $queue->id }}" class="p-3 border rounded-lg transition-all duration-300">
                            <div class="flex items-center justify-between">
                                <div class="flex-1 flex items-center gap-3">
                                    <div class="text-2xl font-bold text-gray-500 queue-number">#{{ $queue->id }}</div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $queue->mahasiswa->nama ?? '-' }}</p>
                                        <p class="text-sm text-gray-500 queue-service">
                                            {{ $queue->service->nama_layanan ?? '-' }} • NIM: {{ $queue->kode_user }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-1">
                                    <button data-id="{{ $queue->id }}" data-number="{{ $queue->id }}" class="call-btn bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-300 mb-1">
                                        Panggil
                                    </button>
                                    <button class="bg-gray-200 text-gray-700 p-2 rounded-lg hover:bg-gray-300">
                                        <i class="fa-solid fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <p class="text-gray-500 text-sm">Belum ada antrean.</p>
                @endforelse
            </div>
        </div>

        {{-- Antrean Selesai --}}
        <div class="bg-white p-6 rounded-xl shadow-lg">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Selesai</h2>
            <div id="completed-queue-list" class="space-y-3 max-h-96 overflow-y-auto">
                @forelse ($data['myQueues'] as $queue)
                    @if($queue->status === 'Selesai')
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-check-circle text-2xl text-green-600"></i>
                                <div>
                                    <p class="font-semibold text-gray-900">#{{ $queue->id }} - {{ $queue->mahasiswa->nama ?? '-' }}</p>
                                    <p class="text-sm text-gray-500">{{ $queue->service->nama_layanan ?? '-' }}</p>
                                </div>
                            </div>
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-green-100 text-green-800">
                                {{ $queue->updated_at->format('H:i') }}
                            </span>
                        </div>
                    @endif
                @empty
                    <p class="text-gray-500 text-sm">Belum ada antrean selesai.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script src="//{{ request()->getHost() }}:6001/socket.io/socket.io.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo/dist/echo.iife.js"></script>
<script>
    const roomStatus = document.getElementById('room-status');
    const openBtns = [document.getElementById('open-queue-btn-1'), document.getElementById('open-queue-btn-2')];
    const closeBtns = [document.getElementById('close-queue-btn-1'), document.getElementById('close-queue-btn-2')];
    const token = "{{ csrf_token() }}";

    function updateStatus(isOpen){
        if(isOpen){
            roomStatus.textContent = 'Buka';
            roomStatus.classList.remove('bg-red-100','text-red-800');
            roomStatus.classList.add('bg-green-100','text-green-800');
            openBtns.forEach(b=>b.classList.add('hidden'));
            closeBtns.forEach(b=>b.classList.remove('hidden'));
        } else {
            roomStatus.textContent = 'Tutup';
            roomStatus.classList.remove('bg-green-100','text-green-800');
            roomStatus.classList.add('bg-red-100','text-red-800');
            openBtns.forEach(b=>b.classList.remove('hidden'));
            closeBtns.forEach(b=>b.classList.add('hidden'));
        }
    }

    async function toggleQueue(open){
        try {
            const res = await fetch("{{ route('queue.toggle') }}",{
                method:'POST',
                headers:{
                    'Content-Type':'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({ open })
            });
            const data = await res.json();
            if(data.status === 'ok'){
                updateStatus(data.is_active_queue);
            }
        } catch(e){
            console.error(e);
        }
    }

    openBtns.forEach(b=>b.addEventListener('click',()=>toggleQueue(true)));
    closeBtns.forEach(b=>b.addEventListener('click',()=>toggleQueue(false)));

    // Laravel Echo realtime
    window.Echo.channel('queue-status')
        .listen('.queue.updated', (e)=>{
            const isOpen = e.user?.is_active_queue ?? false;
            updateStatus(isOpen);
            console.log('Update realtime:', e.user);
        });
</script>
</body>
</html>
