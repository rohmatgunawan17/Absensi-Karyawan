<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table): void {
            $table->enum('status', ['Hadir', 'Izin', 'Sakit', 'Alpha', 'Libur'])
                ->default('Hadir')
                ->change();
        });
    }

    public function down(): void
    {
        DB::table('attendances')->where('status', 'Libur')->update(['status' => 'Alpha']);

        Schema::table('attendances', function (Blueprint $table): void {
            $table->enum('status', ['Hadir', 'Izin', 'Sakit', 'Alpha'])
                ->default('Hadir')
                ->change();
        });
    }
};
