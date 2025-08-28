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
    Schema::create('levels', function (Blueprint $table) {
        
    $table->id();
    $table->unsignedBigInteger('course_id');
    $table->string('name');
    $table->string('teacher');
    $table->integer('seats_number')->default(0);
    $table->enum('status', ['full', 'starting_soon', 'coming_soon'])->default('coming_soon');
    // $table->enum('day', ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']);
    $table->json('days')->nullable();
    $table->time('start_time')->nullable();
    $table->date('start_date')->nullable();
    $table->text('description')->nullable();
    $table->timestamps();

    $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('levels');
    }
};
