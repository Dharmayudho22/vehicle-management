@extends('layouts.app')
@section('title', 'Detail Kendaraan')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-start justify-between mb-5">
            <div>
                <h2 class="text-xl font-bold text-gray-800">{{ $vehicle->brand }} {{ $vehicle->model }}</h2>
                <p class="text-sm text-gray-400">{{ $vehicle->plate_number }}</p>
            </div>
            @php
                $sc = match($vehicle->status) {
                    'available'   => 'bg-green-100 text-green-700',
                    'in_use'      => 'bg-blue-100 text-blue-700',
                    'maintenance' => 'bg-red-100 text-red-700',
                    default       => 'bg-gray-100 text-gray-600',
                };
                $sl = match($vehicle->status) {
                    'available'   => 'Tersedia',
                    'in_use'      => 'Digunakan',
                    'maintenance' => 'Perawatan',
                    default       => $vehicle->status,
                };
            @endphp
            <span class="px-4 py-1.5 rounded-full text-sm font-bold {{ $sc }}">{{ $sl }}</span>
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><p class="text-xs text-gray-400">Tipe</p><p class="font-medium">{{ $vehicle->type }}</p></div>
            <div><p class="text-xs text-gray-400">Kepemilikan</p>
                <p class="font-medium">{{ match($vehicle->ownership) { 'owned'=>'Milik Perusahaan','leased'=>'Leasing','rental'=>'Sewa',default=>$vehicle->ownership } }}</p>
            </div>
            <div><p class="text-xs text-gray-400">Konsumsi BBM</p><p class="font-medium">{{ $vehicle->fuel_consumption ?? '-' }} km/L</p></div>
            <div><p class="text-xs text-gray-400">Servis Terakhir</p><p class="font-medium">{{ $vehicle->last_service?->format('d M Y') ?? '-' }}</p></div>
            <div><p class="text-xs text-gray-400">Servis Berikutnya</p>
                <p class="font-medium {{ $vehicle->next_service?->isPast() ? 'text-red-500' : '' }}">
                    {{ $vehicle->next_service?->format('d M Y') ?? '-' }}
                </p>
            </div>
            <div><p class="text-xs text-gray-400">Total Pemesanan</p><p class="font-medium">{{ $vehicle->bookings->count() }} kali</p></div>
        </div>
    </div>

    {{-- Riwayat Pemesanan --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Riwayat Pemesanan</h3>
        <div class="space-y-2">
            @forelse($vehicle->bookings->take(5) as $b)
            <div class="flex items-center justify-between text-sm p-3 bg-gray-50 rounded-lg">
                <span class="font-mono text-xs">{{ $b->booking_code }}</span>
                <span>{{ $b->requester->name }}</span>
                <span class="text-gray-400 text-xs">{{ $b->start_datetime->format('d M Y') }}</span>
                <span class="text-xs px-2 py-0.5 rounded-full
                    {{ $b->status === 'completed' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                    {{ ucfirst($b->status) }}
                </span>
            </div>
            @empty
            <p class="text-gray-400 text-sm">Belum ada riwayat pemesanan</p>
            @endforelse
        </div>
    </div>

    <div class="flex gap-3">
        <a href="{{ route('vehicles.index') }}" class="bg-gray-100 text-gray-700 px-5 py-2.5 rounded-lg text-sm hover:bg-gray-200">← Kembali</a>
        <a href="{{ route('vehicles.edit', $vehicle) }}" class="bg-yellow-500 text-white px-5 py-2.5 rounded-lg text-sm hover:bg-yellow-400">Edit Kendaraan</a>
    </div>
</div>
@endsection