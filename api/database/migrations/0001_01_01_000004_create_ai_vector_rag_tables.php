<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        // Documents table
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('source_url')->nullable();
            $table->timestamps();
        });

        // Document Chunks
        Schema::create('document_chunks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->timestamps();
        });

        // 384-dimensional vector column via raw SQL (standard for lightweight multilingual MiniLM models)
        DB::statement('ALTER TABLE document_chunks ADD COLUMN embedding vector(384);');

        // HNSW Index (for fast approximate nearest neighbor searches)
        DB::statement('CREATE INDEX document_chunks_embedding_hnsw_idx ON document_chunks USING hnsw (embedding vector_l2_ops);');
    }

    public function down(): void {
        Schema::dropIfExists('document_chunks');
        Schema::dropIfExists('documents');
    }
};