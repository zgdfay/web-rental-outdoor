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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('gear_id')->constrained()->onDelete('cascade');
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->text('customer_address');
            $table->string('ktp_path')->nullable();
            $table->string('transfer_proof_path')->nullable();
            $table->integer('rental_days');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('total_price');
            $table->integer('dp_amount');
            $table->integer('remaining_balance');
            $table->string('status')->default('pending'); // pending, validated, handed_over, returned, overdue, cancelled
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
