<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Roles table
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // admin, secretary, professor, student
            $table->timestamps();
        });

        // Departments Table
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->timestamps();
        });

        // Users Table
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // Standard Laravel Auth Tables
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // Buildings Table
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        // Rooms Table
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('building_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->enum('type', [
                'lecture_hall',
                'amphitheater',
                'computer_lab',
                'research_lab',
                'conference_room'
            ])->default('lecture_hall');
            $table->tinyInteger('floor')->default(0); // 0 = Ground, 1 = 1st, etc
            $table->string('svg_element_id')->nullable();
            $table->integer('capacity');
            $table->integer('exam_capacity');
            $table->boolean('has_projector')->default(false);
            $table->boolean('has_whiteboard')->default(true);
            $table->integer('computer_count')->default(0);
            $table->boolean('is_wheelchair_accessible')->default(false);
            $table->boolean('has_wheelchair_desks')->default(false);
            $table->enum('hvac_type', [
                'ac',
                'central_heating',
                'heat_pump',
                'none'
            ])->default('none');
            $table->timestamps();
        });
        
    }

    public function down(): void {
        Schema::dropIfExists('rooms');
        Schema::dropIfExists('buildings');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('roles');
    }
};