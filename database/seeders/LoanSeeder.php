<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Loan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LoanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seeder 60 Loan di bulan 6 2025
        $muridIds = \App\Models\User::where('role', 'murid')->pluck('id');
        $bookIds = Buku::pluck('id');
        if ($muridIds->count() > 0 && $bookIds->count() > 0) {
            for ($i = 0; $i < 60; $i++) {
                try {
                    Loan::factory()->create([
                        'user_id' => $muridIds->random(),
                        'book_id' => $bookIds->random(),
                    ]);
                } catch (\Exception $e) {
                    dd($e->getMessage());
                }
            }
        }
    }
}
