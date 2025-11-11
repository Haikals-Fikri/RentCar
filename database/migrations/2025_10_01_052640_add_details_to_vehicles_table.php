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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('seat')->nullable()->after('plate_number');
            $table->string('transmission')->nullable()->after('seat');
            $table->string('fuel_type')->nullable()->after('transmission');
            $table->string('year')->nullable()->after('fuel_type');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['seat', 'transmission', 'fuel_type', 'year']);
        });
    }
};
