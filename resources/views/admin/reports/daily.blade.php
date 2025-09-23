@extends('layouts.app')

@section('content')
<div class="p-6 bg-white rounded-xl shadow-md">
    <h1 class="text-2xl font-bold mb-4">📅 Laporan Harian</h1>
    <p>Total antrean selesai hari ini: <strong>{{ $completedToday }}</strong></p>
    <p>Antrean aktif saat ini: <strong>{{ $activeQueues }}</strong></p>
</div>
@endsection
