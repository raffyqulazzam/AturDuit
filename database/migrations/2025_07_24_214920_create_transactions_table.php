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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('account_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->string('type')->comment('income or expense');
            $table->decimal('amount', 15, 2);
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->date('transaction_date');
            $table->string('reference_number')->nullable();
            $table->json('tags')->nullable();
            $table->string('location')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'transaction_date']);
            $table->index(['account_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
