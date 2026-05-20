<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\User;
use App\Models\Approval;
use App\Models\AppLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['requester', 'vehicle', 'driver.user', 'approvals.approver'])
            ->latest();

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->date_from) {
            $query->whereDate('start_datetime', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('start_datetime', '<=', $request->date_to);
        }

        $bookings = $query->paginate(15)->withQueryString();
        $vehicles = Vehicle::all();

        AppLog::record('VIEW', 'Booking');

        return view('bookings.index', compact('bookings', 'vehicles'));
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = auth()->guard('web')->user();

        $vehicles  = Vehicle::available()->get();
        $drivers   = Driver::with('user')->active()->get();
        $approvers = User::whereIn('role', ['admin', 'manager'])
            ->where('id', '!=', $user->id)
            ->get();

        return view('bookings.create', compact('vehicles', 'drivers', 'approvers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id'      => 'required|exists:vehicles,id',
            'driver_id'       => 'required|exists:drivers,id',
            'approver1_id'    => 'required|exists:users,id|different:approver2_id',
            'approver2_id'    => 'required|exists:users,id',
            'start_datetime'  => 'required|date|after:now',
            'end_datetime'    => 'required|date|after:start_datetime',
            'destination'     => 'required|string|max:255',
            'purpose'         => 'required|string',
            'passenger_count' => 'required|integer|min:1',
        ]);

        // Cek konflik kendaraan
        $conflict = Booking::where('vehicle_id', $validated['vehicle_id'])
            ->whereIn('status', ['approved', 'pending'])
            ->where(function ($q) use ($validated) {
                $q->whereBetween('start_datetime', [
                        $validated['start_datetime'],
                        $validated['end_datetime']
                    ])
                  ->orWhereBetween('end_datetime', [
                        $validated['start_datetime'],
                        $validated['end_datetime']
                    ]);
            })->exists();

        if ($conflict) {
            return back()
                ->withErrors(['vehicle_id' => 'Kendaraan sudah dipesan pada waktu tersebut.'])
                ->withInput();
        }

        DB::transaction(function () use ($validated) {
            /** @var \App\Models\User $user */
            $user = auth()->guard('web')->user();

            $booking = Booking::create([
                'booking_code'    => Booking::generateCode(),
                'requester_id'    => $user->id,
                'vehicle_id'      => $validated['vehicle_id'],
                'driver_id'       => $validated['driver_id'],
                'start_datetime'  => $validated['start_datetime'],
                'end_datetime'    => $validated['end_datetime'],
                'destination'     => $validated['destination'],
                'purpose'         => $validated['purpose'],
                'passenger_count' => $validated['passenger_count'],
                'status'          => 'pending',
            ]);

            // Approval berjenjang 2 level
            Approval::create([
                'booking_id'  => $booking->id,
                'approver_id' => $validated['approver1_id'],
                'level'       => 1,
                'status'      => 'pending',
            ]);

            Approval::create([
                'booking_id'  => $booking->id,
                'approver_id' => $validated['approver2_id'],
                'level'       => 2,
                'status'      => 'pending',
            ]);

            AppLog::record('CREATE', 'Booking', [
                'booking_code' => $booking->booking_code,
                'vehicle_id'   => $booking->vehicle_id,
            ]);
        });

        return redirect()->route('bookings.index')
            ->with('success', 'Pemesanan berhasil dibuat dan menunggu persetujuan.');
    }

    public function show(Booking $booking)
    {
        $booking->load(['requester', 'vehicle', 'driver.user', 'approvals.approver']);
        AppLog::record('VIEW', 'Booking', ['booking_id' => $booking->id]);
        return view('bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        abort_unless($booking->isEditable(), 403, 'Pemesanan tidak dapat diedit.');
        
        /** @var \App\Models\User $user */
        $user = auth()->guard('web')->user();

        $vehicles  = Vehicle::available()->get();
        $drivers   = Driver::with('user')->active()->get();
        $approvers = User::whereIn('role', ['admin', 'manager'])
            ->where('id', '!=', $user->id)
            ->get();

        return view('bookings.edit', compact('booking', 'vehicles', 'drivers', 'approvers'));
    }

    public function update(Request $request, Booking $booking)
    {
        abort_unless($booking->isEditable(), 403, 'Pemesanan tidak dapat diedit.');

        $validated = $request->validate([
            'vehicle_id'      => 'required|exists:vehicles,id',
            'driver_id'       => 'nullable|exists:drivers,id',
            'start_datetime'  => 'required|date|after:now',
            'end_datetime'    => 'required|date|after:start_datetime',
            'destination'     => 'required|string|max:255',
            'purpose'         => 'required|string',
            'passenger_count' => 'required|integer|min:1',
        ]);

        $booking->update($validated);
        AppLog::record('UPDATE', 'Booking', ['booking_id' => $booking->id]);

        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Pemesanan berhasil diperbarui.');
    }

    public function destroy(Booking $booking)
    {
        abort_unless($booking->isEditable(), 403, 'Pemesanan tidak dapat dibatalkan.');

        $booking->update(['status' => 'rejected']);
        AppLog::record('CANCEL', 'Booking', ['booking_id' => $booking->id]);

        return redirect()->route('bookings.index')
            ->with('success', 'Pemesanan berhasil dibatalkan.');
    }

    public function complete(Request $request, Booking $booking)
    {
        abort_unless($booking->status === 'approved', 403);

        $booking->update(['status' => 'completed']);
        $booking->vehicle->update(['status' => 'available']);

        AppLog::record('COMPLETE', 'Booking', ['booking_id' => $booking->id]);

        return back()->with('success', 'Pemesanan selesai.');
    }
}
