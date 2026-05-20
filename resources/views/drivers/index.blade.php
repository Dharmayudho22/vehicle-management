@extends('layouts.app')
@section('title', 'Data Supir Tambang')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <span class="text-2xl">👤</span>
        <div>
            <h2 class="font-bold text-gray-800">Driver Operasional Tambang</h2>
            <p class="text-xs text-gray-400">Daftar operator driver tersertifikasi & masa berlaku lisensi SIM</p>
        </div>
    </div>
    <a href="{{ route('drivers.create') }}" class="bg-gray-900 text-white text-sm px-5 py-2.5 rounded-lg hover:bg-gray-700 transition">
        + Tambah Driver
    </a>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($drivers as $driver)
    @php $expired = \Carbon\Carbon::parse($driver->license_expiry ?? now()->addYear())->isPast(); @endphp
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 relative">
        @if($expired)
        <div class="absolute top-3 right-3 bg-red-500 text-white text-xs px-2 py-1 rounded-full">⚠ SIM EXPIRED</div>
        @endif

        <div class="flex items-center gap-3 mb-4">
            <div class="w-12 h-12 rounded-full bg-orange-100 flex items-center justify-center text-orange-500 text-xl">
                👤
            </div>
            <div>
                <h3 class="font-bold text-gray-800">{{ $driver->user->name ?? 'Driver #'.$driver->id }}</h3>
                @php
                    $sc = match($driver->status) {
                        'active'   => 'bg-green-100 text-green-700',
                        'inactive' => 'bg-gray-100 text-gray-600',
                        default    => 'bg-blue-100 text-blue-700',
                    };
                    $sl = match($driver->status) {
                        'active'   => 'READY',
                        'inactive' => 'ON-DUTY / OFF',
                        default    => strtoupper($driver->status),
                    };
                @endphp
                <span class="text-xs px-2 py-0.5 rounded-full {{ $sc }}">{{ $sl }}</span>
            </div>
        </div>

        <div class="space-y-1.5 text-sm text-gray-600 mb-4">
            @if($driver->user?->phone)
            <div class="flex items-center gap-2"><span>📞</span> HP: {{ $driver->user->phone }}</div>
            @endif
            <div class="flex items-center gap-2"><span>📄</span> SIM: {{ $driver->license_number }}</div>
            @if($driver->license_expiry ?? false)
            <div class="flex items-center gap-2 {{ $expired ? 'text-red-500' : '' }}">
                <span>📅</span> Masa Berlaku SIM: {{ \Carbon\Carbon::parse($driver->license_expiry)->format('d F Y') }}
            </div>
            @endif
        </div>

        <div class="flex items-center justify-between pt-3 border-t border-gray-50">
            <span class="text-xs text-gray-500">Ganti Status:</span>
            <div class="flex gap-2">
                <form method="POST" action="{{ route('drivers.update', $driver) }}">
                    @csrf @method('PUT')
                    <input type="hidden" name="license_number" value="{{ $driver->license_number }}">
                    <input type="hidden" name="license_type" value="{{ $driver->license_type }}">
                    <input type="hidden" name="status" value="active">
                    <button class="text-xs px-3 py-1 rounded-full border
                        {{ $driver->status === 'active' ? 'bg-green-600 text-white border-green-600' : 'border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                        Set Available
                    </button>
                </form>
                <form method="POST" action="{{ route('drivers.update', $driver) }}">
                    @csrf @method('PUT')
                    <input type="hidden" name="license_number" value="{{ $driver->license_number }}">
                    <input type="hidden" name="license_type" value="{{ $driver->license_type }}">
                    <input type="hidden" name="status" value="inactive">
                    <button class="text-xs px-3 py-1 rounded-full border
                        {{ $driver->status === 'inactive' ? 'bg-blue-600 text-white border-blue-600' : 'border-gray-300 text-gray-600 hover:bg-gray-50' }}">
                        Set Occupied
                    </button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-span-3 bg-white rounded-xl border p-12 text-center">
        <p class="text-gray-400">Belum ada driver terdaftar</p>
    </div>
    @endforelse
</div>

<div class="mt-4">{{ $drivers->links() }}</div>
@endsection