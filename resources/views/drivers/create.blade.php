@extends('layouts.app')
@section('title', 'Tambah Driver')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
        <h2 class="text-lg font-bold text-gray-800 mb-6">Form Tambah Driver</h2>
        <form method="POST" action="{{ route('drivers.store') }}" class="space-y-5">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">User (Role Driver) *</label>
                <select name="user_id" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
                    <option value="">-- Pilih User --</option>
                    @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ old('user_id')==$u->id?'selected':'' }}>
                        {{ $u->name }} ({{ $u->email }})
                    </option>
                    @endforeach
                </select>
                @if($users->isEmpty())
                <p class="text-xs text-red-500 mt-1">Tidak ada user dengan role 'driver' yang tersedia. Tambah user dengan role driver terlebih dahulu.</p>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor SIM *</label>
                    <input type="text" name="license_number" value="{{ old('license_number') }}" placeholder="SIM-B1-001-2022"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe SIM *</label>
                    <select name="license_type" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
                        <option value="A"  {{ old('license_type')==='A' ?'selected':'' }}>A</option>
                        <option value="B1" {{ old('license_type')==='B1'?'selected':'' }}>B1</option>
                        <option value="B2" {{ old('license_type')==='B2'?'selected':'' }}>B2</option>
                        <option value="C"  {{ old('license_type')==='C' ?'selected':'' }}>C</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-gray-900 text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-700 transition">
                    Simpan Driver
                </button>
                <a href="{{ route('drivers.index') }}" class="bg-gray-100 text-gray-700 px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-200 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection