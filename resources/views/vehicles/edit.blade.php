@extends('layouts.app')
@section('title', 'Edit Kendaraan')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
        <h2 class="text-lg font-bold text-gray-800 mb-6">Edit Kendaraan — {{ $vehicle->plate_number }}</h2>

        <form method="POST" action="{{ route('vehicles.update', $vehicle) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Plat Nomor *</label>
                    <input type="text" name="plate_number"
                           value="{{ old('plate_number', $vehicle->plate_number) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Merk *</label>
                    <input type="text" name="brand"
                           value="{{ old('brand', $vehicle->brand) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Model *</label>
                    <input type="text" name="model"
                           value="{{ old('model', $vehicle->model) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe *</label>
                    <input type="text" name="type"
                           value="{{ old('type', $vehicle->type) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kepemilikan *</label>
                    <select name="ownership" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
                        <option value="owned"  {{ old('ownership', $vehicle->ownership)==='owned' ?'selected':'' }}>Milik Perusahaan</option>
                        <option value="leased" {{ old('ownership', $vehicle->ownership)==='leased'?'selected':'' }}>Leasing</option>
                        <option value="rental" {{ old('ownership', $vehicle->ownership)==='rental'?'selected':'' }}>Sewa</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                    <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
                        <option value="available"   {{ old('status', $vehicle->status)==='available'  ?'selected':'' }}>Tersedia</option>
                        <option value="in_use"      {{ old('status', $vehicle->status)==='in_use'     ?'selected':'' }}>Digunakan</option>
                        <option value="maintenance" {{ old('status', $vehicle->status)==='maintenance'?'selected':'' }}>Perawatan</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konsumsi BBM (km/L)</label>
                    <input type="number" name="fuel_consumption" step="0.1"
                           value="{{ old('fuel_consumption', $vehicle->fuel_consumption) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Servis Terakhir</label>
                    <input type="date" name="last_service"
                           value="{{ old('last_service', $vehicle->last_service?->format('Y-m-d')) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Servis Berikutnya</label>
                    <input type="date" name="next_service"
                           value="{{ old('next_service', $vehicle->next_service?->format('Y-m-d')) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none">
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-gray-900 text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-700 transition">
                    Simpan Perubahan
                </button>
                <a href="{{ route('vehicles.index') }}" class="bg-gray-100 text-gray-700 px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-200 transition">
                    Batal
                </a>
                <form method="POST" action="{{ route('vehicles.destroy', $vehicle) }}" class="ml-auto"
                      onsubmit="return confirm('Yakin hapus kendaraan ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="bg-red-50 text-red-600 px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-red-100 transition">
                        Hapus
                    </button>
                </form>
            </div>
        </form>
    </div>
</div>
@endsection