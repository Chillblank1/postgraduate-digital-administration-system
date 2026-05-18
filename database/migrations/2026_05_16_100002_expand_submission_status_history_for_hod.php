<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            Schema::table('submission_status_history', function (Blueprint $table) {
                $table->string('from_status', 50)->nullable()->change();
                $table->string('to_status', 50)->change();
            });

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE submission_status_history ALTER COLUMN from_status TYPE VARCHAR(50) USING from_status::text');
            DB::statement('ALTER TABLE submission_status_history ALTER COLUMN to_status TYPE VARCHAR(50) USING to_status::text');

            return;
        }

        DB::statement('ALTER TABLE submission_status_history MODIFY from_status VARCHAR(50) NULL');
        DB::statement('ALTER TABLE submission_status_history MODIFY to_status VARCHAR(50) NOT NULL');
    }

    public function down(): void
    {
        //
    }
};
