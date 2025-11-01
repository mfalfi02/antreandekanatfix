<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Dekanat - Display Antrean</title>
    @vite('resources/css/app.css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background-color: #f0f4f8;
        }

        .queue-status {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 0.5rem;
        }
    </style>
</head>

<body class="p-8">
    <div class="max-w-7xl mx-auto">
        <header class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">LAYANAN DEKANAT</h1>
                <p class="text-md text-gray-600">Universitas Widya Dharma Pontianak</p>
            </div>
            <div class="flex items-center space-x-4 text-sm text-gray-500">
                <div class="text-right">
                    <p id="current-date">Jumat, 19 September 2025</p>
                    <p id="current-time">01:20:49</p>
                </div>

                <!-- Tombol Kembali -->
                <a href="{{ route('welcome') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 text-white font-medium hover:bg-blue-700 transition duration-200 shadow">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </header>
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow p-6 text-center">
                <p id="total-queues" class="text-3xl font-bold text-blue-600">0</p>
                <p class="text-sm text-gray-500">Antrean Saat Ini</p>
            </div>
            <div class="bg-white rounded-xl shadow p-6 text-center">
                <p id="available-staff" class="text-3xl font-bold text-green-600">0</p>
                <p class="text-sm text-gray-500">Dosen Tersedia</p>
            </div>
            <div class="bg-white rounded-xl shadow p-6 text-center">
                <p id="serving-queues" class="text-3xl font-bold text-orange-600">0</p>
                <p class="text-sm text-gray-500">Sedang Melayani</p>
            </div>
            <div class="bg-white rounded-xl shadow p-6 text-center">
                <p id="waiting-queues" class="text-3xl font-bold text-gray-600">0</p>
                <p class="text-sm text-gray-500">Menunggu</p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Dosen Aktif -->
            <div class="bg-white p-6 rounded-xl shadow">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center mb-4">
                    <i class="fa-solid fa-users text-gray-500 mr-2"></i> Dosen Aktif Membuka Antrean
                </h2>
                <ul id="staff-status" class="space-y-4">
                    <li class="text-gray-500 text-sm">Memuat data...</li>
                </ul>
            </div>

            <!-- Status Antrean -->
            <div class="bg-white p-6 rounded-xl shadow">
                <h2 class="text-xl font-semibold text-gray-900 flex items-center mb-4">
                    <i class="fa-solid fa-list-check text-gray-500 mr-2"></i> Status Antrean Hari Ini
                </h2>
                <ul id="queue-list" class="space-y-4">
                    <li class="text-gray-500 text-sm">Memuat data...</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // Waktu realtime
        function updateClock() {
            const now = new Date();
            const options = {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            };
            document.getElementById('current-date').textContent = now.toLocaleDateString('id-ID', options);
            document.getElementById('current-time').textContent = now.toLocaleTimeString('id-ID');
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Fetch antrean dan dosen aktif
        async function fetchDisplayData() {
            try {
                const res = await fetch("{{ route('display.queues') }}"); // endpoint JSON
                const data = await res.json();

                // Stats
                const totalQueues = data.length;
                const serving = data.filter(q => q.status === 'Sedang Dilayani').length;
                const waiting = data.filter(q => q.status === 'Menunggu').length;

                document.getElementById('total-queues').textContent = totalQueues;
                document.getElementById('serving-queues').textContent = serving;
                document.getElementById('waiting-queues').textContent = waiting;
                document.getElementById('available-staff').textContent = [...new Set(data.filter(q => q.dosen).map(q =>
                    q.dosen.name))].length;

                // Update antrean
                const ulQueue = document.getElementById('queue-list');
                ulQueue.innerHTML = '';
                data.forEach(q => {
                    let statusColor = 'bg-gray-200 text-gray-600';
                    if (q.status === 'Selesai') statusColor = 'bg-green-100 text-green-600';
                    else if (q.status === 'Sedang Dilayani') statusColor = 'bg-blue-100 text-blue-600';
                    else if (q.status === 'Menunggu') statusColor = 'bg-yellow-100 text-yellow-600';

                    const li = document.createElement('li');
                    li.className = 'flex justify-between items-center border border-gray-200 p-4 rounded-lg';
                    li.innerHTML = `
                        <div>
                            <p class="font-medium text-gray-900">${q.user?.name ?? q.nama ?? '-'}</p>
                            <p class="text-sm text-gray-500">${q.service?.nama_layanan ?? '-'}</p>
                            <p class="text-xs text-gray-400">Dosen: ${q.dosen?.name ?? '-'}</p>
                        </div>
                        <span class="queue-status ${statusColor}">${q.status}</span>
                    `;
                    ulQueue.appendChild(li);
                });

                // Update dosen aktif
                const activeDosen = [...new Set(data.filter(q => q.status === 'Sedang Dilayani').map(q => q.dosen))]
                    .filter(Boolean);
                const ulStaff = document.getElementById('staff-status');
                ulStaff.innerHTML = '';
                if (activeDosen.length === 0) {
                    ulStaff.innerHTML = '<li class="text-gray-500 text-sm">Tidak ada dosen aktif</li>';
                } else {
                    activeDosen.forEach(d => {
                        const li = document.createElement('li');
                        li.className = 'flex justify-between items-start border border-gray-200 p-4 rounded-lg';
                        li.innerHTML = `
                            <div class="flex items-start space-x-3">
                                <i class="fa-solid fa-user-circle text-2xl text-gray-400"></i>
                                <div>
                                    <p class="font-medium text-gray-900">${d.name}</p>
                                    <p class="text-sm text-gray-500">${d.title ?? 'Dosen'}</p>
                                    <p class="text-xs text-gray-400">${d.room ?? '-'}</p>
                                </div>
                            </div>
                            <span class="text-xs bg-blue-100 text-blue-600 px-2 py-1 rounded-full font-semibold">Sedang Melayani</span>
                        `;
                        ulStaff.appendChild(li);
                    });
                }

            } catch (error) {
                console.error(error);
            }
        }

        fetchDisplayData();
        setInterval(fetchDisplayData, 5000); // refresh tiap 5 detik
    </script>
    <script src="https://js.pusher.com/8.0/pusher.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>

    <script>
        window.Echo.channel('display')
            .listen('QueueStatusUpdated', (e) => {
                const pejabat = e.activePejabat;
                if (pejabat) {
                    document.getElementById('active-pejabat-name').textContent = pejabat.name;
                    document.getElementById('active-pejabat-role').textContent = pejabat.role.charAt(0).toUpperCase() +
                        pejabat.role.slice(1);
                } else {
                    document.getElementById('active-pejabat-name').textContent = 'Belum ada yang membuka antrean';
                    document.getElementById('active-pejabat-role').textContent = '';
                }

                const ul = document.getElementById('queue-list');
                ul.innerHTML = '';
                let total = 0,
                    serving = 0,
                    waiting = 0,
                    completed = 0;
                e.queues.forEach(q => {
                    total++;
                    let statusClass = '';
                    if (q.status === 'Menunggu') {
                        statusClass = 'bg-gray-200 text-gray-600';
                        waiting++;
                    } else if (q.status === 'Sedang Dilayani') {
                        statusClass = 'bg-blue-100 text-blue-600';
                        serving++;
                    } else if (q.status === 'Selesai') {
                        statusClass = 'bg-green-100 text-green-600';
                        completed++;
                    }

                    const li = document.createElement('li');
                    li.className =
                        `flex justify-between items-center border border-gray-200 p-4 rounded-lg ${statusClass}`;
                    li.innerHTML = `<div>
            <p class="font-medium text-gray-900">${q.mahasiswa?.nama || '-'}</p>
            <p class="text-sm text-gray-500">${q.service?.nama_service || '-'}</p>
        </div>
        <span class="text-xs px-2 py-1 rounded-full font-semibold">${q.status}</span>`;
                    ul.appendChild(li);
                });

                document.getElementById('total-queues').textContent = total;
                document.getElementById('serving-count').textContent = serving;
                document.getElementById('waiting-count').textContent = waiting;
                document.getElementById('completed-count').textContent = completed;
            });
    </script>

</body>

</html>
