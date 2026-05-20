<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\User;
use App\Models\AppLog;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function index()
    {
        $drivers = Driver::with('user')->latest()->paginate(15);
        AppLog::record('VIEW', 'Driver');
        return view('drivers.index', compact('drivers'));
    }

    public function create()
    {
        // User dengan role driver yang belum punya data driver
        $users = User::where('role', 'driver')
            ->whereDoesntHave('driver')
            ->get();

        return view('drivers.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'        => 'required|exists:users,id|unique:drivers,user_id',
            'license_number' => 'required|string|unique:drivers,license_number',
            'license_type'   => 'required|string|max:10',
        ]);

        $driver = Driver::create($validated);
        AppLog::record('CREATE', 'Driver', ['driver_id' => $driver->id]);

        return redirect()->route('drivers.index')
            ->with('success', 'Driver berhasil ditambahkan.');
    }

    public function show(Driver $driver)
    {
        $driver->load(['user', 'bookings.vehicle']);
        return view('drivers.show', compact('driver'));
    }

    public function edit(Driver $driver)
    {
        $users = User::where('role', 'driver')->get();
        return view('drivers.edit', compact('driver', 'users'));
    }

    public function update(Request $request, Driver $driver)
    {
        $validated = $request->validate([
            'license_number' => 'required|string|unique:drivers,license_number,' . $driver->id,
            'license_type'   => 'required|string|max:10',
            'status'         => 'required|in:active,inactive',
        ]);

        $driver->update($validated);
        AppLog::record('UPDATE', 'Driver', ['driver_id' => $driver->id]);

        return redirect()->route('drivers.index')
            ->with('success', 'Driver berhasil diperbarui.');
    }

    public function destroy(Driver $driver)
    {
        abort_if(
            $driver->bookings()->whereIn('status', ['approved', 'pending'])->exists(),
            403,
            'Driver sedang bertugas.'
        );

        AppLog::record('DELETE', 'Driver', ['driver_id' => $driver->id]);
        $driver->delete();

        return redirect()->route('drivers.index')
            ->with('success', 'Driver berhasil dihapus.');
    }
}
