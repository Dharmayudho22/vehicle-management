@extends('layouts.app')
@section('title', 'Tinjau Persetujuan')

@section('content')
<div class="max-w-2xl mx-auto space-y-5">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="font-bold text-gray-800 mb-5">Detail Pemesanan — {{ $approval->booking->booking_code }}</h2>

        <div class="grid grid-cols-2 gap-4 text-sm mb-5">
            <div><p class="text-xs text-gray-400">Pemohon</p><p class="font-medium">{{ $approval->booking->requester->name }}</p></div>
            <div><p class="text-xs text-gray-400">Kendaraan</p><p class="font-medium">{{ $approval->booking->vehicle->brand }} {{ $approval->booking->vehicle->model }}</p></div>
            <div><p class="text-xs text-gray-400">Driver</p><p class="font-medium">{{ $approval->booking->driver->user->name ?? '-' }}</p></div>
            <div><p class="text-xs text-gray-400">Tujuan</p><p class="font-medium">{{ $approval->booking->destination }}</p></div>
            <div><p class="text-xs text-gray-400">Waktu</p><p class="font-medium">{{ $approval->booking->start_datetime->format('d M Y H:i') }}</p></div>
            <div><p class="text-xs text-gray-400">Keperluan</p><p class="font-medium">{{ $approval->booking->purpose }}</p></div>
        </div>

        <div class="flex gap-3 text-xs mb-5">
            @foreach($approval->booking->approvals as $a)
            <span class="px-3 py-1.5 rounded-full border
                {{ $a->status === 'approved' ? 'border-green-300 text-green-600 bg-green-50' :
                   ($a->status === 'rejected' ? 'border-red-300 text-red-600 bg-red-50' : 'border-gray-200 text-gray-500') }}">
                Level {{ $a->level }}: {{ ucfirst($a->status) }}
            </span>
            @endforeach
        </div>

        @if($approval->isPending())
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Approve --}}
            <form method="POST" action="{{ route('approvals.approve', $approval) }}" class="bg-green-50 rounded-xl p-5 border border-green-100">
                @csrf
                <h3 class="font-semibold text-green-700 mb-3">✓ Setujui Pemesanan</h3>
                <textarea name="notes" rows="3" placeholder="Catatan (opsional)..."
                          class="w-full border border-green-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-300 mb-3"></textarea>
                <button class="w-full bg-green-600 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-green-500 transition">
                    Setujui Sekarang
                </button>
            </form>

            {{-- Reject --}}
            <form method="POST" action="{{ route('approvals.reject', $approval) }}" class="bg-red-50 rounded-xl p-5 border border-red-100">
                @csrf
                <h3 class="font-semibold text-red-700 mb-3">✗ Tolak Pemesanan</h3>
                <textarea name="notes" rows="3" placeholder="Alasan penolakan (wajib)..."
                          class="w-full border border-red-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-300 mb-3" required></textarea>
                <button class="w-full bg-red-600 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-red-500 transition">
                    Tolak Pemesanan
                </button>
            </form>
        </div>
        @else
        <div class="bg-gray-50 rounded-xl p-4 text-sm text-gray-600">
            Sudah diproses: <strong class="uppercase">{{ $approval->status }}</strong>
            @if($approval->notes) — {{ $approval->notes }} @endif
        </div>
        @endif
    </div>

    <a href="{{ route('approvals.index') }}" class="inline-block bg-gray-100 text-gray-700 px-5 py-2.5 rounded-lg text-sm hover:bg-gray-200">← Kembali</a>
</div>
@endsection