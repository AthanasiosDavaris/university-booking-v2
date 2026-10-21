<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Academic Years
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // for example "2026-2027"
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });

        // Semesters
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->string('title'); // for example "Fall 2026", "String 2027"
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });

        // Holidays
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });

        // Courses
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code')->unique();
            $table->integer('student_count');
            $table->timestamps();
        });

        // Course Sessions
        Schema::create('course_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('professor_id')->constrained('users')->cascadeOnDelete();
            $table->integer('duration_minutes')->default(120);
            $table->boolean('requires_lab')->default(false);
            $table->boolean('requires_projector')->default(true);
            $table->timestamps();
        });

        // Professor Availabilities (for Google OR-Tools)
        Schema::create('professor_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professor_id')->constrained('users')->cascadeOnDelete();
            $table->tinyInteger('day_of_week'); // 0 = Sunday, 1 = Monday, ..., 6 = Saturday
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_preferred')->default(true); // true = preferred, false = unavailable
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('professor_availabilities');
        Schema::dropIfExists('course_sessions');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('holidays');
        Schema::dropIfExists('semesters');
        Schema::dropIfExists('academic_years');
    }
};