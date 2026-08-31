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
        Schema::create('mDriver', function (Blueprint $table) {
            $table->id('intDriver_ID');
            $table->string('txtDriverName', 100);
            $table->string('txtPhoneNumber', 50);
            $table->string('txtLicenseNumber', 50); // SIM
            $table->string('txtEmail', 150)->unique();
            $table->string('txtPassword', 255);
            $table->string('txtAvatar', 255)->nullable();
            $table->string('txtStatus', 50)->default('AVAILABLE'); // AVAILABLE, ON_DUTY, OFF
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
        Schema::dropIfExists('mDriver');
    }
};
