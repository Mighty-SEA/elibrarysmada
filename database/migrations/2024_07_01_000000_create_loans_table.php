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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->enum('status', ['belum_diambil', 'dipinjam', 'terlambat', 'dikembalikan'])->default('belum_diambil');
            $table->dateTime('request_date');
            $table->dateTime('approval_date')->nullable();
            $table->foreignId('approval_by')->nullable()->constrained('users');
            $table->dateTime('due_date')->nullable();
            $table->dateTime('return_date')->nullable();
            $table->decimal('fine_amount', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
}; 