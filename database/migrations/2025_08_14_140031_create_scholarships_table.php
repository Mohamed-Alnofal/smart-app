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
        Schema::create('scholarships', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // علاقة مع المستخدم
        $table->string('name');
        $table->text('description')->nullable();
        $table->string('type_of_financing');
        $table->string('funding_agency');
        $table->string('achieved_certificate');
        $table->string('required_documents')->nullable();
        $table->text('advantages')->nullable();
        $table->text('required_certificates')->nullable();
        $table->string('university');
        $table->string('country');
        $table->string('specialization')->nullable();
        $table->string('image')->nullable(); // لتخزين مسار الصورة
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scholarships');
    }
};
