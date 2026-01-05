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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('vehicle_id')->constrained();
            $table->string('name');
            $table->string('address');
            $table->string('phone');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('sim_image')->nullable();
            $table->string('payment_method');
            $table->string('status');
            $table->string('total_payment')->nullable();
            $table->text('review')->nullable();
            $table->integer('rating')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
