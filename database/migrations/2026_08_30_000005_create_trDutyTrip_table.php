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
        Schema::create('trDutyTrip', function (Blueprint $table) {
            $table->id('intDutyTrip_ID');
            $table->string('txtTripCode', 50)->unique();
            $table->unsignedBigInteger('intVehicle_ID')->nullable();
            $table->unsignedBigInteger('intDriver_ID')->nullable();
            $table->date('dtmTripDate');
            $table->dateTime('dtmDepartureTime')->nullable();
            $table->dateTime('dtmArrivalTime')->nullable();
            $table->string('txtDestination', 255);
            $table->text('txtPurpose');
            $table->string('txtTripStatus', 50)->default('PENDING'); // PENDING, SCHEDULED, IN_PROGRESS, REFUELING, ARRIVED, COMPLETED, CANCELLED
            $table->integer('intStartOdometer')->nullable();
            $table->integer('intEndOdometer')->nullable();
            $table->double('floatTotalFuelCost')->default(0)->nullable();
            $table->double('floatTotalFuelLiters')->default(0)->nullable();
            $table->text('txtNotes')->nullable();
            $table->string('txtInsertedBy', 100)->default('SYSTEM');
            $table->timestamp('dtmInserted')->useCurrent();

            $table->foreign('intVehicle_ID')->references('intVehicle_ID')->on('mVehicle')->nullOnDelete();
            $table->foreign('intDriver_ID')->references('intDriver_ID')->on('mDriver')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trDutyTrip');
    }
};
