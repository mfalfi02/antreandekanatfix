<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Antrean Dekanat - Dashboard Dosen</title>
    @vite('resources/css/app.css')
    <style>
        body {
            background-color: #f0f4f8; /* Tailwind equivalent: bg-gray-100 or a custom shade */
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" xintegrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="p-8">

    @php
        // Data dummy, akan diganti dengan data dari controller atau database
        $isQueueOpen = false;
        $selectedService = '';
        $currentlyServing = null;

        $queueStats = [
            'waiting' => 3, // Jumlah antrean saat ini
            'completed' => 0, // Akan dihitung oleh JS
            'currentNumber' => '-', // Akan diupdate oleh JS
            'estimatedWaitTime' => '0 menit' // Akan diupdate oleh JS
        ];

        $queueList = [
            (object)['id' => 1, 'number' => 9, 'name' => 'Ahmad Rizki Pratama', 'service' => 'Legalisir Dokumen', 'waitTime' => '15 menit', 'priority' => 'normal', 'studentId' => '123456789', 'contact' => '081234567890'],
            (object)['id' => 2, 'number' => 10, 'name' => 'Sari Indah Permata', 'service' => 'Surat Aktif Kuliah', 'waitTime' => '20 menit', 'priority' => 'urgent', 'studentId' => '987654321', 'contact' => '081987654321'],
            (object)['id' => 3, 'number' => 11, 'name' => 'Budi Santoso', 'service' => 'Konsultasi Akademik', 'waitTime' => '25 menit', 'priority' => 'normal', 'studentId' => '456789123', 'contact' => '081456789123']
        ];

        $serviceTypes = [
            (object)['id' => 1, 'name' => 'Legalisir Dokumen', 'estimatedTime' => '15 menit'],
            (object)['id' => 2, 'name' => 'Surat Aktif Kuliah', 'estimatedTime' => '10 menit'],
            (object)['id' => 3, 'name' => 'Surat Cuti Akademik', 'estimatedTime' => '20 menit'],
            (object)['id' => 4, 'name' => 'Konsultasi Akademik', 'estimatedTime' => '30 menit']
        ];

        $completedToday = []; // Akan diisi oleh JS
    @endphp

    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Dashboard Dosen Dekanat</h1>
                <p class="text-gray-600">Kelola antrean dan layanan mahasiswa</p>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm">Status Ruangan:</span>
                    <span id="room-status" class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-red-100 text-red-800">
                        Tutup
                    </span>
                </div>
            </div>
        </div>

        <!-- Queue Control -->
        <div class="bg-white p-6 rounded-xl shadow">
            <h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2 mb-4">
                <i class="fa-solid fa-play-circle text-gray-600"></i> Kontrol Antrean
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Jenis Layanan</label>
                    <select id="service-select" class="mt-2 block w-full pl-3 pr-10 py-2 text-base border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih jenis layanan</option>
                        <option value="all">Semua jenis layanan</option>
                        @foreach ($serviceTypes as $service)
                            <option value="{{ $service->id }}">
                                {{ $service->name }} ({{ $service->estimatedTime }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center space-x-2">
                    <button id="open-queue-btn-1" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                        Buka Antrean
                    </button>
                    <button id="close-queue-btn-1" class="bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition-colors hidden">
                        Tutup Antrean
                    </button>
                </div>
                <div>
                    <button id="open-queue-btn-2" class="w-full bg-blue-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-blue-700 transition-colors">
                        Buka Antrean
                    </button>
                    <button id="close-queue-btn-2" class="w-full bg-red-600 text-white px-4 py-2 rounded-lg font-medium hover:bg-red-700 transition-colors hidden">
                        Tutup Antrean
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-xl shadow transition-transform transform hover:scale-105 hover:shadow-lg">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-users text-3xl text-gray-600"></i>
                    <div>
                        <p id="waiting-count" class="text-2xl font-bold">{{ count($queueList) }}</p>
                        <p class="text-sm text-gray-500">Menunggu</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow transition-transform transform hover:scale-105 hover:shadow-lg">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-check-circle text-3xl text-green-600"></i>
                    <div>
                        <p id="completed-count" class="text-2xl font-bold">{{ count($completedToday) }}</p>
                        <p class="text-sm text-gray-500">Selesai Hari Ini</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow transition-transform transform hover:scale-105 hover:shadow-lg">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-clock text-3xl text-yellow-600"></i>
                    <div>
                        <p id="current-number" class="text-2xl font-bold">-</p>
                        <p class="text-sm text-gray-500">Nomor Saat Ini</p>
                    </div>
                </div>
            </div>
            <div class="bg-white p-6 rounded-xl shadow transition-transform transform hover:scale-105 hover:shadow-lg">
                <div class="flex items-center space-x-2">
                    <i class="fa-solid fa-hourglass-half text-3xl text-gray-600"></i>
                    <div>
                        <p id="estimated-wait-time" class="text-lg font-bold">0 menit</p>
                        <p class="text-sm text-gray-500">Estimasi Tunggu</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Active Queue -->
            <div class="bg-white p-6 rounded-xl shadow">
                <div class="flex flex-row items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900">Antrean Aktif</h2>
                    <button id="complete-service-btn" class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 transition-colors hidden">
                        <i class="fa-solid fa-check-circle mr-2"></i> Selesai Layani
                    </button>
                </div>
                <div id="active-queue-list" class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach ($queueList as $item)
                        <div id="queue-item-{{ $item->id }}" class="p-3 border rounded-lg transition-all duration-300 {{ $item->priority === 'urgent' ? 'border-l-4 border-l-yellow-500' : '' }}">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3">
                                        <div class="text-2xl font-bold text-gray-500 queue-number">
                                            #{{ $item->number }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $item->name }}</p>
                                            <p class="text-sm text-gray-500 queue-service">
                                                {{ $item->service }} • ID: {{ $item->studentId }}
                                            </p>
                                            <p class="text-xs text-gray-500">
                                                Menunggu: {{ $item->waitTime }} • Tel: {{ $item->contact }}
                                            </p>
                                        </div>
                                    </div>
                                    @if ($item->priority === 'urgent')
                                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-yellow-100 text-yellow-800 mt-2 inline-block">
                                            Urgent
                                        </span>
                                    @endif
                                </div>
                                <div class="flex flex-col gap-1">
                                    <button data-id="{{ $item->id }}" data-number="{{ $item->number }}" class="call-btn bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-300 transition-colors mb-1">
                                        Panggil
                                    </button>
                                    <div class="flex gap-1">
                                        <button class="bg-gray-200 text-gray-700 p-2 rounded-lg text-sm hover:bg-gray-300 transition-colors">
                                            <i class="fa-solid fa-chevron-up"></i>
                                        </button>
                                        <button class="bg-gray-200 text-gray-700 p-2 rounded-lg text-sm hover:bg-gray-300 transition-colors">
                                            <i class="fa-solid fa-chevron-down"></i>
                                        </button>
                                        <button class="bg-gray-200 text-gray-700 p-2 rounded-lg text-sm hover:bg-gray-300 transition-colors">
                                            <i class="fa-solid fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Completed Today -->
            <div class="bg-white p-6 rounded-xl shadow">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Selesai Hari Ini</h2>
                <div id="completed-queue-list" class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach ($completedToday as $item)
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-check-circle text-2xl text-green-600"></i>
                                <div>
                                    <p class="font-semibold text-gray-900">#{{ $item->number }} - {{ $item->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $item->service }}</p>
                                </div>
                            </div>
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-green-100 text-green-800">
                                {{ $item->completedAt }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const roomStatusSpan = document.getElementById('room-status');
            const openQueueBtn1 = document.getElementById('open-queue-btn-1');
            const closeQueueBtn1 = document.getElementById('close-queue-btn-1');
            const openQueueBtn2 = document.getElementById('open-queue-btn-2');
            const closeQueueBtn2 = document.getElementById('close-queue-btn-2');
            const serviceSelect = document.getElementById('service-select');
            const completeServiceBtn = document.getElementById('complete-service-btn');
            const activeQueueList = document.getElementById('active-queue-list');
            const completedQueueList = document.getElementById('completed-queue-list');
            const callButtons = document.querySelectorAll('.call-btn');
            
            // Stats elements
            const waitingCount = document.getElementById('waiting-count');
            const completedCount = document.getElementById('completed-count');
            const currentNumber = document.getElementById('current-number');
            const estimatedWaitTime = document.getElementById('estimated-wait-time');
            
            const queueData = @json($queueList);
            const serviceData = @json($serviceTypes);
            const completedData = @json($completedToday);
            
            let currentlyServingId = null;

            function updateStats() {
                waitingCount.textContent = activeQueueList.children.length;
                completedCount.textContent = completedQueueList.children.length;
                
                if (currentlyServingId) {
                    const servingItem = queueData.find(item => item.id == currentlyServingId);
                    if (servingItem) {
                        currentNumber.textContent = `#${servingItem.number}`;
                    }
                } else {
                    currentNumber.textContent = '-';
                }
                
                // Hitung estimasi waktu tunggu
                const totalEstimatedTime = Array.from(activeQueueList.children).reduce((total, item) => {
                    const id = item.id.replace('queue-item-', '');
                    const queueItem = queueData.find(q => q.id == id);
                    const serviceItem = serviceData.find(s => s.name === queueItem.service);
                    if (serviceItem) {
                        const time = parseInt(serviceItem.estimatedTime.split(' ')[0]);
                        return total + time;
                    }
                    return total;
                }, 0);
                
                estimatedWaitTime.textContent = `${totalEstimatedTime} menit`;
            }

            function updateQueueStatus(isOpen) {
                if (isOpen) {
                    roomStatusSpan.textContent = 'Buka';
                    roomStatusSpan.classList.remove('bg-red-100', 'text-red-800');
                    roomStatusSpan.classList.add('bg-green-100', 'text-green-800');
                    
                    openQueueBtn1.classList.add('hidden');
                    closeQueueBtn1.classList.remove('hidden');
                    openQueueBtn2.classList.add('hidden');
                    closeQueueBtn2.classList.remove('hidden');

                    serviceSelect.disabled = true;

                } else {
                    roomStatusSpan.textContent = 'Tutup';
                    roomStatusSpan.classList.remove('bg-green-100', 'text-green-800');
                    roomStatusSpan.classList.add('bg-red-100', 'text-red-800');

                    openQueueBtn1.classList.remove('hidden');
                    closeQueueBtn1.classList.add('hidden');
                    openQueueBtn2.classList.remove('hidden');
                    closeQueueBtn2.classList.add('hidden');
                    
                    serviceSelect.disabled = false;
                }
            }

            openQueueBtn1.addEventListener('click', () => {
                if (serviceSelect.value) {
                    updateQueueStatus(true);
                } else {
                    alert('Pilih jenis layanan terlebih dahulu.');
                }
            });

            openQueueBtn2.addEventListener('click', () => {
                if (serviceSelect.value) {
                    updateQueueStatus(true);
                } else {
                    alert('Pilih jenis layanan terlebih dahulu.');
                }
            });

            closeQueueBtn1.addEventListener('click', () => {
                updateQueueStatus(false);
            });

            closeQueueBtn2.addEventListener('click', () => {
                updateQueueStatus(false);
            });

            callButtons.forEach(button => {
                button.addEventListener('click', (event) => {
                    const queueId = event.target.dataset.id;
                    if (currentlyServingId) {
                        const previousItem = document.getElementById(`queue-item-${currentlyServingId}`);
                        if (previousItem) {
                            previousItem.classList.remove('bg-blue-50', 'border-blue-600');
                            const previousNumberElement = previousItem.querySelector('.queue-number');
                            if (previousNumberElement) {
                                previousNumberElement.classList.remove('text-blue-600');
                                previousNumberElement.classList.add('text-gray-500');
                            }
                        }
                    }

                    currentlyServingId = queueId;
                    const currentItem = document.getElementById(`queue-item-${queueId}`);
                    if (currentItem) {
                        currentItem.classList.add('bg-blue-50', 'border-blue-600');
                        const currentNumberElement = currentItem.querySelector('.queue-number');
                        if (currentNumberElement) {
                            currentNumberElement.classList.remove('text-gray-500');
                            currentNumberElement.classList.add('text-blue-600');
                        }
                    }
                    
                    completeServiceBtn.classList.remove('hidden');
                    updateStats();
                });
            });

            completeServiceBtn.addEventListener('click', () => {
                if (currentlyServingId) {
                    const currentItem = document.getElementById(`queue-item-${currentlyServingId}`);
                    
                    // Ambil detail antrean yang selesai
                    const number = currentItem.querySelector('.queue-number').textContent.trim();
                    const name = currentItem.querySelector('.font-semibold').textContent.trim();
                    const service = currentItem.querySelector('.queue-service').textContent.split(' •')[0].trim();
                    const now = new Date();
                    const completedTime = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');

                    // Buat elemen baru untuk daftar "Selesai Hari Ini"
                    const completedItemHtml = `
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                            <div class="flex items-center gap-3">
                                <i class="fa-solid fa-check-circle text-2xl text-green-600"></i>
                                <div>
                                    <p class="font-semibold text-gray-900">${number} - ${name}</p>
                                    <p class="text-sm text-gray-500">${service}</p>
                                </div>
                            </div>
                            <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-green-100 text-green-800">
                                ${completedTime}
                            </span>
                        </div>
                    `;

                    // Tambahkan ke daftar "Selesai Hari Ini"
                    completedQueueList.insertAdjacentHTML('beforeend', completedItemHtml);

                    // Hapus dari daftar "Antrean Aktif"
                    if (currentItem) {
                        currentItem.remove();
                    }
                    currentlyServingId = null;
                    completeServiceBtn.classList.add('hidden');
                    updateStats();
                }
            });

            // Initial stat update
            updateStats();
        });
    </script>
</body>
</html>
