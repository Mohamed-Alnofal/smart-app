<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('scholarship_applications', function (Blueprint $table) {
    $table->id();
    $table->enum('academic_stage', [
        'Pre-Secondary',
        'Secondary',
        'Institute',
        'University Degree',
        'Masters',
        'PhD'
    ])->nullable(false);
    $table->string('school_name', 255);
    $table->string('field_of_study', 255);
    $table->string('academic_year', 20);
    $table->decimal('average', 5, 2)->nullable();
    $table->boolean('placement_test')->default(false);
    $table->enum('language_level', [
        'Beginner',
        'Weak-Elementary',
        'Pre-Intermediate',
        'Intermediate',
        'Advanced-Upper-Intermediate',
        'ICantDecide'
    ]);
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scholarship_applications');
    }
};
