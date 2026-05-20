@extends('layouts.app')
@section('title', 'Ringkasan Laporan & Export')

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <span class="text-2xl">📊</span>
        <div>
            <h2 class="font-bold text-gray-800">Laporan Periodik & Export Excel</h2>
            <p class="text-xs text-gray-400">Saring rentang tanggal pemesanan lalu unduh dokumen spreadsheet resmi nikel</p>
        </div>
    </div>
    <a href="{{ route('reports.export', request()->query()) }}"
       class="bg-green-600 text-white text-sm px-5 py-2.5 rounded-lg hover:bg-green-500 transition flex items-center gap-2">
        ⬇ Export Excel (.xls)
    </a>
</div>

{{-- Filter --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 mb-6">
    <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-xs text-gray-500 mb-1">Pencarian Umum</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Masukkan kode, nama, depar..."
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Dari Tanggal</label>
            <input type="date" name="date_from" value="{{ $dateFrom }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Sampai Tanggal</label>
            <input type="date" name="date_to" value="{{ $dateTo }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">Status Pemesanan</label>
            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                <option value="">Semua Status</option>
                <option value="pending" {{ request('status')==='pending'?'selected':'' }}>Menunggu</option>
                <option value="approved" {{ request('status')==='approved'?'selected':'' }}>Disetujui</option>
                <option value="completed" {{ request('status')==='completed'?'selected':'' }}>Selesai</option>
                <option value="rejected" {{ request('status')==='rejected'?'selected':'' }}>Ditolak</option>
            </select>
        </div>
        <div class="md:col-span-4">
            <button type="submit" class="bg-gray-900 text-white px-5 py-2 rounded-lg text-sm hover:bg-gray-700 transition">Filter</button>
        </div>
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-5 border-b flex items-center justify-between">
        <div>
            <h3 class="font-semibold text-gray-800">Preview Data Laporan ({{ $bookings->count() }} Log)</h3>
            <p class="text-xs text-gray-400">Saring otomatis sebelum diunduh</p>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">No</th>
                    <th class="px-4 py-3 text-left">Kode</th>
                    <th class="px-4 py-3 text-left">Pemohon / Dept</th>
                    <th class="px-4 py-3 text-left">Kendaraan</th>
                    <th class="px-4 py-3 text-left">Driver</th>
                    <th class="px-4 py-3 text-left">Rute Perjalanan</th>
                    <th class="px-4 py-3 text-left">Keperluan</th>
                    <th class="px-4 py-3 text-left">Waktu Mulai</th>
                    <th class="px-4 py-3 text-left">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($bookings as $i => $b)
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-400">{{ $i+1 }}</td>
                    <td class="px-4 py-3 font-mono text-xs">{{ $b->booking_code }}</td>
                    <td class="px-4 py-3">
                        <p class="font-medium">{{ $b->requester->name }}</p>
                        <p class="text-xs text-gray-400">{{ $b->requester->department ?? $b->requester->role }}</p>
                    </td>
                    <td class="px-4 py-3">
                        <p>{{ $b->vehicle->brand }} {{ $b->vehicle->model }}</p>
                        <p class="text-xs text-gray-400">{{ $b->vehicle->plate_number }}</p>
                    </td>
                    <td class="px-4 py-3">{{ $b->driver->user->name ?? '-' }}</td>
                    <td class="px-4 py-3 text-xs">{{ $b->destination }}</td>
                    <td class="px-4 py-3 text-xs text-gray-500">"{{ Str::limit($b->purpose, 20) }}"</td>
                    <td class="px-4 py-3 text-xs">{{ $b->start_datetime->format('d M Y, H:i') }}</td>
                    <td class="px-4 py-3">
                        @php $bc = match($b->status) {
                            'pending'=>'bg-yellow-100 text-yellow-700',
                            'approved'=>'bg-blue-100 text-blue-700',
                            'completed'=>'bg-green-100 text-green-700',
                            'rejected'=>'bg-red-100 text-red-700',
                            default=>'bg-gray-100 text-gray-600'
                        }; @endphp
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $bc }}">{{ ucfirst($b->status) }}</span>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="px-4 py-8 text-center text-gray-400">Tidak ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection