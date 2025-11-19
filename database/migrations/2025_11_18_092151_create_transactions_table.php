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
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('receiver_id')->constrained('users')->onDelete('cascade');
            $table->decimal('amount', 15);
            $table->decimal('commission_fee', 15)->default(0);
            $table->enum('type', ['transfer', 'payment', 'refund', 'withdrawal', 'deposit'])->default('transfer');
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('completed');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index(['sender_id', 'created_at'], 'idx_sender_created');
            $table->index(['receiver_id', 'created_at'], 'idx_receiver_created');
            $table->index(['status', 'created_at'], 'idx_status_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('idx_sender_created');
            $table->dropIndex('idx_receiver_created');
            $table->dropIndex('idx_status_created');
        });

        Schema::dropIfExists('transactions');
    }
};
