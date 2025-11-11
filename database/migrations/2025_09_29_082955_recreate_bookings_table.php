<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Hapus tabel 'bookings' jika sudah ada
        Schema::dropIfExists('bookings');

        // 2. Buat kembali tabel 'bookings' dengan struktur baru
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_payment', 10, 2);
            $table->string('payment_method');
            $table->string('status')->default('Menunggu Konfirmasi Admin');
            $table->string('sim_image'); // Path ke gambar SIM
            $table->string('name');
            $table->text('address');
            $table->string('phone');
            $table->integer('rating')->nullable();
            $table->text('review')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Jika migrasi di-rollback, hapus tabel yang baru dibuat
        Schema::dropIfExists('bookings');
    }
};
