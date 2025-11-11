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
         Schema::create('owner_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id')->unique();
            $table->string('owner_name')->nullable();
            $table->string('business_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('ktp_number')->nullable();
            $table->string('npwp_number')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('owner_profiles');
    }
};
