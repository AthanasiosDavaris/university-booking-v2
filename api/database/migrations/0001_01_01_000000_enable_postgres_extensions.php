<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        // Enable pgvector for AI Sematic Search
        DB::statement('CREATE EXTENSION IF NOT EXISTS vector;');

        // Enable btree_gist for Double-Booking Exclusion Constraint
        DB::statement('CREATE EXTENSION IF NOT EXISTS btree_gist;');
    }

    public function down(): void {
        DB::statement('DROP EXTENSION IF EXISTS btree_gist;');
        DB::statement('DROP EXTENSION IF EXISTS vector;');
    }
};