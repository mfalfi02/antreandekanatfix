<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Antrean Dekanat - Dashboard Mahasiswa</title>
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
        $currentQueue = [
            'number' => 0,
            'status' => 'none',
            'estimatedWait' => '0 menit',
            'position' => 0
        ];

        $deanList = [
            (object)['id' => 1, 'name' => 'Prof. Dr. Sutrisno, M.Si.', 'position' => 'Dekan', 'room' => 'Ruang Dekan', 'available' => true],
            (object)['id' => 2, 'name' => 'Dr. Sri Wahyuni, M.Pd.', 'position' => 'Wakil Dekan I', 'room' => 'Ruang WD I', 'available' => false],
            (object)['id' => 3, 'name' => 'Dr. Ahmad Rahman, M.T.', 'position' => 'Wakil Dekan II', 'room' => 'Ruang WD II', 'available' => true],
            (object)['id' => 4, 'name' => 'Dra. Maria Sari, M.M.', 'position' => 'Wakil Dekan III', 'room' => 'Ruang WD III', 'available' => false]
        ];

        $serviceTypes = [
            (object)['id' => 1, 'name' => 'Legalisir Dokumen', 'estimatedTime' => '15 menit', 'description' => 'Legalisir ijazah, transkrip, sertifikat'],
            (object)['id' => 2, 'name' => 'Surat Aktif Kuliah', 'estimatedTime' => '10 menit', 'description' => 'Surat keterangan masih aktif kuliah'],
            (object)['id' => 3, 'name' => 'Surat Cuti Akademik', 'estimatedTime' => '20 menit', 'description' => 'Pengajuan cuti sementara dari kuliah'],
            (object)['id' => 4, 'name' => 'Konsultasi Akademik', 'estimatedTime' => '30 menit', 'description' => 'Konsultasi masalah akademik']
        ];

        $queueHistory = [
            (object)['id' => 1, 'date' => '2024-01-15', 'service' => 'Legalisir Dokumen', 'status' => 'completed', 'number' => 3],
            (object)['id' => 2, 'date' => '2024-01-10', 'service' => 'Surat Aktif Kuliah', 'status' => 'completed', 'number' => 7]
        ];
    @endphp

    <div class="min-h-screen bg-white">
      <div class="max-w-6xl mx-auto py-10 px-6 space-y-8">
        <!-- Header -->
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Dashboard Mahasiswa</h1>
            <p class="text-gray-500">Sistem Antrean Dekanat UNWIDHA</p>
          </div>
          <div class="flex items-center gap-2 text-gray-500">
            <i class="fa-solid fa-calendar text-sm"></i>
            <span id="current-date" class="text-sm">{{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}</span>
          </div>
        </div>

        <!-- Current Queue Status -->
        <div id="queue-status-card" class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg shadow-md hidden">
          <div class="flex items-center gap-2 mb-2">
            <i class="fa-solid fa-clock text-blue-500"></i>
            <h2 class="text-lg font-semibold text-blue-800">Status Antrean Anda</h2>
          </div>
          <div class="flex items-center justify-between">
            <div>
              <div class="flex items-center gap-4">
                <div class="text-4xl font-bold text-blue-600" id="current-queue-number">#0</div>
                <div>
                  <span id="current-queue-badge" class="inline-flex items-center px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                    Menunggu
                  </span>
                  <p class="text-sm text-gray-500 mt-1">
                    Posisi ke-<span id="current-queue-position">0</span> • Estimasi: <span id="current-queue-est-wait">0 menit</span>
                  </p>
                </div>
              </div>
            </div>
            <button class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-300 transition-colors">
              <i class="fa-solid fa-bell mr-2"></i> Aktifkan Notifikasi
            </button>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Queue Registration -->
          <div class="bg-white p-6 rounded-xl shadow-md">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Daftar Antrean Baru</h2>
            <div class="space-y-4">
              <div>
                <label class="block text-sm font-medium text-gray-700">Pilih Dosen Tujuan</label>
                <select id="dean-select" class="mt-2 block w-full pl-3 pr-10 py-2 text-base border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                  <option value="">Pilih dosen dekanat</option>
                  @foreach ($deanList as $dean)
                    <option value="{{ $dean->id }}" data-available="{{ $dean->available ? 'true' : 'false' }}" @if (!$dean->available) disabled @endif>
                      {{ $dean->name }}
                    </option>
                  @endforeach
                </select>
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700">Jenis Layanan</label>
                <select id="service-select" class="mt-2 block w-full pl-3 pr-10 py-2 text-base border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                  <option value="">Pilih jenis layanan</option>
                  @foreach ($serviceTypes as $service)
                    <option value="{{ $service->id }}" data-estimated-time="{{ $service->estimatedTime }}">
                      {{ $service->name }}
                    </option>
                  @endforeach
                  <option value="other">Lainnya</option>
                </select>
                <input type="text" id="other-service-input" placeholder="Masukkan jenis layanan" class="mt-2 block w-full pl-3 pr-10 py-2 text-base border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 hidden">
              </div>

              <button id="join-queue-btn" class="w-full bg-blue-600 text-white px-4 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors disabled:bg-gray-400 disabled:cursor-not-allowed">
                Ambil Nomor Antrean
              </button>
            </div>
          </div>

          <!-- Available Deans -->
          <div class="bg-white p-6 rounded-xl shadow-md">
            <h2 class="text-xl font-semibold text-gray-900 flex items-center gap-2 mb-4">
              <i class="fa-solid fa-user text-gray-600"></i> Status Dosen Dekanat
            </h2>
            <div class="space-y-3">
              @foreach ($deanList as $dean)
                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                  <div class="flex items-center gap-3">
                    <i class="fa-solid fa-user-circle text-2xl text-gray-500"></i>
                    <div>
                      <p class="font-semibold text-gray-900 text-base">{{ $dean->name }}</p>
                      <p class="text-xs text-gray-500">{{ $dean->position }}</p>
                      <div class="flex items-center gap-1 mt-1">
                        <i class="fa-solid fa-map-pin text-xs text-gray-500"></i>
                        <span class="text-xs text-gray-500">{{ $dean->room }}</span>
                      </div>
                    </div>
                  </div>
                  <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full {{ $dean->available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $dean->available ? 'Tersedia' : 'Sibuk' }}
                  </span>
                </div>
              @endforeach
            </div>
          </div>
        </div>

        <!-- Queue History -->
        <div class="bg-white p-6 rounded-xl shadow-md">
          <h2 class="text-xl font-semibold text-gray-900 mb-4">Riwayat Antrean</h2>
          <div id="history-list" class="space-y-3">
            @foreach ($queueHistory as $item)
              <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                <div class="flex items-center gap-3">
                  <i class="fa-solid fa-check-circle text-2xl text-green-600"></i>
                  <div>
                    <p class="font-medium text-gray-900">#{{ $item->number }} - {{ $item->service }}</p>
                    <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($item->date)->locale('id')->isoFormat('D/M/YYYY') }}</p>
                  </div>
                </div>
                <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-green-100 text-green-800">
                  Selesai
                </span>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const joinQueueBtn = document.getElementById('join-queue-btn');
            const deanSelect = document.getElementById('dean-select');
            const serviceSelect = document.getElementById('service-select');
            const otherServiceInput = document.getElementById('other-service-input');
            const queueStatusCard = document.getElementById('queue-status-card');
            const currentQueueNumber = document.getElementById('current-queue-number');
            const currentQueuePosition = document.getElementById('current-queue-position');
            const currentQueueEstWait = document.getElementById('current-queue-est-wait');
            const historyList = document.getElementById('history-list');

            let lastQueueNumber = 7; // Dummy, should be from database

            // Show/hide other service input
            serviceSelect.addEventListener('change', () => {
                if (serviceSelect.value === 'other') {
                    otherServiceInput.classList.remove('hidden');
                } else {
                    otherServiceInput.classList.add('hidden');
                }
            });

            // Function to handle "Ambil Nomor Antrean" button click
            joinQueueBtn.addEventListener('click', () => {
                const selectedDeanId = deanSelect.value;
                let selectedServiceName = '';

                if (serviceSelect.value === 'other') {
                    selectedServiceName = otherServiceInput.value.trim();
                } else {
                    selectedServiceName = serviceSelect.options[serviceSelect.selectedIndex].textContent;
                }
                
                if (!selectedDeanId || !selectedServiceName) {
                    alert('Harap pilih Dosen Tujuan dan Jenis Layanan.');
                    return;
                }

                // Simulate taking a new queue number
                lastQueueNumber++;
                const newQueueNumber = lastQueueNumber;
                const estimatedTime = serviceSelect.value === 'other' ? '15 menit' : serviceSelect.options[serviceSelect.selectedIndex].dataset.estimatedTime;
                
                // Update the current queue status card
                queueStatusCard.classList.remove('hidden');
                currentQueueNumber.textContent = `#${newQueueNumber}`;
                currentQueuePosition.textContent = Math.floor(Math.random() * 5) + 1; // Random position
                currentQueueEstWait.textContent = `${estimatedTime}`;

                // Disable form after taking a number
                deanSelect.disabled = true;
                serviceSelect.disabled = true;
                otherServiceInput.disabled = true;
                joinQueueBtn.disabled = true;

                // Simulate queue completion after a delay
                setTimeout(() => {
                    // Update the status to 'Selesai'
                    const newHistoryItem = document.createElement('div');
                    newHistoryItem.className = "flex items-center justify-between p-3 border border-gray-200 rounded-lg";
                    newHistoryItem.innerHTML = `
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-check-circle text-2xl text-green-600"></i>
                            <div>
                                <p class="font-medium text-gray-900">#${newQueueNumber} - ${selectedServiceName}</p>
                                <p class="text-sm text-gray-500">${new Date().toLocaleDateString('id-ID')}</p>
                            </div>
                        </div>
                        <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-green-100 text-green-800">
                            Selesai
                        </span>
                    `;
                    historyList.prepend(newHistoryItem);

                    // Reset the current queue status
                    queueStatusCard.classList.add('hidden');
                    deanSelect.disabled = false;
                    serviceSelect.disabled = false;
                    otherServiceInput.disabled = false;
                    joinQueueBtn.disabled = false;
                    deanSelect.value = "";
                    serviceSelect.value = "";
                    otherServiceInput.value = "";
                    otherServiceInput.classList.add('hidden');

                }, 10000); // Simulate a 10-second wait
            });
        });
    </script>
</body>
</html>
