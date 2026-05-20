@extends('layouts.app')
@section('title', 'Verifikasi Persetujuan')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="{ selectedApproval: null }">

    {{-- Kolom Kiri — List --}}
    <div class="lg:col-span-2 space-y-4">

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="text-xl">✅</span>
                <div>
                    <h2 class="font-bold text-gray-800">Kotak Masuk Persetujuan</h2>
                    <p class="text-xs text-gray-400">
                        Menampilkan booking menunggu keputusan
                        {{ auth()->user()->isAdmin() ? 'Admin' : 'Approver' }}
                    </p>
                </div>
            </div>
            <span class="bg-gray-900 text-white text-xs px-4 py-2 rounded-lg">Antrean</span>
        </div>

        @forelse($approvals as $approval)
        <div
            @click="selectedApproval = {{ $approval->id }}"
            class="bg-white rounded-xl border shadow-sm p-5 cursor-pointer transition
                   {{ $approval->isPending() ? 'border-l-4 border-l-yellow-400 border-gray-100' : 'border-gray-100' }}"
            :class="selectedApproval === {{ $approval->id }} ? 'ring-2 ring-yellow-400' : 'hover:border-yellow-200'">

            <div class="flex items-start justify-between mb-3">
                <span class="font-mono text-sm font-semibold text-gray-800">{{ $approval->booking->booking_code }}</span>
                <span class="text-xs text-gray-500 uppercase bg-gray-100 px-2 py-1 rounded">
                    {{ $approval->booking->requester->role }}
                </span>
            </div>

            <div class="grid grid-cols-2 gap-2 text-sm text-gray-600 mb-3">
                <div>Pemohon: <strong>{{ $approval->booking->requester->name }}</strong></div>
                <div>Armada: <strong>{{ $approval->booking->vehicle->brand }} {{ $approval->booking->vehicle->model }}</strong>
                    <span class="text-xs text-gray-400">({{ $approval->booking->vehicle->plate_number }})</span>
                </div>
                <div>Driver Mandat: <strong>{{ $approval->booking->driver->user->name ?? '-' }}</strong></div>
                <div>Tujuan: <strong>{{ $approval->booking->destination }}</strong></div>
            </div>

            <div class="flex gap-3 text-xs mb-3 flex-wrap">
                @foreach($approval->booking->approvals as $a)
                <span class="flex items-center gap-1 px-3 py-1 rounded-full border
                    {{ $a->status === 'approved' ? 'border-green-300 text-green-600 bg-green-50' :
                       ($a->status === 'rejected' ? 'border-red-300 text-red-600 bg-red-50' :
                       'border-gray-200 text-gray-500 bg-gray-50') }}">
                    <span class="w-4 h-4 rounded-full bg-gray-200 flex items-center justify-center font-bold text-gray-700 text-xs">{{ $a->level }}</span>
                    Level {{ $a->level }}: {{ ucfirst($a->status) }}
                </span>
                @endforeach
            </div>

            @if(!$approval->isPending())
            <p class="text-xs font-semibold {{ $approval->status === 'approved' ? 'text-green-600' : 'text-red-600' }}">
                ✓ Sudah diproses: {{ strtoupper($approval->status) }}
            </p>
            @endif
        </div>
        @empty
        <div class="bg-white rounded-xl border border-gray-100 p-12 text-center">
            <span class="text-4xl">📭</span>
            <p class="text-gray-400 mt-3">Tidak ada persetujuan</p>
        </div>
        @endforelse

        {{-- Matriks --}}
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center gap-2 mb-4">
                <span class="text-green-600">🛡</span>
                <h3 class="font-semibold text-gray-800">Matriks Kebijakan Approval Berjenjang</h3>
            </div>
            <ul class="text-sm text-gray-600 space-y-2">
                @foreach($approvals as $a)
                @if($loop->first)
                <li><strong>Level 1 (Kepala Seksi):</strong>
                    {{ $a->booking->approvalLevel1?->approver->name ?? 'Manager' }}
                    meninjau aspek operasional di site tambang.
                </li>
                <li><strong>Level 2 (Kepala Departemen):</strong>
                    {{ $a->booking->approvalLevel2?->approver->name ?? 'Admin' }}
                    menetapkan keputusan final setelah Level 1 menyetujui.
                </li>
                @endif
                @endforeach
                <li class="text-xs text-gray-400 mt-2">
                    <strong>Aturan Penting:</strong> Level 2 tidak diperbolehkan memberikan keputusan sebelum Level 1 menyelesaikan persetujuan.
                    Jika Level 1 melakukan <strong>Penolakan</strong>, maka Level 2 otomatis berstatus ditolak pula (Cascade Rejection).
                </li>
            </ul>
        </div>

        <div>{{ $approvals->links() }}</div>
    </div>

    {{-- Kolom Kanan — Panel Detail --}}
    <div class="lg:col-span-1">

        @foreach($approvals as $approval)
        @php
            $canAct = auth()->user()->isApprover()
                      && $approval->approver_id === auth()->id()
                      && $approval->isPending();

            if ($canAct && $approval->level === 2) {
                $level1 = $approval->booking->approvalLevel1;
                $canAct = $level1 && $level1->status === 'approved';
            }
        @endphp

        <div x-show="selectedApproval === {{ $approval->id }}"
             x-cloak
             class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 sticky top-6">

            <div class="flex items-center justify-between mb-5">
                <div>
                    <p class="text-xs text-gray-400 uppercase tracking-wide">Tinjau Jadwal</p>
                    <p class="font-mono font-bold text-gray-800">{{ $approval->booking->booking_code }}</p>
                </div>
                <button @click="selectedApproval = null" class="text-gray-400 hover:text-gray-600 text-lg">✕</button>
            </div>

            {{-- Rute --}}
            <div class="bg-gray-50 rounded-xl p-4 mb-4">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-semibold text-gray-500 uppercase">Rute Perjalanan</span>
                    <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded font-bold">
                        {{ $approval->booking->passenger_count }} PENUMPANG
                    </span>
                </div>
                <div class="space-y-2">
                    <div class="flex items-start gap-2">
                        <span class="w-5 h-5 rounded-full bg-gray-800 text-white text-xs flex items-center justify-center shrink-0 mt-0.5">A</span>
                        <div>
                            <p class="text-xs text-gray-400">Mulai perjalanan</p>
                            <p class="text-sm font-medium text-gray-800">{{ $approval->booking->destination }}</p>
                        </div>
                    </div>
                    <div class="ml-2.5 border-l-2 border-dashed border-gray-300 h-4"></div>
                    <div class="flex items-start gap-2">
                        <span class="w-5 h-5 rounded-full bg-green-600 text-white text-xs flex items-center justify-center shrink-0 mt-0.5">B</span>
                        <div>
                            <p class="text-xs text-gray-400">Lokasi tujuan akhir</p>
                            <p class="text-sm font-medium text-gray-800">{{ $approval->booking->destination }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Waktu --}}
            <div class="bg-blue-50 rounded-lg px-4 py-3 mb-4 text-xs text-blue-800">
                <p class="font-semibold mb-1">Waktu Jadwal Perjalanan</p>
                <p>{{ $approval->booking->start_datetime->format('d M Y, H.i') }} s/d {{ $approval->booking->end_datetime->format('d M Y, H.i') }}</p>
            </div>

            {{-- Info --}}
            <div class="space-y-3 mb-5 text-sm">
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <p class="text-xs text-gray-400">Nama Pemohon</p>
                        <p class="font-semibold text-gray-800">{{ $approval->booking->requester->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Departemen</p>
                        <p class="font-semibold text-gray-800 capitalize">{{ $approval->booking->requester->role }}</p>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-400 mb-1">Kendaraan Tambang</p>
                    <p class="font-semibold text-gray-800">{{ $approval->booking->vehicle->brand }} {{ $approval->booking->vehicle->model }}</p>
                    <p class="text-xs text-gray-400">{{ $approval->booking->vehicle->plate_number }}</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-3">
                    <p class="text-xs text-gray-400 mb-1">Driver Operasional</p>
                    <p class="font-semibold text-gray-800">{{ $approval->booking->driver->user->name ?? '-' }}</p>
                    @if($approval->booking->driver->user?->phone)
                    <p class="text-xs text-gray-400">{{ $approval->booking->driver->user->phone }}</p>
                    @endif
                </div>

                <div>
                    <p class="text-xs text-gray-400 mb-1">Alasan Perjalanan / Kebutuhan</p>
                    <p class="text-sm text-gray-700 italic bg-gray-50 rounded-lg px-3 py-2">
                        "{{ $approval->booking->purpose }}"
                    </p>
                </div>
            </div>

            {{-- ACTION SECTION --}}
            @if($canAct)
            {{-- Approver yang berwenang: tampilkan form --}}
            <div class="border-t pt-4">
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">
                    Berikan Catatan Review/Alasan
                </p>

                <textarea id="notes-{{ $approval->id }}"
                          rows="3"
                          placeholder="Tuliskan catatan persetujuan atau alasan penolakan..."
                          class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 mb-4 resize-none"></textarea>

                <div class="grid grid-cols-2 gap-3">
                    {{-- TOLAK --}}
                    <form method="POST" action="{{ route('approvals.reject', $approval) }}">
                        @csrf
                        <input type="hidden" name="notes" id="reject-notes-{{ $approval->id }}">
                        <button type="submit"
                                onclick="document.getElementById('reject-notes-{{ $approval->id }}').value = document.getElementById('notes-{{ $approval->id }}').value"
                                class="w-full flex items-center justify-center gap-2 border-2 border-red-300 text-red-600 py-3 rounded-xl font-semibold text-sm hover:bg-red-50 transition">
                            ✕ TOLAK
                        </button>
                    </form>

                    {{-- SETUJUI --}}
                    <form method="POST" action="{{ route('approvals.approve', $approval) }}">
                        @csrf
                        <input type="hidden" name="notes" id="approve-notes-{{ $approval->id }}">
                        <button type="submit"
                                onclick="document.getElementById('approve-notes-{{ $approval->id }}').value = document.getElementById('notes-{{ $approval->id }}').value"
                                class="w-full flex items-center justify-center gap-2 bg-yellow-500 text-white py-3 rounded-xl font-semibold text-sm hover:bg-yellow-400 transition">
                            ✓ SETUJUI
                        </button>
                    </form>
                </div>
            </div>

            @elseif($approval->isPending() && auth()->user()->isAdmin())
            {{-- Admin: lihat status saja --}}
            <div class="border-t pt-4">
                <div class="bg-blue-50 rounded-lg p-4">
                    <p class="text-xs font-semibold text-blue-700 mb-2">ℹ Status Persetujuan</p>
                    <div class="space-y-2">
                        @foreach($approval->booking->approvals as $a)
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-600">Level {{ $a->level }}: {{ $a->approver->name }}</span>
                            <span class="font-bold px-2 py-0.5 rounded-full
                                {{ $a->status === 'approved' ? 'bg-green-100 text-green-700' :
                                   ($a->status === 'rejected' ? 'bg-red-100 text-red-700' :
                                   'bg-yellow-100 text-yellow-700') }}">
                                {{ strtoupper($a->status) }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                    <p class="text-xs text-blue-500 mt-3">
                        💡 Gunakan Switch Account untuk beralih ke akun Approver.
                    </p>
                </div>
            </div>

            @elseif($approval->isPending() && $approval->level === 2 && $approval->booking->approvalLevel1?->status !== 'approved')
            {{-- Level 2 menunggu Level 1 --}}
            <div class="border-t pt-4">
                <div class="bg-yellow-50 rounded-lg p-4 text-center">
                    <span class="text-2xl">⏳</span>
                    <p class="text-xs font-semibold text-yellow-700 mt-2">Menunggu Persetujuan Level 1</p>
                    <p class="text-xs text-yellow-600 mt-1">
                        {{ $approval->booking->approvalLevel1?->approver->name ?? 'Approver Level 1' }}
                        belum memberikan keputusan.
                    </p>
                </div>
            </div>

            @elseif(!$approval->isPending())
            {{-- Sudah diproses --}}
            <div class="border-t pt-4 text-center">
                <span class="text-3xl">{{ $approval->status === 'approved' ? '✅' : '❌' }}</span>
                <p class="text-sm font-bold mt-2 {{ $approval->status === 'approved' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $approval->status === 'approved' ? 'Sudah Disetujui' : 'Sudah Ditolak' }}
                </p>
                @if($approval->notes)
                <p class="text-xs text-gray-400 mt-1 italic">"{{ $approval->notes }}"</p>
                @endif
            </div>
            @endif

        </div>
        @endforeach

        {{-- Empty state --}}
        <div x-show="selectedApproval === null"
             class="bg-white rounded-xl border border-gray-100 shadow-sm p-8 text-center sticky top-6">
            <span class="text-4xl">✅</span>
            <p class="text-sm font-semibold text-gray-500 mt-3 uppercase tracking-wide">Kotak Peninjauan</p>
            <p class="text-xs text-gray-400 mt-2 leading-relaxed">
                Pilih salah satu reservasi di samping kiri untuk mengkaji parameter tugas dinas.
            </p>
        </div>
    </div>

</div>

<style>[x-cloak] { display: none !important; }</style>
@endsection