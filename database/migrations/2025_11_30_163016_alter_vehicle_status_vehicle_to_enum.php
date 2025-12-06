<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // PASTIKAN ADA INI!

return new class extends Migration
{
    private $enumName = 'vehicle_status_type';
    // Gunakan nilai ENUM yang benar-benar Anda inginkan
    private $enumValues = ['Tersedia', 'Tidak_tersedia', 'Maintanance'];

    public function up(): void
    {
        // 1. Buat tipe ENUM kustom di PostgreSQL
        DB::statement("CREATE TYPE {$this->enumName} AS ENUM ('" . implode("', '", $this->enumValues) . "')");

        // 2. Ubah kolom yang sudah ada
        $sqlChange = "ALTER TABLE vehicles
                      ALTER COLUMN status_vehicle DROP DEFAULT,
                      ALTER COLUMN status_vehicle TYPE {$this->enumName}
                      USING status_vehicle::text::{$this->enumName},
                      ALTER COLUMN status_vehicle SET DEFAULT 'Tersedia',
                      ALTER COLUMN status_vehicle SET NOT NULL";

        DB::statement($sqlChange);
    }

    public function down(): void
    {
        // 1. Kembalikan tipe kolom ke string (VARCHAR)
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('status_vehicle', 255)->default('Tersedia')->change();
        });

        // 2. Hapus tipe ENUM kustom yang telah dibuat
        DB::statement("DROP TYPE {$this->enumName}");
    }
};
