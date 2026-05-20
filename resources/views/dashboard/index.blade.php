@extends('layouts.app')
@section('title', 'Dashboard Monitoring')

@section('content')

{{-- Hero Banner --}}
<div class="bg-gray-900 text-white rounded-xl px-8 py-5 mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-lg font-bold">PT SEKAWAN MEDIA INFORMATIKA — TAMBANG ORE NIKEL</h2>
        <p class="text-sm text-gray-400 mt-1">Sistem Pemantauan Armada Angkutan Operasional Multi-Level Approval.</p>
    </div>
    <span class="text-xs text-green-400 font-mono border border-green-400 rounded px-3 py-1 flex items-center gap-1">
        <span class="w-2 h-2 bg-green-400 rounded-full inline-block"></span> SERVER KONEKSI AKTIF
    </span>
</div>

@if(auth()->user()->isAdmin())

{{-- DASHBOARD ADMIN --}}
{{-- Stats --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Total Pemesanan</p>
        <p class="text-4xl font-bold text-gray-900">{{ $stats['total_bookings'] }}</p>
        <p class="text-xs text-green-500 mt-1">+12% bln ini</p>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Kendaraan Aktif</p>
        <p class="text-4xl font-bold text-gray-900">{{ $stats['vehicles_in_use'] }} <span class="text-lg text-gray-400">/ {{ $stats['vehicles_total'] }}</span></p>
        <p class="text-xs text-yellow-500 mt-1">{{ $stats['vehicles_total'] > 0 ? round(($stats['vehicles_in_use']/$stats['vehicles_total'])*100) : 0 }}% Capacity</p>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Driver Tersedia</p>
        <p class="text-4xl font-bold text-gray-900">{{ $stats['drivers_active'] }}</p>
        <p class="text-xs text-blue-500 mt-1">Ready</p>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Pending Approval</p>
        <p class="text-4xl font-bold text-gray-900">{{ str_pad($stats['pending'], 2, '0', STR_PAD_LEFT) }}</p>
        <p class="text-xs text-red-500 mt-1">REQUIRES ACTION</p>
    </div>
</div>

{{-- Charts --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-1">
            <div>
                <h3 class="font-semibold text-gray-800">Grafik Pemakaian Kendaraan</h3>
                <p class="text-xs text-gray-400">Total armada pemesanan 12 bulan terakhir</p>
            </div>
            <span class="text-xs text-green-500">↗ Naik +14% bln ini</span>
        </div>
        <div id="chartMonthly" class="mt-4"></div>
    </div>
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <h3 class="font-semibold text-gray-800 mb-1">Distribusi Status Perjalanan</h3>
        <p class="text-xs text-gray-400 mb-4">Persentase status booking saat ini</p>
        <div id="chartStatus"></div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <h3 class="font-semibold text-gray-800 mb-1">Rasio Penggunaan per Kendaraan</h3>
        <p class="text-xs text-gray-400 mb-4">Frekuensi trip kendaraan operasional aktif</p>
        <div id="chartVehicle"></div>
    </div>

    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-semibold text-gray-800">8 Pemesanan Terupdate</h3>
                <p class="text-xs text-gray-400">Antrean logs pemesanan kendaraan operasional tambang</p>
            </div>
            <a href="{{ route('bookings.index') }}" class="text-xs text-yellow-600 font-medium hover:underline">Lihat Semua →</a>
        </div>
        <table class="w-full text-xs">
            <thead>
                <tr class="text-gray-400 uppercase border-b">
                    <th class="pb-2 text-left">Kode</th>
                    <th class="pb-2 text-left">Pemohon</th>
                    <th class="pb-2 text-left">Kendaraan</th>
                    <th class="pb-2 text-left">Driver</th>
                    <th class="pb-2 text-left">Tgl Mulai</th>
                    <th class="pb-2 text-left">Status</th>
                    <th class="pb-2 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($recentBookings as $b)
                <tr>
                    <td class="py-2 font-mono">{{ $b->booking_code }}</td>
                    <td class="py-2">
                        <p class="font-medium text-gray-800">{{ $b->requester->name }}</p>
                        <p class="text-gray-400 text-xs">{{ $b->requester->role }}</p>
                    </td>
                    <td class="py-2 text-gray-500">
                        <p>{{ $b->vehicle->brand }} {{ $b->vehicle->model }}</p>
                        <p class="text-gray-400">{{ $b->vehicle->plate_number }}</p>
                    </td>
                    <td class="py-2 text-gray-500">{{ $b->driver->user->name ?? '-' }}</td>
                    <td class="py-2 text-gray-500">{{ $b->start_datetime->format('d M Y, H:i') }}</td>
                    <td class="py-2">
                        @php $bc = match($b->status) {
                            'pending'=>'bg-yellow-100 text-yellow-700',
                            'approved'=>'bg-blue-100 text-blue-700',
                            'completed'=>'bg-green-100 text-green-700',
                            'rejected'=>'bg-red-100 text-red-700',
                            default=>'bg-gray-100 text-gray-600'
                        }; @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $bc }}">
                            {{ match($b->status) { 'pending'=>'Menunggu','approved'=>'Disetujui','completed'=>'Selesai','rejected'=>'Ditolak',default=>$b->status } }}
                        </span>
                    </td>
                    <td class="py-2">
                        <a href="{{ route('bookings.show', $b) }}" class="text-blue-500 hover:underline">Detail</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="py-4 text-center text-gray-400">Belum ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@else

{{-- DASHBOARD APPROVER/MANAGER --}}

{{-- Stats Approver --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Total Pemesanan</p>
        <p class="text-4xl font-bold text-gray-900">{{ $stats['total_bookings'] }}</p>
        <p class="text-xs text-green-500 mt-1">+12% bln ini</p>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Kendaraan Aktif</p>
        <p class="text-4xl font-bold text-gray-900">{{ $stats['vehicles_in_use'] }} <span class="text-lg text-gray-400">/ {{ $stats['vehicles_total'] }}</span></p>
        <p class="text-xs text-yellow-500 mt-1">{{ $stats['vehicles_total'] > 0 ? round(($stats['vehicles_in_use']/$stats['vehicles_total'])*100) : 0 }}% Capacity</p>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Driver Tersedia</p>
        <p class="text-4xl font-bold text-gray-900">{{ $stats['drivers_active'] }}</p>
        <p class="text-xs text-blue-500 mt-1">Ready</p>
    </div>
    <div class="bg-white rounded-xl p-5 shadow-sm border border-gray-100">
        <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">Pending Approval</p>
        <p class="text-4xl font-bold text-gray-900">{{ str_pad($myApprovalStats['pending'] ?? 0, 2, '0', STR_PAD_LEFT) }}</p>
        <p class="text-xs text-red-500 mt-1">REQUIRES ACTION</p>
    </div>
</div>

{{-- Charts Approver --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-1">
            <div>
                <h3 class="font-semibold text-gray-800">Grafik Pemakaian Kendaraan</h3>
                <p class="text-xs text-gray-400">Total armada pemesanan 12 bulan terakhir</p>
            </div>
            <span class="text-xs text-green-500">↗ Naik +14% bln ini</span>
        </div>
        <div id="chartMonthly" class="mt-4"></div>
    </div>
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <h3 class="font-semibold text-gray-800 mb-1">Status Approval Saya</h3>
        <p class="text-xs text-gray-400 mb-4">Ringkasan keputusan persetujuan yang telah saya buat</p>
        <div id="chartMyApproval"></div>
    </div>
</div>

{{-- Pending Approvals --}}
@if($myPendingApprovals && $myPendingApprovals->count() > 0)
<div class="bg-white rounded-xl p-6 shadow-sm border border-l-4 border-yellow-400 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h3 class="font-semibold text-gray-800">⚠ Menunggu Keputusan Anda</h3>
            <p class="text-xs text-gray-400">Booking berikut memerlukan persetujuan segera</p>
        </div>
        <a href="{{ route('approvals.index') }}" class="bg-yellow-500 text-white text-xs px-4 py-2 rounded-lg hover:bg-yellow-400 transition">
            Proses Semua →
        </a>
    </div>
    <div class="space-y-3">
        @foreach($myPendingApprovals as $approval)
        <div class="flex items-center justify-between bg-yellow-50 rounded-lg p-4 border border-yellow-100">
            <div class="flex-1">
                <p class="text-sm font-mono font-bold text-gray-800">{{ $approval->booking->booking_code }}</p>
                <p class="text-xs text-gray-500 mt-0.5">
                    {{ $approval->booking->requester->name }} →
                    {{ $approval->booking->vehicle->brand }} {{ $approval->booking->vehicle->model }} ({{ $approval->booking->vehicle->plate_number }})
                </p>
                <p class="text-xs text-gray-400 mt-0.5">
                    🕐 {{ $approval->booking->start_datetime->format('d M Y, H:i') }} |
                    📍 {{ $approval->booking->destination }}
                </p>
            </div>
            <div class="flex items-center gap-2 ml-4">
                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded font-bold">
                    LEVEL {{ $approval->level }}
                </span>
                <a href="{{ route('approvals.index') }}"
                   class="text-xs bg-gray-900 text-white px-3 py-1.5 rounded-lg hover:bg-gray-700 transition">
                    Tinjau
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Bottom: Pemesanan Terbaru + Riwayat Approval --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-semibold text-gray-800">8 Pemesanan Terupdate</h3>
                <p class="text-xs text-gray-400">Antrean logs pemesanan kendaraan operasional tambang</p>
            </div>
            <a href="{{ route('bookings.index') }}" class="text-xs text-yellow-600 font-medium hover:underline">Lihat Semua →</a>
        </div>
        <table class="w-full text-xs">
            <thead>
                <tr class="text-gray-400 uppercase border-b">
                    <th class="pb-2 text-left">Kode</th>
                    <th class="pb-2 text-left">Pemohon</th>
                    <th class="pb-2 text-left">Kendaraan</th>
                    <th class="pb-2 text-left">Driver</th>
                    <th class="pb-2 text-left">Tgl Mulai</th>
                    <th class="pb-2 text-left">Status</th>
                    <th class="pb-2 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($recentBookings as $b)
                <tr>
                    <td class="py-2 font-mono">{{ $b->booking_code }}</td>
                    <td class="py-2">
                        <p class="font-medium text-gray-800">{{ $b->requester->name }}</p>
                        <p class="text-gray-400">{{ $b->requester->role }}</p>
                    </td>
                    <td class="py-2 text-gray-500">
                        <p>{{ $b->vehicle->brand }} {{ $b->vehicle->model }}</p>
                        <p class="text-gray-400">{{ $b->vehicle->plate_number }}</p>
                    </td>
                    <td class="py-2 text-gray-500">{{ $b->driver->user->name ?? '-' }}</td>
                    <td class="py-2 text-gray-500">{{ $b->start_datetime->format('d M Y, H:i') }}</td>
                    <td class="py-2">
                        @php $bc = match($b->status) {
                            'pending'=>'bg-yellow-100 text-yellow-700',
                            'approved'=>'bg-blue-100 text-blue-700',
                            'completed'=>'bg-green-100 text-green-700',
                            'rejected'=>'bg-red-100 text-red-700',
                            default=>'bg-gray-100 text-gray-600'
                        }; @endphp
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $bc }}">
                            {{ match($b->status) { 'pending'=>'Menunggu','approved'=>'Disetujui','completed'=>'Selesai','rejected'=>'Ditolak',default=>$b->status } }}
                        </span>
                    </td>
                    <td class="py-2">
                        <a href="{{ route('bookings.show', $b) }}" class="text-blue-500 hover:underline">Detail</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="py-4 text-center text-gray-400">Belum ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="font-semibold text-gray-800">Riwayat Persetujuan Saya</h3>
                <p class="text-xs text-gray-400">5 keputusan terakhir yang saya buat</p>
            </div>
            <a href="{{ route('approvals.index') }}" class="text-xs text-yellow-600 font-medium hover:underline">Lihat Semua →</a>
        </div>
        <div class="space-y-3">
            @forelse($myRecentApprovals as $a)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div>
                    <p class="text-xs font-mono font-bold text-gray-800">{{ $a->booking->booking_code }}</p>
                    <p class="text-xs text-gray-500">{{ $a->booking->requester->name }} — {{ $a->booking->vehicle->plate_number }}</p>
                    <p class="text-xs text-gray-400">Level {{ $a->level }} · {{ $a->updated_at->format('d M Y') }}</p>
                </div>
                <span class="text-xs font-bold px-2 py-1 rounded-full
                    {{ $a->status === 'approved' ? 'bg-green-100 text-green-700' :
                       ($a->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                    {{ strtoupper($a->status) }}
                </span>
            </div>
            @empty
            <p class="text-xs text-gray-400 text-center py-4">Belum ada riwayat persetujuan</p>
            @endforelse
        </div>
    </div>
</div>

@endif

{{-- Charts Script --}}
@php
$statusLabels = $statusDist->keys()->map(fn($k) => match($k) {
    'pending'   => 'Menunggu',
    'approved'  => 'Disetujui',
    'completed' => 'Selesai',
    'rejected'  => 'Ditolak',
    default     => $k,
})->values();
@endphp

<script>
new ApexCharts(document.getElementById('chartMonthly'), {
    chart: { type: 'area', height: 200, toolbar: { show: false } },
    series: [{ name: 'Pemesanan', data: @json($monthlyUsage->pluck('total')) }],
    xaxis: { categories: @json($monthlyUsage->pluck('label')), labels: { style: { fontSize: '10px' } } },
    colors: ['#F59E0B'],
    fill: { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0.05 } },
    stroke: { curve: 'smooth', width: 2 },
    dataLabels: { enabled: false },
    grid: { strokeDashArray: 4 },
}).render();

@if(auth()->user()->isAdmin())
new ApexCharts(document.getElementById('chartStatus'), {
    chart: { type: 'donut', height: 220 },
    series: @json($statusDist->values()),
    labels: @json($statusLabels),
    colors: ['#F59E0B', '#3B82F6', '#10B981', '#EF4444'],
    legend: { position: 'bottom', fontSize: '11px' },
    dataLabels: { enabled: true },
}).render();

new ApexCharts(document.getElementById('chartVehicle'), {
    chart: { type: 'bar', height: 200, toolbar: { show: false } },
    series: [{ name: 'Trip', data: @json($vehicleUsage->pluck('total')) }],
    xaxis: { categories: @json($vehicleUsage->pluck('plate_number')), labels: { style: { fontSize: '10px' } } },
    plotOptions: { bar: { horizontal: true, borderRadius: 3 } },
    colors: ['#3B82F6'],
    dataLabels: { enabled: false },
    grid: { strokeDashArray: 4 },
}).render();
@else
new ApexCharts(document.getElementById('chartMyApproval'), {
    chart: { type: 'donut', height: 220 },
    series: [
        {{ $myApprovalStats['pending'] ?? 0 }},
        {{ $myApprovalStats['approved'] ?? 0 }},
        {{ $myApprovalStats['rejected'] ?? 0 }}
    ],
    labels: ['Menunggu', 'Disetujui', 'Ditolak'],
    colors: ['#F59E0B', '#10B981', '#EF4444'],
    legend: { position: 'bottom', fontSize: '11px' },
    dataLabels: { enabled: true },
}).render();
@endif
</script>
@endsection