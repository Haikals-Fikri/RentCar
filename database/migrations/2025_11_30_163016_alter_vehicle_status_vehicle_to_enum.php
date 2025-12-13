<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private string $enumName = 'vehicle_status_type';

    private array $enumValues = [
        'Tersedia',
        'Tidak_tersedia',
        'Maintenance', // perbaikan ejaan dari "Maintanance"
    ];

    public function up(): void
    {
        // Membuat ENUM type jika belum ada
        DB::statement("
            DO $$
            BEGIN
                IF NOT EXISTS (
                    SELECT 1 FROM pg_type WHERE typname = '{$this->enumName}'
                ) THEN
                    CREATE TYPE {$this->enumName} AS ENUM ('" . implode("', '", $this->enumValues) . "');
                END IF;
            END
            $$;
        ");

        // Ubah kolom status_vehicle menjadi ENUM
        DB::statement("
            ALTER TABLE vehicles
                ALTER COLUMN status_vehicle DROP DEFAULT,
                ALTER COLUMN status_vehicle TYPE {$this->enumName} USING status_vehicle::text::{$this->enumName},
                ALTER COLUMN status_vehicle SET DEFAULT 'Tersedia',
                ALTER COLUMN status_vehicle SET NOT NULL;
        ");
    }

    public function down(): void
    {
        // Kembalikan kolom status_vehicle ke string biasa
        Schema::table('vehicles', function (Blueprint $table) {
            $table->string('status_vehicle', 255)
                  ->default('Tersedia')
                  ->change();
        });

        // Hapus ENUM type jika ada
        DB::statement("DROP TYPE IF EXISTS {$this->enumName}");
    }
};
