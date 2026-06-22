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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('custom_id')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained()->onDelete('cascade');
            $table->integer('months')->default(12);
            $table->foreignId('coupon_id')->nullable()->constrained()->onDelete('set null');
            
            $table->string('full_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->nullable();
            
            $table->decimal('subtotal', 8, 2)->default(0);
            $table->decimal('discount', 8, 2)->default(0);
            $table->decimal('tax', 8, 2)->default(0);
            $table->decimal('total', 8, 2)->default(0);
            
            $table->string('status')->default('pending'); // pending, paid, cancelled
            $table->string('stripe_session_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
