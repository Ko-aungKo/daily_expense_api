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
        Schema::create('categories', function (Blueprint $table) {
            $table->ulid('id')->primary();
            // user_id is nullable: null indicates system-default category
            $table->foreignUlid('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'is_default']);

            // Unique category name per user (and for system defaults, user_id is null)
            $table->unique(['user_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
