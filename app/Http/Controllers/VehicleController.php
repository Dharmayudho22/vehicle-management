<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\AppLog;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::withCount('bookings')->latest()->paginate(15);
        AppLog::record('VIEW', 'Vehicle');
        return view('vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        return view('vehicles.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate_number'     => 'required|string|unique:vehicles,plate_number',
            'brand'            => 'required|string|max:100',
            'model'            => 'required|string|max:100',
            'type'             => 'required|string|max:100',
            'ownership'        => 'required|in:owned,leased,rental',
            'fuel_consumption' => 'nullable|numeric|min:0',
            'last_service'     => 'nullable|date',
            'next_service'     => 'nullable|date|after_or_equal:last_service',
        ]);

        $vehicle = Vehicle::create($validated);
        AppLog::record('CREATE', 'Vehicle', ['vehicle_id' => $vehicle->id]);

        return redirect()->route('vehicles.index')
            ->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['bookings.requester', 'fuelLogs', 'serviceLogs']);
        return view('vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        return view('vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'plate_number'     => 'required|string|unique:vehicles,plate_number,' . $vehicle->id,
            'brand'            => 'required|string|max:100',
            'model'            => 'required|string|max:100',
            'type'             => 'required|string|max:100',
            'ownership'        => 'required|in:owned,leased,rental',
            'status'           => 'required|in:available,in_use,maintenance',
            'fuel_consumption' => 'nullable|numeric|min:0',
            'last_service'     => 'nullable|date',
            'next_service'     => 'nullable|date',
        ]);

        $vehicle->update($validated);
        AppLog::record('UPDATE', 'Vehicle', ['vehicle_id' => $vehicle->id]);

        return redirect()->route('vehicles.index')
            ->with('success', 'Kendaraan berhasil diperbarui.');
    }

    public function destroy(Vehicle $vehicle)
    {
        abort_if(
            $vehicle->bookings()->whereIn('status', ['approved', 'pending'])->exists(),
            403,
            'Kendaraan sedang digunakan.'
        );

        AppLog::record('DELETE', 'Vehicle', ['vehicle_id' => $vehicle->id]);
        $vehicle->delete();

        return redirect()->route('vehicles.index')
            ->with('success', 'Kendaraan berhasil dihapus.');
    }
}
