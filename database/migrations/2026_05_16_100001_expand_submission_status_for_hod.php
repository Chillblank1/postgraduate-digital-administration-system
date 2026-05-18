<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            Schema::table('submissions', function (Blueprint $table) {
                $table->string('status', 50)->default('draft')->change();
            });
        } else {
            DB::statement("ALTER TABLE submissions MODIFY status VARCHAR(50) NOT NULL DEFAULT 'draft'");
        }
    }

    public function down(): void
    {
        // Intentionally left — reverting enum/string shape is environment-specific.
    }
};
