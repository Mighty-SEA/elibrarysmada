<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->integer('ketersediaan')->nullable()->after('jumlah');
        });
        
        // Set ketersediaan to match jumlah for existing books
        DB::statement('UPDATE books SET ketersediaan = jumlah WHERE ketersediaan IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('ketersediaan');
        });
    }
};
