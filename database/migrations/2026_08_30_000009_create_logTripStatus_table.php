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
        Schema::create('logTripStatus', function (Blueprint $table) {
            $table->id('intLog_ID');
            $table->unsignedBigInteger('intDutyTrip_ID');
            $table->string('txtPreviousStatus', 50)->nullable();
            $table->string('txtNewStatus', 50);
            $table->text('txtActionNotes')->nullable();
            $table->string('txtInsertedBy', 100)->default('SYSTEM');
            $table->timestamp('dtmInserted')->useCurrent();
            $table->string('txtUpdatedBy', 100)->nullable();
            $table->timestamp('dtmUpdated')->nullable();

            $table->foreign('intDutyTrip_ID')->references('intDutyTrip_ID')->on('trDutyTrip')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logTripStatus');
    }
};
