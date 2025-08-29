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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('gender')->nullable(); // male أو female
            $table->date('birthday')->nullable();
            $table->boolean('active')->default(false);
            $table->string('verification_code')->nullable();
            $table->foreignId('role_id')
          ->constrained('roles')     // يربط بـ roles.id تلقائياً
          ->onDelete('cascade')      // حذف المستخدم إذا حذف الدور
          ->default(3);             $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
