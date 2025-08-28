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
        Schema::create('enrollments', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // الطالب
        $table->foreignId('level_id')->constrained()->onDelete('cascade'); // المستوى
        $table->enum('academic_stage', ['pre-secondary', 'secondary', 'institute', 'university', 'masters', 'phd']);
        $table->enum('language_level', ['beginner', 'weak-elementary', 'pre-intermediate', 'intermediate', 'advanced-upper-intermediate', 'i-cant-decide']);
        $table->time('time');
        $table->enum('days', ['tue-thu-wed', 'sat-sun-mon']);
        $table->enum('learning_method', ['at-smart-foundation', 'online']);
        $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
