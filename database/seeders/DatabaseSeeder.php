<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Booking;
use App\Models\Approval;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \App\Models\Approval::truncate();
        \App\Models\Booking::truncate();
        \App\Models\Driver::truncate();
        \App\Models\Vehicle::truncate();
        \App\Models\User::truncate();
        \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // ══════════════════════════════════════
        // USERS
        // ══════════════════════════════════════
        $admin = User::create([
            'name'     => 'Admin Pool',
            'email'    => 'admin@nikel.co.id',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        $manager1 = User::create([
            'name'     => 'Budi Santoso',
            'email'    => 'manager1@nikel.co.id',
            'password' => Hash::make('password'),
            'role'     => 'manager',
        ]);

        $manager2 = User::create([
            'name'     => 'Siti Rahayu',
            'email'    => 'manager2@nikel.co.id',
            'password' => Hash::make('password'),
            'role'     => 'manager',
        ]);

        $driverUser1 = User::create([
            'name'     => 'Ahmad Fauzi',
            'email'    => 'driver1@nikel.co.id',
            'password' => Hash::make('password'),
            'role'     => 'driver',
        ]);

        $driverUser2 = User::create([
            'name'     => 'Rudi Hermawan',
            'email'    => 'driver2@nikel.co.id',
            'password' => Hash::make('password'),
            'role'     => 'driver',
        ]);

        $driverUser3 = User::create([
            'name'     => 'Hendra Wijaya',
            'email'    => 'driver3@nikel.co.id',
            'password' => Hash::make('password'),
            'role'     => 'driver',
        ]);

        // ══════════════════════════════════════
        // DRIVERS
        // ══════════════════════════════════════
        $driver1 = Driver::create([
            'user_id'        => $driverUser1->id,
            'license_number' => 'SIM-B1-001-2022',
            'license_type'   => 'B1',
            'status'         => 'active',
        ]);

        $driver2 = Driver::create([
            'user_id'        => $driverUser2->id,
            'license_number' => 'SIM-B1-002-2022',
            'license_type'   => 'B1',
            'status'         => 'active',
        ]);

        $driver3 = Driver::create([
            'user_id'        => $driverUser3->id,
            'license_number' => 'SIM-B2-003-2021',
            'license_type'   => 'B2',
            'status'         => 'active',
        ]);

        // ══════════════════════════════════════
        // VEHICLES
        // ══════════════════════════════════════
        $vehicle1 = Vehicle::create([
            'plate_number'    => 'DT 1234 AB',
            'brand'           => 'Toyota',
            'model'           => 'Fortuner',
            'type'            => 'SUV',
            'ownership'       => 'owned',
            'status'          => 'available',
            'fuel_consumption'=> 12.5,
            'last_service'    => '2024-10-01',
            'next_service'    => '2025-01-01',
        ]);

        $vehicle2 = Vehicle::create([
            'plate_number'    => 'DT 5678 CD',
            'brand'           => 'Mitsubishi',
            'model'           => 'L300',
            'type'            => 'Pickup',
            'ownership'       => 'owned',
            'status'          => 'available',
            'fuel_consumption'=> 10.0,
            'last_service'    => '2024-09-15',
            'next_service'    => '2024-12-15',
        ]);

        $vehicle3 = Vehicle::create([
            'plate_number'    => 'DT 9012 EF',
            'brand'           => 'Isuzu',
            'model'           => 'Elf',
            'type'            => 'Minibus',
            'ownership'       => 'rental',
            'status'          => 'available',
            'fuel_consumption'=> 9.0,
            'last_service'    => '2024-08-01',
            'next_service'    => '2024-11-01',
        ]);

        $vehicle4 = Vehicle::create([
            'plate_number'    => 'DT 3456 GH',
            'brand'           => 'Toyota',
            'model'           => 'Hilux',
            'type'            => 'Pickup',
            'ownership'       => 'owned',
            'status'          => 'available',
            'fuel_consumption'=> 11.0,
            'last_service'    => '2024-11-01',
            'next_service'    => '2025-02-01',
        ]);

        // ══════════════════════════════════════
        // SAMPLE BOOKINGS (untuk demo dashboard)
        // ══════════════════════════════════════
        $sampleData = [
            [
                'vehicle'  => $vehicle1,
                'driver'   => $driver1,
                'status'   => 'completed',
                'days_ago' => 20,
            ],
            [
                'vehicle'  => $vehicle2,
                'driver'   => $driver2,
                'status'   => 'completed',
                'days_ago' => 15,
            ],
            [
                'vehicle'  => $vehicle3,
                'driver'   => $driver3,
                'status'   => 'completed',
                'days_ago' => 10,
            ],
            [
                'vehicle'  => $vehicle1,
                'driver'   => $driver1,
                'status'   => 'completed',
                'days_ago' => 7,
            ],
            [
                'vehicle'  => $vehicle4,
                'driver'   => $driver2,
                'status'   => 'approved',
                'days_ago' => 3,
            ],
            [
                'vehicle'  => $vehicle2,
                'driver'   => $driver3,
                'status'   => 'pending',
                'days_ago' => 1,
            ],
        ];

        foreach ($sampleData as $i => $data) {
            $start = now()->subDays($data['days_ago'])->setTime(8, 0);
            $end   = $start->copy()->addHours(6);

            $booking = Booking::create([
                'booking_code'    => 'BK-' . now()->subDays($data['days_ago'])->format('Ymd') . '-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT),
                'requester_id'    => $admin->id,
                'vehicle_id'      => $data['vehicle']->id,
                'driver_id'       => $data['driver']->id,
                'start_datetime'  => $start,
                'end_datetime'    => $end,
                'destination'     => 'Tambang Site ' . chr(65 + $i),
                'purpose'         => 'Kunjungan lapangan rutin ke site operasional',
                'passenger_count' => rand(2, 5),
                'status'          => $data['status'],
            ]);

            // Approval level 1
            Approval::create([
                'booking_id'  => $booking->id,
                'approver_id' => $manager1->id,
                'level'       => 1,
                'status'      => in_array($data['status'], ['approved', 'completed'])
                    ? 'approved' : 'pending',
                'approved_at' => in_array($data['status'], ['approved', 'completed'])
                    ? now()->subDays($data['days_ago'] - 1) : null,
            ]);

            // Approval level 2
            Approval::create([
                'booking_id'  => $booking->id,
                'approver_id' => $manager2->id,
                'level'       => 2,
                'status'      => $data['status'] === 'completed'
                    ? 'approved' : 'pending',
                'approved_at' => $data['status'] === 'completed'
                    ? now()->subDays($data['days_ago'] - 1) : null,
            ]);
        }
    }
}