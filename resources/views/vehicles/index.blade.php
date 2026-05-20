@extends('layouts.app')
@section('title', 'Armada Kendaraan')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <span class="text-2xl">🚗</span>
        <div>
            <h2 class="font-bold text-gray-800">Armada Tambang</h2>
            <p class="text-xs text-gray-400">Katalog & ketersediaan armada aktif</p>
        </div>
    </div>
    <a href="{{ route('vehicles.create') }}" class="bg-gray-900 text-white text-sm px-5 py-2.5 rounded-lg hover:bg-gray-700 transition">
        + Tambah Kendaraan
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    @forelse($vehicles as $vehicle)
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 hover:border-yellow-300 transition cursor-pointer">
        <div class="flex items-start justify-between mb-3">
            <div>
                <p class="text-xs text-gray-400 uppercase">{{ $vehicle->brand }} — {{ match($vehicle->ownership) { 'owned'=>'Milik', 'leased'=>'Leasing', 'rental'=>'Sewa', default=>$vehicle->ownership } }}</p>
                <h3 class="font-bold text-gray-800">{{ $vehicle->brand }} {{ $vehicle->model }}</h3>
            </div>
            @php
                $sc = match($vehicle->status) {
                    'available'   => 'bg-green-100 text-green-700 border-green-200',
                    'in_use'      => 'bg-blue-100 text-blue-700 border-blue-200',
                    'maintenance' => 'bg-red-100 text-red-700 border-red-200',
                    default       => 'bg-gray-100 text-gray-600',
                };
                $sl = match($vehicle->status) {
                    'available'   => 'Tersedia',
                    'in_use'      => 'Digunakan',
                    'maintenance' => 'Perawatan',
                    default       => $vehicle->status,
                };
            @endphp
            <span class="text-xs font-bold px-3 py-1 rounded-full border {{ $sc }}">{{ $sl }}</span>
        </div>

        <div class="space-y-1 text-sm text-gray-600 mb-4">
            <div class="flex justify-between"><span>Plat Nomor:</span><span class="font-medium">{{ $vehicle->plate_number }}</span></div>
            <div class="flex justify-between"><span>Tipe:</span><span class="font-medium">{{ $vehicle->type }}</span></div>
            @if($vehicle->next_service)
            <div class="flex justify-between"><span>Jadwal Servis:</span>
                <span class="font-medium {{ $vehicle->next_service->isPast() ? 'text-red-500' : '' }}">
                    {{ $vehicle->next_service->format('d M Y') }}
                </span>
            </div>
            @endif
        </div>

        <div class="flex justify-between items-center pt-3 border-t border-gray-50 text-xs">
            <a href="{{ route('vehicles.show', $vehicle) }}" class="text-gray-400 hover:text-gray-600 uppercase tracking-wide">Log Keamanan</a>
            <a href="{{ route('vehicles.edit', $vehicle) }}" class="text-yellow-600 font-semibold hover:underline uppercase tracking-wide">Kelola & Log BBM →</a>
        </div>
    </div>
    @empty
    <div class="col-span-2 bg-white rounded-xl border p-12 text-center">
        <p class="text-gray-400">Belum ada kendaraan terdaftar</p>
    </div>
    @endforelse
</div>

<div class="mt-4">{{ $vehicles->links() }}</div>
@endsection