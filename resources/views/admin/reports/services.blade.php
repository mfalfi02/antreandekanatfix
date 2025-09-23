@extends('layouts.app')

@section('content')
<div class="p-6 bg-white rounded-xl shadow-md">
    <h1 class="text-2xl font-bold mb-4">📊 Statistik Layanan</h1>

    <!-- Chart canvas -->
    <canvas id="serviceChart" class="mb-6" width="400" height="200"></canvas>

    <!-- Tabel Data -->
    <table class="min-w-full border">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 border">Nama Layanan</th>
                <th class="px-4 py-2 border">Estimasi Waktu</th>
                <th class="px-4 py-2 border">Jumlah Antrean</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($services as $service)
            <tr>
                <td class="px-4 py-2 border">{{ $service->name }}</td>
                <td class="px-4 py-2 border">{{ $service->est_time }}</td>
                <td class="px-4 py-2 border">{{ $service->queues_count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Pastikan Chart.js ter-include (boleh ditempatkan di layout/app.blade.php) --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // fallback jika variabel tidak dikirim (agar tidak error)
    const labels = @json($labels ?? []);
    const dataset = @json($data ?? []);

    const ctx = document.getElementById('serviceChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Antrean per Layanan',
                data: dataset,
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
@endsection
