@extends('layouts.app')
@section('title', 'Log Aktivitas')

@section('content')

<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">

    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h2 class="font-semibold text-gray-700">Log Aktivitas Sistem</h2>
            <p class="text-xs text-gray-400 mt-0.5">Rekam jejak semua aktivitas pengguna di aplikasi</p>
        </div>
        <span class="text-xs text-gray-400 bg-gray-100 px-3 py-1.5 rounded-lg font-mono">
            Total: {{ $logs->total() }} entri
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                <tr>
                    <th class="px-5 py-3 text-left">Waktu</th>
                    <th class="px-5 py-3 text-left">User</th>
                    <th class="px-5 py-3 text-left">Module</th>
                    <th class="px-5 py-3 text-left">Action</th>
                    <th class="px-5 py-3 text-left">Detail</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50 transition-colors">

                    {{-- Waktu --}}
                    <td class="px-5 py-3 whitespace-nowrap">
                        <p class="text-xs font-medium text-gray-700">{{ $log->created_at->format('d M Y') }}</p>
                        <p class="text-xs text-gray-400 font-mono">{{ $log->created_at->format('H:i:s') }}</p>
                    </td>

                    {{-- User --}}
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 shrink-0">
                                {{ strtoupper(substr($log->user->name ?? 'S', 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-medium text-gray-800 text-xs">{{ $log->user->name ?? 'System' }}</p>
                                <p class="text-xs text-gray-400 capitalize">{{ $log->user->role ?? '-' }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Module --}}
                    <td class="px-5 py-3">
                        @php
                        $moduleColor = match($log->module) {
                            'Booking'  => 'bg-blue-100 text-blue-700',
                            'Approval' => 'bg-purple-100 text-purple-700',
                            'Vehicle'  => 'bg-orange-100 text-orange-700',
                            'Driver'   => 'bg-cyan-100 text-cyan-700',
                            'Report'   => 'bg-green-100 text-green-700',
                            'Dashboard'=> 'bg-gray-100 text-gray-600',
                            default    => 'bg-gray-100 text-gray-600',
                        };
                        @endphp
                        <span class="px-2 py-1 rounded-md text-xs font-semibold {{ $moduleColor }}">
                            {{ $log->module }}
                        </span>
                    </td>

                    {{-- Action --}}
                    <td class="px-5 py-3">
                        @php
                        $actionColor = match($log->action) {
                            'CREATE'   => 'bg-green-100 text-green-700',
                            'UPDATE'   => 'bg-yellow-100 text-yellow-700',
                            'DELETE'   => 'bg-red-100 text-red-700',
                            'APPROVE'  => 'bg-green-100 text-green-700',
                            'REJECT'   => 'bg-red-100 text-red-700',
                            'EXPORT'   => 'bg-indigo-100 text-indigo-700',
                            'CANCEL'   => 'bg-red-100 text-red-700',
                            'COMPLETE' => 'bg-teal-100 text-teal-700',
                            'VIEW'     => 'bg-gray-100 text-gray-500',
                            default    => 'bg-gray-100 text-gray-600',
                        };
                        @endphp
                        <span class="px-2 py-1 rounded-md text-xs font-bold {{ $actionColor }}">
                            {{ $log->action }}
                        </span>
                    </td>

                    {{-- Payload --}}
                    <td class="px-5 py-3 max-w-xs">
                        @if($log->payload)
                        <div class="text-xs text-gray-500 font-mono bg-gray-50 rounded px-2 py-1 truncate max-w-[200px]"
                             title="{{ json_encode($log->payload) }}">
                            {{ json_encode($log->payload) }}
                        </div>
                        @else
                        <span class="text-gray-300 text-xs">—</span>
                        @endif
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-16 text-center">
                        <span class="text-4xl block mb-3">📋</span>
                        <p class="text-gray-400 text-sm">Belum ada log aktivitas</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($logs->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $logs->links() }}
    </div>
    @endif

</div>
@endsection