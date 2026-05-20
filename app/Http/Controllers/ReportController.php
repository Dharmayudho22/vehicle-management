<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\AppLog;
use App\Exports\BookingsExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $dateFrom = $request->date_from ?? now()->startOfMonth()->format('Y-m-d');
        $dateTo   = $request->date_to   ?? now()->format('Y-m-d');

        $query = Booking::with(['requester', 'vehicle', 'driver.user', 'approvals.approver'])
            ->whereBetween('created_at', [$dateFrom, $dateTo . ' 23:59:59'])
            ->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $bookings = $query->get();

        $summary = [
            'total'     => $bookings->count(),
            'approved'  => $bookings->where('status', 'approved')->count(),
            'completed' => $bookings->where('status', 'completed')->count(),
            'rejected'  => $bookings->where('status', 'rejected')->count(),
            'pending'   => $bookings->where('status', 'pending')->count(),
        ];

        AppLog::record('VIEW', 'Report');

        return view('reports.index', compact('bookings', 'summary', 'dateFrom', 'dateTo'));
    }

    public function export(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to'   => 'required|date|after_or_equal:date_from',
        ]);

        $filename = 'laporan-pemesanan-' . $request->date_from . '-sd-' . $request->date_to . '.xlsx';

        AppLog::record('EXPORT', 'Report', [
            'date_from' => $request->date_from,
            'date_to'   => $request->date_to,
        ]);

        return Excel::download(
            new BookingsExport($request->date_from, $request->date_to, $request->status),
            $filename
        );
    }
}
