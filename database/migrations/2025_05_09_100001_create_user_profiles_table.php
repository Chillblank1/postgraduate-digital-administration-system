<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
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
    }

    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
