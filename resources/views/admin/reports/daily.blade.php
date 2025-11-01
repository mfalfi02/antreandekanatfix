@extends('layouts.app')

@section('content')
<div class="p-6 bg-white rounded-lg shadow">
    <h2 class="text-2xl font-semibold mb-4">{{ $title }}</h2>
    <p class="text-gray-600 mb-6">Tanggal: {{ $date }}</p>

    <table class="w-full border-collapse border border-gray-300">
        <thead class="bg-gray-100">
            <tr>
                <th class="border p-2">No</th>
                <th class="border p-2">Nama</th>
                <th class="border p-2">Layanan</th>
                <th class="border p-2">Waktu</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($queues as $q)
                <tr>
                    <td class="border p-2">{{ $loop->iteration }}</td>
                    <td class="border p-2">{{ $q->user->name ?? '-' }}</td>
                    <td class="border p-2">{{ $q->service->nama_layanan ?? '-' }}</td>
                    <td class="border p-2">{{ $q->created_at->format('H:i') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection