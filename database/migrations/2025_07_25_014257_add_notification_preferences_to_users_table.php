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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('budget_alerts')->default(true);
            $table->boolean('large_transaction_alerts')->default(true);
            $table->boolean('savings_goal_alerts')->default(true);
            $table->boolean('low_balance_alerts')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'budget_alerts',
                'large_transaction_alerts', 
                'savings_goal_alerts',
                'low_balance_alerts'
            ]);
        });
    }
};
