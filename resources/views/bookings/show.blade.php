@extends('layouts.app')
@section('title', 'Detail Pemesanan')

@section('content')
<div class="max-w-3xl mx-auto space-y-5">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <p class="font-mono text-lg font-bold text-gray-800">{{ $booking->booking_code }}</p>
                <p class="text-sm text-gray-400">{{ $booking->purpose }}</p>
            </div>
            @php
                $badgeColor = match($booking->status) {
                    'pending'   => 'bg-yellow-100 text-yellow-700',
                    'approved'  => 'bg-blue-100 text-blue-700',
                    'completed' => 'bg-green-100 text-green-700',
                    'rejected'  => 'bg-red-100 text-red-700',
                    default     => 'bg-gray-100 text-gray-600',
                };
            @endphp
            <span class="text-sm font-bold px-4 py-1.5 rounded-full {{ $badgeColor }}">
                {{ strtoupper($booking->status) }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-4 text-sm">
            <div><p class="text-gray-400 text-xs">Pemohon</p><p class="font-medium">{{ $booking->requester->name }}</p></div>
            <div><p class="text-gray-400 text-xs">Kendaraan</p><p class="font-medium">{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }} — {{ $booking->vehicle->plate_number }}</p></div>
            <div><p class="text-gray-400 text-xs">Driver</p><p class="font-medium">{{ $booking->driver->user->name ?? '-' }}</p></div>
            <div><p class="text-gray-400 text-xs">Tujuan</p><p class="font-medium">{{ $booking->destination }}</p></div>
            <div><p class="text-gray-400 text-xs">Waktu Mulai</p><p class="font-medium">{{ $booking->start_datetime->format('d M Y, H:i') }}</p></div>
            <div><p class="text-gray-400 text-xs">Waktu Selesai</p><p class="font-medium">{{ $booking->end_datetime->format('d M Y, H:i') }}</p></div>
            <div><p class="text-gray-400 text-xs">Penumpang</p><p class="font-medium">{{ $booking->passenger_count }} orang</p></div>
        </div>
    </div>

    {{-- Approval Status --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-800 mb-4">Status Persetujuan</h3>
        <div class="space-y-3">
            @foreach($booking->approvals as $approval)
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                <div>
                    <p class="text-sm font-medium">Level {{ $approval->level }}: {{ $approval->approver->name }}</p>
                    @if($approval->notes)
                    <p class="text-xs text-gray-500 mt-1">{{ $approval->notes }}</p>
                    @endif
                </div>
                <span class="text-xs font-bold px-3 py-1 rounded-full
                    {{ $approval->status === 'approved' ? 'bg-green-100 text-green-700' : '' }}
                    {{ $approval->status === 'pending'  ? 'bg-yellow-100 text-yellow-700' : '' }}
                    {{ $approval->status === 'rejected' ? 'bg-red-100 text-red-700' : '' }}">
                    {{ strtoupper($approval->status) }}
                </span>
            </div>
            @endforeach
        </div>
    </div>

    <div class="flex gap-3">
        <a href="{{ route('bookings.index') }}" class="bg-gray-100 text-gray-700 px-5 py-2.5 rounded-lg text-sm hover:bg-gray-200">← Kembali</a>
        @if($booking->isEditable())
        <a href="{{ route('bookings.edit', $booking) }}" class="bg-yellow-500 text-white px-5 py-2.5 rounded-lg text-sm hover:bg-yellow-400">Edit</a>
        @endif
        @if($booking->status === 'approved')
        <form method="POST" action="{{ route('bookings.complete', $booking) }}">
            @csrf
            <button class="bg-green-600 text-white px-5 py-2.5 rounded-lg text-sm hover:bg-green-500">Tandai Selesai</button>
        </form>
        @endif
    </div>
</div>
@endsection