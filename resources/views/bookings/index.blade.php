@extends('layouts.app')
@section('title', 'Kelola Pemesanan')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-2xl">📋</span>
            <div>
                <h2 class="font-bold text-gray-800">Daftar Jadwal Pemesanan</h2>
                <p class="text-xs text-gray-400">Kendalikan perizinan unit pertambangan</p>
            </div>
        </div>
        <a href="{{ route('bookings.create') }}"
           class="bg-gray-900 text-white text-sm px-5 py-2.5 rounded-lg hover:bg-gray-700 transition">
            + Buat Pemesanan
        </a>
    </div>
</div>

{{-- Filter Tabs --}}
<div class="flex flex-wrap gap-2 mb-4">
    @php
        $statuses = ['all'=>'Semua Perjalanan','pending'=>'Menunggu','approved'=>'Disetujui','completed'=>'Selesai','rejected'=>'Ditolak'];
        $counts = ['all'=>$bookings->total(),'pending'=>0,'approved'=>0,'completed'=>0,'rejected'=>0];
        foreach($bookings as $b) { if(isset($counts[$b->status])) $counts[$b->status]++; }
        $active = request('status','all');
    @endphp
    @foreach($statuses as $val => $label)
    <a href="{{ route('bookings.index', ['status'=>$val=='all'?null:$val]) }}"
       class="px-4 py-1.5 rounded-full text-sm font-medium transition
              {{ $active===$val ? 'bg-gray-900 text-white' : 'bg-white border text-gray-600 hover:bg-gray-50' }}">
        {{ $label }}
    </a>
    @endforeach
</div>

{{-- Bookings List --}}
<div class="space-y-3">
    @forelse($bookings as $booking)
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-start justify-between mb-3">
            <div>
                <span class="font-mono text-sm font-semibold text-gray-800">{{ $booking->booking_code }}</span>
                <span class="text-gray-400 mx-2">|</span>
                <span class="text-sm text-gray-500">{{ $booking->purpose }}</span>
            </div>
            @php
                $badgeColor = match($booking->status) {
                    'pending'   => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                    'approved'  => 'bg-blue-100 text-blue-700 border-blue-200',
                    'completed' => 'bg-green-100 text-green-700 border-green-200',
                    'rejected'  => 'bg-red-100 text-red-700 border-red-200',
                    default     => 'bg-gray-100 text-gray-600',
                };
                $badgeLabel = match($booking->status) {
                    'pending'   => 'MENUNGGU',
                    'approved'  => 'DISETUJUI',
                    'completed' => 'SELESAI',
                    'rejected'  => 'DITOLAK',
                    default     => strtoupper($booking->status),
                };
            @endphp
            <span class="text-xs font-bold px-3 py-1 rounded-full border {{ $badgeColor }}">{{ $badgeLabel }}</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm text-gray-600">
            <div class="flex items-center gap-2">
                <span>🚙</span>
                <span>{{ $booking->vehicle->brand }} {{ $booking->vehicle->model }}
                    <span class="text-xs bg-gray-100 px-2 py-0.5 rounded ml-1">{{ $booking->vehicle->plate_number }}</span>
                </span>
            </div>
            <div class="flex items-center gap-2">
                <span>👤</span>
                <span>Driver: <strong>{{ $booking->driver->user->name ?? '-' }}</strong></span>
            </div>
            <div class="flex items-center gap-2">
                <span>📍</span>
                <span>{{ $booking->destination }}</span>
            </div>
        </div>

        <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-50">
            <span class="text-xs text-gray-400">
                🕐 {{ $booking->start_datetime->format('d M Y, H.i') }} — {{ $booking->end_datetime->format('d M Y, H.i') }}
            </span>
            <div class="flex gap-2">
                <a href="{{ route('bookings.show', $booking) }}"
                   class="text-xs text-blue-600 hover:underline">Detail</a>
                @if($booking->isEditable())
                <a href="{{ route('bookings.edit', $booking) }}"
                   class="text-xs text-yellow-600 hover:underline">Edit</a>
                @endif
                @if($booking->status === 'approved')
                <form method="POST" action="{{ route('bookings.complete', $booking) }}" class="inline">
                    @csrf
                    <button class="text-xs text-green-600 hover:underline">Selesaikan</button>
                </form>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl border border-gray-100 p-12 text-center">
        <p class="text-gray-400">Belum ada data pemesanan</p>
    </div>
    @endforelse
</div>

{{-- Pagination --}}
<div class="mt-4">{{ $bookings->links() }}</div>
@endsection