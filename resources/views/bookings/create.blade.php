@extends('layouts.app')
@section('title', 'Buat Pemesanan Baru')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
        <h2 class="text-lg font-bold text-gray-800 mb-6">Form Pemesanan Kendaraan</h2>

        <form method="POST" action="{{ route('bookings.store') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kendaraan <span class="text-red-500">*</span></label>
                    <select name="vehicle_id" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
                        <option value="">-- Pilih Kendaraan --</option>
                        @foreach($vehicles as $v)
                        <option value="{{ $v->id }}" {{ old('vehicle_id')==$v->id?'selected':'' }}>
                            {{ $v->brand }} {{ $v->model }} — {{ $v->plate_number }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Driver <span class="text-red-500">*</span></label>
                    <select name="driver_id" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
                        <option value="">-- Pilih Driver --</option>
                        @foreach($drivers as $d)
                        <option value="{{ $d->id }}" {{ old('driver_id')==$d->id?'selected':'' }}>
                            {{ $d->user->name ?? 'Driver #'.$d->id }} ({{ $d->license_type }})
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Approver Level 1 <span class="text-red-500">*</span></label>
                    <select name="approver1_id" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
                        <option value="">-- Pilih Approver 1 --</option>
                        @foreach($approvers as $a)
                        <option value="{{ $a->id }}" {{ old('approver1_id')==$a->id?'selected':'' }}>
                            {{ $a->name }} ({{ $a->role }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Approver Level 2 <span class="text-red-500">*</span></label>
                    <select name="approver2_id" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
                        <option value="">-- Pilih Approver 2 --</option>
                        @foreach($approvers as $a)
                        <option value="{{ $a->id }}" {{ old('approver2_id')==$a->id?'selected':'' }}>
                            {{ $a->name }} ({{ $a->role }})
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Mulai <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="start_datetime" value="{{ old('start_datetime') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Selesai <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="end_datetime" value="{{ old('end_datetime') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan <span class="text-red-500">*</span></label>
                <input type="text" name="destination" value="{{ old('destination') }}" placeholder="Contoh: Site Tambang Block A"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keperluan <span class="text-red-500">*</span></label>
                <input type="text" name="purpose" value="{{ old('purpose') }}" placeholder="Contoh: Kunjungan lapangan rutin"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Penumpang</label>
                <input type="number" name="passenger_count" value="{{ old('passenger_count', 1) }}" min="1"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="bg-gray-900 text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-700 transition">
                    Buat Pemesanan
                </button>
                <a href="{{ route('bookings.index') }}"
                   class="bg-gray-100 text-gray-700 px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-200 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection