<?php

namespace App\Console\Commands;

use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckOverdueLoans extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'loans:check-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for overdue loans and update their status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for overdue loans...');
        
        $now = Carbon::now();
        $overdueLoans = Loan::where('status', 'dipinjam')
            ->whereDate('due_date', '<', $now)
            ->get();
            
        $count = 0;
        foreach ($overdueLoans as $loan) {
            $loan->status = 'terlambat';
            $loan->save();
            $count++;
        }
        
        $this->info("Updated status for {$count} overdue loans.");
        return Command::SUCCESS;
    }
} 