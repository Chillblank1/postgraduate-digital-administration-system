<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_transitions', function (Blueprint $table) {
            $table->id();
            $table->enum('from_status', [
                'draft',
                'submitted_pending_supervisor',
                'supervisor_approved',
                'supervisor_revision_requested',
                'rejected',
            ]);
            $table->enum('to_status', [
                'draft',
                'submitted_pending_supervisor',
                'supervisor_approved',
                'supervisor_revision_requested',
                'rejected',
            ]);
            $table->enum('allowed_role', [
                'student',
                'supervisor',
                'hod',
                'internal_evaluator',
                'external_evaluator',
                'fpgc_r',
                'fpgc',
                'admin',
            ]);
            $table->string('description', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['from_status', 'to_status', 'allowed_role', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_transitions');
    }
};
