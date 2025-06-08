<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Buku;
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
    
    /**
     * Request to borrow a book
     */
    public function requestLoan(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);
        
        $user = Auth::user();
        $book = Buku::find($request->book_id);
        
        // Check if book is available
        if (!$book->isAvailable()) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Buku tidak tersedia untuk dipinjam.'
                ], 422);
            }
            
            return redirect()->back()->with('error', 'Buku tidak tersedia untuk dipinjam.');
        }
        
        // Check if user already has a pending or active loan for this book
        $existingLoan = Loan::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereIn('status', ['belum_diambil', 'dipinjam', 'terlambat'])
            ->first();
            
        if ($existingLoan) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah meminjam atau mengajukan peminjaman untuk buku ini.'
                ], 422);
            }
            
            return redirect()->back()->with('error', 'Anda sudah meminjam atau mengajukan peminjaman untuk buku ini.');
        }
        
        // Create loan request
        Loan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => 'belum_diambil',
            'request_date' => Carbon::now(),
        ]);
        
        // Decrease book availability but not the total stock
        $book->decreaseAvailability();
        
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Permintaan peminjaman buku berhasil. Silakan ambil buku di perpustakaan.'
            ]);
        }
        
        return redirect()->route('bookshelves')->with('success', 'Permintaan peminjaman buku berhasil. Silakan ambil buku di perpustakaan.');
    }
    
    /**
     * Cancel a loan request
     */
    public function cancelRequest(Loan $loan)
    {
        // Check if the loan belongs to the user
        if ($loan->user_id !== Auth::id()) {
            return redirect()->route('bookshelves')->with('error', 'Anda tidak memiliki akses untuk membatalkan peminjaman ini.');
        }
        
        // Check if the loan is in 'belum_diambil' status
        if ($loan->status !== 'belum_diambil') {
            return redirect()->route('bookshelves')->with('error', 'Status peminjaman tidak valid untuk dibatalkan.');
        }
        
        // Increase book availability since the request is canceled
        $book = $loan->book;
        $book->increaseAvailability();
        
        // Delete the loan request
        $loan->delete();
        
        return redirect()->route('bookshelves')->with('success', 'Permintaan peminjaman berhasil dibatalkan.');
    }
} 