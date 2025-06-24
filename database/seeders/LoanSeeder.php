<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LoanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder creates various types of loan records:
     * - Active loans (dipinjam)
     * - Returned loans (dikembalikan)
     * - Overdue loans (terlambat)
     * - Pending pickup loans (belum_diambil)
     */
    public function run(): void
    {
        // Ambil ID murid dan admin untuk data peminjaman
        $muridIds = User::where('role', 'murid')->pluck('id')->toArray();
        $adminIds = User::where('role', 'administrasi')->pluck('id')->toArray();
        $bookIds = Buku::where('ketersediaan', '>', 0)->pluck('id')->toArray();
        
        if (empty($muridIds) || empty($bookIds) || empty($adminIds)) {
            $this->command->error('Data murid, buku, atau admin tidak ditemukan. Pastikan seeder lain sudah dijalankan.');
            return;
        }

        // Ambil admin ID pertama sebagai default approver
        $adminId = $adminIds[0];
        
        // Distribusi dari 200 peminjaman total
        $this->createActiveLoans($muridIds, $bookIds, $adminId, 80);
        $this->createReturnedLoans($muridIds, $bookIds, $adminId, 70);
        $this->createOverdueLoans($muridIds, $bookIds, $adminId, 30);
        $this->createPendingLoans($muridIds, $bookIds, 20);
        
        // Update ketersediaan buku berdasarkan peminjaman aktif
        $this->updateBookAvailability();
        
        $this->command->info('200 loan data seeded successfully with dates ranging from January to June 2025!');
    }
    
    /**
     * Create active loans (dipinjam)
     */
    private function createActiveLoans(array $muridIds, array $bookIds, int $adminId, int $count = 20): void
    {
        $this->command->info("Creating {$count} active loans...");
        
        // Rentang tanggal untuk peminjaman aktif (lebih baru)
        $startDate = Carbon::create(2025, 5, 1); // 1 Mei 2025
        $endDate = Carbon::create(2025, 6, 30);  // 30 Juni 2025
        
        for ($i = 0; $i < $count; $i++) {
            // Tanggal request acak dalam rentang
            $requestDate = Carbon::createFromTimestamp(
                rand($startDate->timestamp, $endDate->timestamp)
            );
            $approvalDate = (clone $requestDate)->addHours(rand(1, 24));
            $dueDate = (clone $approvalDate)->addDays(7);
            
            Loan::create([
                'user_id' => $muridIds[array_rand($muridIds)],
                'book_id' => $bookIds[array_rand($bookIds)],
                'status' => 'dipinjam',
                'request_date' => $requestDate,
                'approval_date' => $approvalDate,
                'approval_by' => $adminId,
                'due_date' => $dueDate,
                'return_date' => null,
                'fine_amount' => 0,
                'notes' => null,
            ]);
        }
    }
    
    /**
     * Create returned loans (dikembalikan)
     */
    private function createReturnedLoans(array $muridIds, array $bookIds, int $adminId, int $count = 15): void
    {
        $this->command->info("Creating {$count} returned loans...");
        
        // Rentang tanggal untuk peminjaman yang sudah dikembalikan
        $startDate = Carbon::create(2025, 2, 1); // 1 Februari 2025
        $endDate = Carbon::create(2025, 5, 15);  // 15 Mei 2025
        
        for ($i = 0; $i < $count; $i++) {
            // Tanggal request acak dalam rentang
            $requestDate = Carbon::createFromTimestamp(
                rand($startDate->timestamp, $endDate->timestamp)
            );
            $approvalDate = (clone $requestDate)->addHours(rand(1, 24));
            $dueDate = (clone $approvalDate)->addDays(7);
            $returnDate = (clone $approvalDate)->addDays(rand(3, 7)); // Dikembalikan tepat waktu
            
            Loan::create([
                'user_id' => $muridIds[array_rand($muridIds)],
                'book_id' => $bookIds[array_rand($bookIds)],
                'status' => 'dikembalikan',
                'request_date' => $requestDate,
                'approval_date' => $approvalDate,
                'approval_by' => $adminId,
                'due_date' => $dueDate,
                'return_date' => $returnDate,
                'fine_amount' => 0,
                'notes' => 'Buku dikembalikan dalam kondisi baik',
            ]);
        }
    }
    
    /**
     * Create overdue loans (terlambat)
     */
    private function createOverdueLoans(array $muridIds, array $bookIds, int $adminId, int $count = 10): void
    {
        $this->command->info("Creating {$count} overdue loans...");
        
        // Rentang tanggal untuk peminjaman terlambat
        $startDate = Carbon::create(2025, 1, 1); // 1 Januari 2025
        $endDate = Carbon::create(2025, 3, 31);  // 31 Maret 2025
        
        for ($i = 0; $i < $count; $i++) {
            // Tanggal request acak dalam rentang
            $requestDate = Carbon::createFromTimestamp(
                rand($startDate->timestamp, $endDate->timestamp)
            );
            $approvalDate = (clone $requestDate)->addHours(rand(1, 24));
            $dueDate = (clone $approvalDate)->addDays(7);
            // Tanggal jatuh tempo sudah lewat
            
            $daysLate = rand(5, 30);
            $finePerDay = 1000; // Rp 1.000 per hari
            
            Loan::create([
                'user_id' => $muridIds[array_rand($muridIds)],
                'book_id' => $bookIds[array_rand($bookIds)],
                'status' => 'terlambat',
                'request_date' => $requestDate,
                'approval_date' => $approvalDate,
                'approval_by' => $adminId,
                'due_date' => $dueDate,
                'return_date' => null,
                'fine_amount' => $daysLate * $finePerDay,
                'notes' => 'Terlambat ' . $daysLate . ' hari',
            ]);
        }
    }
    
    /**
     * Create pending pickup loans (belum_diambil)
     */
    private function createPendingLoans(array $muridIds, array $bookIds, int $count = 5): void
    {
        $this->command->info("Creating {$count} pending loans...");
        
        // Rentang tanggal untuk peminjaman belum diambil (terbaru)
        $startDate = Carbon::create(2025, 6, 1); // 1 Juni 2025
        $endDate = Carbon::create(2025, 6, 30);  // 30 Juni 2025
        
        for ($i = 0; $i < $count; $i++) {
            // Tanggal request acak dalam rentang terbatas (terbaru)
            $requestDate = Carbon::createFromTimestamp(
                rand($startDate->timestamp, $endDate->timestamp)
            );
            
            Loan::create([
                'user_id' => $muridIds[array_rand($muridIds)],
                'book_id' => $bookIds[array_rand($bookIds)],
                'status' => 'belum_diambil',
                'request_date' => $requestDate,
                'approval_date' => null,
                'approval_by' => null,
                'due_date' => null,
                'return_date' => null,
                'fine_amount' => 0,
                'notes' => 'Menunggu persetujuan',
            ]);
        }
    }
    
    /**
     * Update book availability based on active loans
     */
    private function updateBookAvailability(): void
    {
        $this->command->info('Updating book availability...');
        
        // Hitung jumlah peminjaman aktif untuk setiap buku
        $activeLoans = Loan::whereIn('status', ['dipinjam', 'terlambat', 'belum_diambil'])
            ->select('book_id')
            ->selectRaw('COUNT(*) as loan_count')
            ->groupBy('book_id')
            ->get();
            
        // Update ketersediaan buku
        foreach ($activeLoans as $loanData) {
            $book = Buku::find($loanData->book_id);
            if ($book) {
                // Pastikan ketersediaan tetap valid
                $newAvailability = $book->eksemplar - $loanData->loan_count;
                if ($newAvailability < 0) $newAvailability = 0;
                
                $book->ketersediaan = $newAvailability;
                $book->save();
            }
        }
    }
}
