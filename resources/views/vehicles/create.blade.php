@extends('layouts.app')
@section('title', 'Tambah Kendaraan')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
        <h2 class="text-lg font-bold text-gray-800 mb-6">Form Tambah Kendaraan</h2>
        <form method="POST" action="{{ route('vehicles.store') }}" class="space-y-5">
            @csrf
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Plat Nomor *</label>
                    <input type="text" name="plate_number" value="{{ old('plate_number') }}" placeholder="DT 1234 AB"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Merk *</label>
                    <input type="text" name="brand" value="{{ old('brand') }}" placeholder="Toyota"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Model *</label>
                    <input type="text" name="model" value="{{ old('model') }}" placeholder="Hilux"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe *</label>
                    <input type="text" name="type" value="{{ old('type') }}" placeholder="SUV / Pickup / Minibus"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kepemilikan *</label>
                    <select name="ownership" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
                        <option value="owned" {{ old('ownership')==='owned'?'selected':'' }}>Milik Perusahaan</option>
                        <option value="leased" {{ old('ownership')==='leased'?'selected':'' }}>Leasing</option>
                        <option value="rental" {{ old('ownership')==='rental'?'selected':'' }}>Sewa</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Konsumsi BBM (km/L)</label>
                    <input type="number" name="fuel_consumption" value="{{ old('fuel_consumption') }}" step="0.1"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Servis Terakhir</label>
                    <input type="date" name="last_service" value="{{ old('last_service') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Servis Berikutnya</label>
                    <input type="date" name="next_service" value="{{ old('next_service') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none">
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-gray-900 text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-700 transition">Simpan</button>
                <a href="{{ route('vehicles.index') }}" class="bg-gray-100 text-gray-700 px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-200 transition">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection