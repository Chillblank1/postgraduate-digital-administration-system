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
            Schema::table('submissions', function (Blueprint $table) {
                $table->string('status', 50)->default('draft')->change();
            });

            return;
        }

        if ($driver === 'pgsql') {
            DB::statement('ALTER TABLE submissions ALTER COLUMN status TYPE VARCHAR(50) USING status::text');
            DB::statement("ALTER TABLE submissions ALTER COLUMN status SET DEFAULT 'draft'");
            DB::statement('ALTER TABLE submissions ALTER COLUMN status SET NOT NULL');

            return;
        }

        DB::statement("ALTER TABLE submissions MODIFY status VARCHAR(50) NOT NULL DEFAULT 'draft'");
    }

    public function down(): void
    {
        // Intentionally left — reverting enum/string shape is environment-specific.
    }
};
