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
        Schema::create('mUser', function (Blueprint $table) {
            $table->id('intUser_ID');
            $table->string('txtUserName', 100);
            $table->string('txtEmail', 150)->unique();
            $table->string('txtPassword', 255);
            $table->string('txtRole', 50)->default('ADMIN_HC');
            $table->string('txtPhoneNumber', 50)->nullable();
            $table->string('txtAvatar', 255)->nullable();
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
        Schema::dropIfExists('mUser');
    }
};
