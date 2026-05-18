<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Local-only PGSMS schema mirror (SQLite). Run explicitly:
 *
 *   php artisan migrate:fresh --path=database/migrations/pgsms
 *
 * Use DB_CONNECTION=sqlite and DB_DATABASE=database/pgsms.sqlite in .env.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('role', 32)->default('student');
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 150)->unique();
            $table->string('password_hash', 255);
            $table->rememberToken();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('phone_number', 20)->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->string('faculty', 150)->nullable();
            $table->timestamps();
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->unique();
            $table->string('faculty', 150)->nullable();
            $table->foreignId('hod_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('department_id')->references('id')->on('departments')->nullOnDelete();
        });

        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('title', 50)->nullable();
            $table->string('job_title', 150)->nullable();
            $table->string('affiliation', 150)->nullable();
            $table->string('office_address', 255)->nullable();
            $table->string('postal_address', 255)->nullable();
            $table->text('bio')->nullable();
            $table->string('profile_photo', 255)->nullable();
            $table->timestamp('account_created_at')->nullable();
            $table->string('source_system', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->unique();
            $table->string('module', 100)->nullable();
            $table->string('description', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('supervision_relationships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('supervisor_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('co_supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('assigned_at')->useCurrent();
            $table->string('status', 32)->default('active');
            $table->timestamp('updated_at')->useCurrent();
            $table->unique(['supervisor_id', 'student_id']);
        });

        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('supervisor_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('co_supervisor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 50);
            $table->string('title', 255)->nullable();
            $table->string('academic_level', 100)->nullable();
            $table->string('status', 50)->default('draft');
            $table->text('supervisor_feedback')->nullable();
            $table->timestamp('supervisor_signed_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->index('status');
            $table->index('type');
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
            $table->text('scientific_field_relevance')->nullable();
            $table->text('aims_objectives_hypothesis')->nullable();
            $table->text('chapter_assessment')->nullable();
            $table->text('overall_judgment')->nullable();
            $table->integer('intellectual_merit_score')->nullable();
            $table->text('intellectual_merit_comments')->nullable();
            $table->integer('scientific_merit_score')->nullable();
            $table->text('scientific_merit_comments')->nullable();
            $table->integer('results_quality_score')->nullable();
            $table->text('results_comments')->nullable();
            $table->integer('presentation_score')->nullable();
            $table->text('presentation_comments')->nullable();
            $table->integer('creativity_score')->nullable();
            $table->text('creativity_comments')->nullable();
            $table->integer('total_marks')->nullable();
            $table->double('percentage')->nullable();
            $table->string('recommendation', 100)->nullable();
            $table->boolean('distinction_objection')->default(false);
            $table->boolean('disclosure_permission')->default(false);
            $table->text('sections_to_share')->nullable();
            $table->string('status', 32)->default('draft');
            $table->timestamp('signed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('workflow_transitions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 100)->nullable()->unique();
            $table->string('name', 255)->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('workflow_transition_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->foreignId('transition_id')->constrained('workflow_transitions')->restrictOnDelete();
            $table->foreignId('executed_by')->constrained('users')->restrictOnDelete();
            $table->string('execution_status', 32)->default('success');
            $table->text('error_message')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('submission_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->string('status', 50);
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('changed_at')->useCurrent();
            $table->timestamps();
        });

        Schema::create('progress_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->date('reporting_period_start')->nullable();
            $table->date('reporting_period_end')->nullable();
            $table->text('milestone_summary')->nullable();
            $table->timestamps();
        });

        Schema::create('proposal_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->string('item_key', 150);
            $table->string('status', 32)->nullable();
            $table->timestamps();
        });

        Schema::create('summary_of_proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->text('synopsis')->nullable();
            $table->timestamps();
        });

        Schema::create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->nullable()->constrained('submissions')->nullOnDelete();
            $table->unsignedInteger('version_no')->default(1);
            $table->string('storage_key', 255)->nullable();
            $table->string('mime_type', 150)->nullable();
            $table->timestamps();
        });

        Schema::create('oral_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('submissions')->cascadeOnDelete();
            $table->foreignId('evaluator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('grade', 20)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('pg_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('programme_code', 50)->nullable();
            $table->string('application_no', 64)->nullable()->unique();
            $table->string('status', 40)->default('pending');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recipient_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 255)->nullable();
            $table->text('body')->nullable();
            $table->string('category', 64)->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event_type', 100);
            $table->string('entity_type', 100)->nullable();
            $table->string('entity_id', 64)->nullable();
            $table->text('old_values_json')->nullable();
            $table->text('new_values_json')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('checklist_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->nullable()->constrained('submissions')->nullOnDelete();
            $table->string('checklist_item', 150)->nullable();
            $table->string('response_val', 40)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach ([
            'checklist_responses',
            'audit_logs',
            'notifications',
            'pg_applications',
            'oral_evaluations',
            'document_versions',
            'summary_of_proposals',
            'proposal_checklists',
            'progress_reports',
            'submission_status_history',
            'workflow_transition_logs',
            'workflow_transitions',
            'thesis_evaluations',
            'submission_evaluations',
            'honorarium_claims',
            'hdc_presentations',
            'external_examiner_proposals',
            'evaluator_assignments',
            'submissions',
            'supervision_relationships',
            'permissions',
            'user_profiles',
            'users',
            'departments',
        ] as $table) {
            Schema::dropIfExists($table);
        }

        Schema::enableForeignKeyConstraints();
    }
};
