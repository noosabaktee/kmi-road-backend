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
        Schema::create('trDutyTrip_Details', function (Blueprint $table) {
            $table->id('intDutyTrip_Detail_ID');
            $table->unsignedBigInteger('intDutyTrip_ID')->nullable();
            $table->string('txtEmployeeName', 100);
            $table->string('txtEmployeeNIK', 50)->nullable();
            $table->string('txtDepartment', 100);
            $table->string('txtPhoneNumber', 50)->nullable();
            $table->date('dtmTripDate');
            $table->unsignedBigInteger('intRequestedVehicle_ID')->nullable();
            $table->string('txtDestination', 255);
            $table->text('txtPurpose');
            $table->text('txtNotes')->nullable();
            $table->string('txtBookingStatus', 50)->default('PENDING'); // PENDING, ASSIGNED, COMPLETED, CANCELLED
            $table->string('txtInsertedBy', 100)->default('EMPLOYEE');
            $table->timestamp('dtmInserted')->useCurrent();

            $table->foreign('intDutyTrip_ID')->references('intDutyTrip_ID')->on('trDutyTrip')->cascadeOnDelete();
            $table->foreign('intRequestedVehicle_ID')->references('intVehicle_ID')->on('mVehicle')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trDutyTrip_Details');
    }
};
