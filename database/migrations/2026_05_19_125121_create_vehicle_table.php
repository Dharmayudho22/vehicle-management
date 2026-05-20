<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate_number')->unique();
            $table->string('brand');
            $table->string('model');
            $table->string('type');
            $table->enum('ownership', [
                'owned',
                'leased',
                'rental'
            ]);

            $table->enum('status', [
                'available',
                'in_use',
                'maintenance',
            ])->default('available');

            $table->decimal('fuel_consumption', 8, 2)
                ->nullable();

            $table->date('last_service')->nullable();
            $table->date('next_service')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
