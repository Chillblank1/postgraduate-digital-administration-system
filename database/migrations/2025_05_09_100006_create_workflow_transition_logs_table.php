<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('workflow_transition_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained()->cascadeOnDelete();
            $table->foreignId('transition_id')->nullable()->constrained('workflow_transitions')->nullOnDelete();
            $table->foreignId('executed_by')->constrained('users')->cascadeOnDelete();
            $table->enum('execution_status', ['success', 'failed', 'skipped'])->default('success');
            $table->text('error_message')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['submission_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_transition_logs');
    }
};
