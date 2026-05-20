<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'])) {
            // MySQL/MariaDB: add check constraints (MySQL may ignore them on older versions)
            DB::statement("ALTER TABLE `shifts` ADD CONSTRAINT `chk_shifts_start_time` CHECK (`start_time` >= '00:00:00' AND `start_time` <= '23:59:59')");
            DB::statement("ALTER TABLE `shifts` ADD CONSTRAINT `chk_shifts_end_time` CHECK (`end_time` >= '00:00:00' AND `end_time` <= '23:59:59')");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE shifts ADD CONSTRAINT chk_shifts_start_time CHECK (start_time >= TIME '00:00' AND start_time <= TIME '23:59')");
            DB::statement("ALTER TABLE shifts ADD CONSTRAINT chk_shifts_end_time CHECK (end_time >= TIME '00:00' AND end_time <= TIME '23:59')");
        } elseif ($driver === 'sqlite') {
            // SQLite: recreate table with constraints is complex; skip automatic migration.
            // We leave application-level validation to enforce correctness for sqlite.
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'])) {
            DB::statement("ALTER TABLE `shifts` DROP CONSTRAINT `chk_shifts_start_time`");
            DB::statement("ALTER TABLE `shifts` DROP CONSTRAINT `chk_shifts_end_time`");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE shifts DROP CONSTRAINT IF EXISTS chk_shifts_start_time");
            DB::statement("ALTER TABLE shifts DROP CONSTRAINT IF EXISTS chk_shifts_end_time");
        } elseif ($driver === 'sqlite') {
            // nothing to do
        }
    }
};
