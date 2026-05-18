<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->unique();
            $table->string('faculty', 150)->nullable();
            $table->foreignId('hod_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('evaluator_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->foreignId('evaluator_id')->constrained('users')->restrictOnDelete();
            $table->string('evaluator_type', 32)->default('internal');
            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamp('deadline')->nullable();
            $table->string('status', 32)->default('pending');
            $table->foreignId('assigned_by')->constrained('users')->restrictOnDelete();
            $table->unique(['submission_id', 'evaluator_id']);
        });

        Schema::create('external_examiner_proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->foreignId('proposed_by')->constrained('users')->restrictOnDelete();
            $table->string('examiner_name', 255);
            $table->string('examiner_email', 150)->nullable();
            $table->string('institution', 255)->nullable();
            $table->text('motivation')->nullable();
            $table->string('status', 40)->default('pending');
            $table->text('fpgc_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('hdc_presentations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->foreignId('fpgcr_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('scheduled_at')->nullable();
            $table->string('venue', 255)->nullable();
            $table->string('outcome', 40)->default('pending');
            $table->text('outcome_notes')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('honorarium_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->foreignId('evaluator_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->string('claim_file_key', 255)->nullable();
            $table->string('status', 32)->default('pending');
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('submission_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->foreignId('evaluator_id')->constrained('users')->restrictOnDelete();
            $table->string('evaluator_type', 32);
            $table->string('grade', 20)->nullable();
            $table->boolean('checklist_signed')->default(false);
            $table->timestamp('checklist_signed_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 32)->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->unique(['submission_id', 'evaluator_id']);
        });

        Schema::create('thesis_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->foreignId('evaluator_id')->constrained('users')->restrictOnDelete();
            $table->string('evaluator_type', 32)->default('internal');
            $table->integer('total_marks')->nullable();
            $table->double('percentage')->nullable();
            $table->string('recommendation', 100)->nullable();
            $table->string('status', 32)->default('draft');
            $table->timestamp('signed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        foreach ([
            'thesis_evaluations',
            'submission_evaluations',
            'honorarium_claims',
            'hdc_presentations',
            'external_examiner_proposals',
            'evaluator_assignments',
            'departments',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
