@extends('layouts.app')

@section('content')
<div class="p-6 bg-white rounded-xl shadow-md">
    <h1 class="text-2xl font-bold mb-4">📑 Rekap Laporan</h1>
    <ul class="list-disc ml-6 space-y-2">
        <li>Total Pengguna: <strong>{{ $totalUsers }}</strong></li>
        <li>Kategori Layanan: <strong>{{ $serviceCategories }}</strong></li>
        <li>Selesai Hari Ini: <strong>{{ $completedToday }}</strong></li>
    </ul>
</div>
@endsection
