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
        Schema::create('dtLocationTracking', function (Blueprint $table) {
            $table->id('intTracking_ID');
            $table->unsignedBigInteger('intDutyTrip_ID');
            $table->unsignedBigInteger('intDriver_ID');
            $table->double('floatLatitude');
            $table->double('floatLongitude');
            $table->double('floatSpeed')->default(0);
            $table->double('floatHeading')->default(0);
            $table->double('floatAccuracy')->default(0);
            $table->timestamp('dtmTracked')->useCurrent();

            $table->foreign('intDutyTrip_ID')->references('intDutyTrip_ID')->on('trDutyTrip')->cascadeOnDelete();
            $table->foreign('intDriver_ID')->references('intDriver_ID')->on('mDriver')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dtLocationTracking');
    }
};
