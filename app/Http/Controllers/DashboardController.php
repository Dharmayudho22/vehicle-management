<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Approval;
use App\Models\AppLog;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = auth()->guard('web')->user();

        // Stats umum
        $stats = [
            'total_bookings'  => Booking::count(),
            'pending'         => Booking::where('status', 'pending')->count(),
            'approved'        => Booking::where('status', 'approved')->count(),
            'completed'       => Booking::where('status', 'completed')->count(),
            'vehicles_total'  => Vehicle::count(),
            'vehicles_in_use' => Vehicle::where('status', 'in_use')->count(),
            'drivers_active'  => Driver::where('status', 'active')->count(),
        ];

        // Chart monthly — ganti YEAR/MONTH dengan EXTRACT
        $monthlyUsage = Booking::select(
                DB::raw('EXTRACT(YEAR FROM start_datetime) as year'),
                DB::raw('EXTRACT(MONTH FROM start_datetime) as month'),
                DB::raw('COUNT(*) as total')
            )
            ->where('start_datetime', '>=', now()->subMonths(12))
            ->groupBy(DB::raw('EXTRACT(YEAR FROM start_datetime)'), DB::raw('EXTRACT(MONTH FROM start_datetime)'))
            ->orderBy('year')->orderBy('month')
            ->get()
            ->map(fn($row) => [
                'label' => Carbon::create((int)$row->year, (int)$row->month)->format('M Y'),
                'total' => $row->total,
            ]);

        // Chart per kendaraan — ganti whereMonth
        $vehicleUsage = Booking::select('vehicles.plate_number', DB::raw('COUNT(*) as total'))
            ->join('vehicles', 'bookings.vehicle_id', '=', 'vehicles.id')
            ->where('bookings.status', 'completed')
            ->whereRaw('EXTRACT(MONTH FROM bookings.start_datetime) = ?', [now()->month])
            ->groupBy('vehicles.id', 'vehicles.plate_number')
            ->orderByDesc('total')
            ->get();

        // Chart status
        $statusDist = Booking::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // Recent bookings
        $recentBookings = Booking::with(['vehicle', 'requester', 'driver.user'])
            ->latest()->limit(8)->get();

        // Data khusus approver
        $myPendingApprovals = null;
        $myApprovalStats    = null;
        $myRecentApprovals  = null;

        if ($user->isApprover()) {
            $myPendingApprovals = Approval::with(['booking.vehicle', 'booking.requester', 'booking.driver.user', 'booking.approvals'])
                ->where('approver_id', $user->id)
                ->where('status', 'pending')
                ->latest()
                ->get();

            $myApprovalStats = [
                'pending'  => Approval::where('approver_id', $user->id)->where('status', 'pending')->count(),
                'approved' => Approval::where('approver_id', $user->id)->where('status', 'approved')->count(),
                'rejected' => Approval::where('approver_id', $user->id)->where('status', 'rejected')->count(),
                'total'    => Approval::where('approver_id', $user->id)->count(),
            ];

            $myRecentApprovals = Approval::with(['booking.vehicle', 'booking.requester'])
                ->where('approver_id', $user->id)
                ->latest()->limit(5)->get();
        }

        AppLog::record('VIEW', 'Dashboard');

        return view('dashboard.index', compact(
            'stats', 'monthlyUsage', 'vehicleUsage', 'statusDist', 'recentBookings',
            'myPendingApprovals', 'myApprovalStats', 'myRecentApprovals'
        ));
    }
}