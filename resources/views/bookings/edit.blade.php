@extends('layouts.app')
@section('title', 'Edit Pemesanan')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-lg font-bold text-gray-800">Edit Pemesanan</h2>
                <p class="text-xs text-gray-400 font-mono">{{ $booking->booking_code }}</p>
            </div>
            <span class="text-xs bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full font-medium">PENDING</span>
        </div>

        <form method="POST" action="{{ route('bookings.update', $booking) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kendaraan *</label>
                    <select name="vehicle_id" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
                        <option value="">-- Pilih Kendaraan --</option>
                        @foreach($vehicles as $v)
                        <option value="{{ $v->id }}" {{ ($booking->vehicle_id == $v->id || old('vehicle_id') == $v->id) ? 'selected' : '' }}>
                            {{ $v->brand }} {{ $v->model }} — {{ $v->plate_number }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Driver *</label>
                    <select name="driver_id" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
                        <option value="">-- Pilih Driver --</option>
                        @foreach($drivers as $d)
                        <option value="{{ $d->id }}" {{ ($booking->driver_id == $d->id || old('driver_id') == $d->id) ? 'selected' : '' }}>
                            {{ $d->user->name ?? 'Driver #'.$d->id }} ({{ $d->license_type }})
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Approver Level 1 *</label>
                    <select name="approver1_id" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
                        <option value="">-- Pilih Approver 1 --</option>
                        @foreach($approvers as $a)
                        @php $currentA1 = $booking->approvalLevel1?->approver_id @endphp
                        <option value="{{ $a->id }}" {{ ($currentA1 == $a->id || old('approver1_id') == $a->id) ? 'selected' : '' }}>
                            {{ $a->name }} ({{ $a->role }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Approver Level 2 *</label>
                    <select name="approver2_id" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
                        <option value="">-- Pilih Approver 2 --</option>
                        @foreach($approvers as $a)
                        @php $currentA2 = $booking->approvalLevel2?->approver_id @endphp
                        <option value="{{ $a->id }}" {{ ($currentA2 == $a->id || old('approver2_id') == $a->id) ? 'selected' : '' }}>
                            {{ $a->name }} ({{ $a->role }})
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Mulai *</label>
                    <input type="datetime-local" name="start_datetime"
                           value="{{ old('start_datetime', $booking->start_datetime->format('Y-m-d\TH:i')) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Waktu Selesai *</label>
                    <input type="datetime-local" name="end_datetime"
                           value="{{ old('end_datetime', $booking->end_datetime->format('Y-m-d\TH:i')) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan *</label>
                <input type="text" name="destination"
                       value="{{ old('destination', $booking->destination) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Keperluan *</label>
                <input type="text" name="purpose"
                       value="{{ old('purpose', $booking->purpose) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none" required>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Penumpang</label>
                <input type="number" name="passenger_count" min="1"
                       value="{{ old('passenger_count', $booking->passenger_count) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:ring-2 focus:ring-yellow-400 focus:outline-none">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="bg-gray-900 text-white px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-700 transition">
                    Simpan Perubahan
                </button>
                <a href="{{ route('bookings.show', $booking) }}"
                   class="bg-gray-100 text-gray-700 px-6 py-2.5 rounded-lg text-sm font-medium hover:bg-gray-200 transition">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection