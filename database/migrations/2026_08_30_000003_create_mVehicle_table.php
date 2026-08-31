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
        Schema::create('mVehicle', function (Blueprint $table) {
            $table->id('intVehicle_ID');
            $table->string('txtVehicleName', 100);
            $table->string('txtPlateNumber', 20)->unique();
            $table->string('txtBrandModel', 100);
            $table->string('txtVehicleType', 50)->default('MPV'); // MPV, SUV, Van, Minibus, Sedan
            $table->integer('intMaxSeat')->default(7);
            $table->integer('intCurrentOdometer')->default(0);
            $table->string('txtFuelType', 50)->default('Pertalite'); // Pertalite, Pertamax, Dexlite, Solar
            $table->string('txtVehiclePhoto', 255)->nullable();
            $table->string('txtStatus', 50)->default('AVAILABLE'); // AVAILABLE, IN_USE, MAINTENANCE
            $table->string('txtInsertedBy', 100)->default('SYSTEM');
            $table->timestamp('dtmInserted')->useCurrent();
            $table->string('txtUpdatedBy', 100)->nullable();
            $table->timestamp('dtmUpdated')->nullable();
            $table->smallInteger('bitActive')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mVehicle');
    }
};
