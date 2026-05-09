<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submission_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->cascadeOnDelete();
            $table->enum('from_status', [
                'draft',
                'submitted_pending_supervisor',
                'supervisor_approved',
                'supervisor_revision_requested',
                'rejected',
            ])->nullable();
            $table->enum('to_status', [
                'draft',
                'submitted_pending_supervisor',
                'supervisor_approved',
                'supervisor_revision_requested',
                'rejected',
            ]);
            $table->foreignId('changed_by')->constrained('users')->cascadeOnDelete();
            $table->enum('actor_role', [
                'student',
                'supervisor',
                'hod',
                'internal_evaluator',
                'external_evaluator',
                'fpgc_r',
                'fpgc',
                'admin',
            ]);
            $table->text('comments')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['submission_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_status_history');
    }
};
