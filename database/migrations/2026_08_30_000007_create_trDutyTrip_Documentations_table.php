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
        Schema::create('trDutyTrip_Documentations', function (Blueprint $table) {
            $table->id('intDocumentation_ID');
            $table->unsignedBigInteger('intDutyTrip_ID');
            $table->unsignedBigInteger('intDriver_ID');
            $table->string('txtCategory', 50); // SEBELUM_BERANGKAT, ISI_BBM, SAMPAI_TUJUAN, SELESAI
            $table->string('txtPhotoPath', 255);
            $table->integer('intOdometer')->nullable();
            $table->double('floatFuelLiters')->nullable();
            $table->double('floatFuelCost')->nullable();
            $table->double('floatLatitude')->nullable();
            $table->double('floatLongitude')->nullable();
            $table->string('txtLocationName', 255)->nullable();
            $table->text('txtNotes')->nullable();
            $table->string('txtInsertedBy', 100)->default('DRIVER');
            $table->timestamp('dtmInserted')->useCurrent();

            $table->foreign('intDutyTrip_ID')->references('intDutyTrip_ID')->on('trDutyTrip')->cascadeOnDelete();
            $table->foreign('intDriver_ID')->references('intDriver_ID')->on('mDriver')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trDutyTrip_Documentations');
    }
};
