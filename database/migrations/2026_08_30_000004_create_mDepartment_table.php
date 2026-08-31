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
        Schema::create('mDepartment', function (Blueprint $table) {
            $table->id('intDepartment_ID');
            $table->string('txtDepartmentName', 100);
            $table->string('txtDepartmentCode', 50);
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
        Schema::dropIfExists('mDepartment');
    }
};
