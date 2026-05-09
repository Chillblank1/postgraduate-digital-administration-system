<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('supervisor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('co_supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('type', ['thesis', 'sop']);
            $table->string('title', 255);
            $table->string('academic_level', 100)->nullable();
            $table->enum('status', [
                'draft',
                'submitted_pending_supervisor',
                'supervisor_approved',
                'supervisor_revision_requested',
                'rejected',
            ])->default('draft');
            $table->text('supervisor_feedback')->nullable();
            $table->string('supervisor_decision', 100)->nullable();
            $table->timestamp('supervisor_signed_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'status']);
            $table->index(['supervisor_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
