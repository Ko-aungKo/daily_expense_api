<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_method_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->text('description')->nullable();
            $table->date('expense_date');
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('user_id');
            $table->index('category_id');
            $table->index('payment_method_id');
            $table->index('expense_date');

            // Composite indexes for common query combinations
            $table->index(['user_id', 'expense_date']);
            $table->index(['user_id', 'category_id']);
            $table->index(['user_id', 'payment_method_id']);
        });

        // Add CHECK constraint using raw SQL
        DB::statement('ALTER TABLE expenses ADD CONSTRAINT check_amount_positive CHECK (amount > 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
