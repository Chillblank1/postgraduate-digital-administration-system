<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('supervision_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supervisor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('co_supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->nullable();
            $table->enum('status', ['active', 'inactive', 'completed'])->default('active');
            $table->timestamps();

            $table->unique(['supervisor_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supervision_relationships');
    }
};
