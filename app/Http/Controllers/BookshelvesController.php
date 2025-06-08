<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class BookshelvesController extends Controller
{
    /**
     * Menampilkan halaman rak buku dan riwayat peminjaman
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $userId = Auth::id();
        
        // Ambil data peminjaman aktif (belum_diambil, dipinjam, terlambat)
        $activeLoans = Loan::with('book')
            ->where('user_id', $userId)
            ->whereIn('status', ['belum_diambil', 'dipinjam', 'terlambat'])
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Ambil data riwayat peminjaman (dikembalikan)
        $loanHistory = Loan::with('book')
            ->where('user_id', $userId)
            ->where('status', 'dikembalikan')
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Format data peminjaman aktif
        $formattedActiveLoans = $activeLoans->map(function ($loan) {
            $loan->book_title = $loan->book->judul;
            $loan->book_cover = $loan->book->cover_url;
            $loan->author = $loan->book->pengarang;
            
            // Check if loan is overdue
            if ($loan->status === 'dipinjam' && $loan->due_date && Carbon::now()->greaterThan($loan->due_date)) {
                $loan->is_overdue = true;
                
                // Update status to terlambat if needed
                if ($loan->status !== 'terlambat') {
                    $loanModel = Loan::find($loan->id);
                    $loanModel->status = 'terlambat';
                    $loanModel->save();
                    $loan->status = 'terlambat';
                }
            } else {
                $loan->is_overdue = false;
            }
            
            return $loan;
        });
        
        // Format data riwayat peminjaman
        $formattedLoanHistory = $loanHistory->map(function ($loan) {
            $loan->book_title = $loan->book->judul;
            $loan->book_cover = $loan->book->cover_url;
            $loan->author = $loan->book->pengarang;
            
            return $loan;
        });

        return Inertia::render('Bookshelves/Index', [
            'activeLoans' => $formattedActiveLoans,
            'loanHistory' => $formattedLoanHistory,
        ]);
    }
} 