<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LoanController extends Controller
{
    /**
     * Display a listing of loans for admin
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'all');
        
        $query = Loan::with(['user', 'book', 'approver'])
            ->orderBy('created_at', 'desc');
            
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $loans = $query->paginate(10);
        
        // Format data for the frontend
        $loans->through(function ($loan) {
            $loan->book_title = $loan->book->judul;
            $loan->username = $loan->user->name;
            $loan->approver_name = $loan->approver ? $loan->approver->name : null;
            
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

        return Inertia::render('Loans/Index', [
            'loans' => $loans,
            'activeStatus' => $status,
        ]);
    }

    /**
     * Display loans pending approval
     */
    public function pendingApproval()
    {
        $loans = Loan::with(['user', 'book'])
            ->where('status', 'belum_diambil')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        // Format data for the frontend
        $loans->through(function ($loan) {
            $loan->book_title = $loan->book->judul;
            $loan->username = $loan->user->name;
            return $loan;
        });

        return Inertia::render('Loans/PendingApproval', [
            'loans' => $loans,
        ]);
    }

    /**
     * Display active loans
     */
    public function activeLoans()
    {
        $loans = Loan::with(['user', 'book'])
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        // Format data for the frontend
        $loans->through(function ($loan) {
            $loan->book_title = $loan->book->judul;
            $loan->username = $loan->user->name;
            
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

        return Inertia::render('Loans/ActiveLoans', [
            'loans' => $loans,
        ]);
    }

    /**
     * Display user's loan history
     */
    public function userLoans()
    {
        $userId = Auth::id();
        
        $loans = Loan::with('book')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        // Format data for the frontend
        $loans->through(function ($loan) {
            $loan->book_title = $loan->book->judul;
            $loan->book_cover = $loan->book->cover_url;
            
            // Check if loan is overdue
            if ($loan->status === 'dipinjam' && $loan->due_date && Carbon::now()->greaterThan($loan->due_date)) {
                $loan->is_overdue = true;
                
                // Update status to terlambat
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

        return Inertia::render('Loans/UserLoans', [
            'loans' => $loans,
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
            return redirect()->back()->with('error', 'Buku tidak tersedia untuk dipinjam.');
        }
        
        // Check if user already has a pending or active loan for this book
        $existingLoan = Loan::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereIn('status', ['belum_diambil', 'dipinjam', 'terlambat'])
            ->first();
            
        if ($existingLoan) {
            return redirect()->back()->with('error', 'Anda sudah meminjam atau mengajukan peminjaman untuk buku ini.');
        }
        
        // Create loan request
        Loan::create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => 'belum_diambil',
            'request_date' => Carbon::now(),
        ]);
        
        // Decrease book availability but not the total eksemplar
        $book->decreaseAvailability();
        
        return redirect()->route('loans.user')->with('success', 'Permintaan peminjaman buku berhasil. Silakan ambil buku di perpustakaan.');
    }

    /**
     * Approve a loan request and give the book to the user
     */
    public function approveLoan(Request $request, Loan $loan)
    {
        $request->validate([
            'due_date' => 'required|date|after:today',
        ]);
        
        // Check if the loan is in 'belum_diambil' status
        if ($loan->status !== 'belum_diambil') {
            return redirect()->back()->with('error', 'Status peminjaman tidak valid untuk disetujui.');
        }
        
        // Check if book is still available (this should always be true since we reduced availability at request time)
        $book = $loan->book;
        
        // Update loan status
        $loan->status = 'dipinjam';
        $loan->approval_date = Carbon::now();
        $loan->approval_by = Auth::id();
        $loan->due_date = Carbon::parse($request->due_date);
        $loan->save();
        
        return redirect()->route('loans.pending')->with('success', 'Peminjaman berhasil disetujui dan buku telah diserahkan.');
    }

    /**
     * Process book return
     */
    public function returnBook(Request $request, Loan $loan)
    {
        // Check if the loan is in 'dipinjam' or 'terlambat' status
        if (!in_array($loan->status, ['dipinjam', 'terlambat'])) {
            return redirect()->back()->with('error', 'Status peminjaman tidak valid untuk dikembalikan.');
        }
        
        // Calculate fine if overdue
        $fineAmount = 0;
        if ($loan->status === 'terlambat' || ($loan->due_date && Carbon::now()->greaterThan($loan->due_date))) {
            $daysLate = Carbon::now()->diffInDays($loan->due_date);
            $fineAmount = $daysLate * 1000; // 1000 rupiah per day
        }
        
        // Update loan status
        $loan->status = 'dikembalikan';
        $loan->return_date = Carbon::now();
        $loan->fine_amount = $fineAmount;
        $loan->save();
        
        // Increase book availability
        $loan->book->increaseAvailability();
        
        return redirect()->route('loans.active')->with('success', 'Buku berhasil dikembalikan.');
    }

    /**
     * Cancel a loan request (for users)
     */
    public function cancelRequest(Loan $loan)
    {
        // Users can only cancel their own loan requests
        if (Auth::id() !== $loan->user_id) {
            return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk membatalkan permintaan ini.');
        }
        
        // Check if the loan is in 'belum_diambil' status
        if ($loan->status !== 'belum_diambil') {
            return redirect()->back()->with('error', 'Status peminjaman tidak valid untuk dibatalkan.');
        }
        
        // Increase book availability since the request is canceled
        $loan->book->increaseAvailability();
        
        $loan->delete();
        
        return redirect()->route('loans.user')->with('success', 'Permintaan peminjaman berhasil dibatalkan.');
    }

    /**
     * Reject a loan request (for admin)
     */
    public function rejectRequest(Loan $loan)
    {
        // Check if the loan is in 'belum_diambil' status
        if ($loan->status !== 'belum_diambil') {
            return redirect()->back()->with('error', 'Status peminjaman tidak valid untuk ditolak.');
        }
        
        // Increase book availability since the request is rejected
        $loan->book->increaseAvailability();
        
        $loan->delete();
        
        return redirect()->route('loans.pending')->with('success', 'Permintaan peminjaman berhasil ditolak.');
    }

    /**
     * Show the form for creating a new loan
     */
    public function create()
    {
        // Get all users except administrators
        $users = User::where('role', '!=', 'administrasi')
            ->orderBy('name')
            ->get(['id', 'name', 'username', 'role']);
            
        // Get all available books
        $books = Buku::where('ketersediaan', '>', 0)
            ->orderBy('judul')
            ->get(['id', 'judul', 'penulis', 'ketersediaan']);
            
        return Inertia::render('Loans/Create', [
            'users' => $users,
            'books' => $books
        ]);
    }
    
    /**
     * Store a newly created loan
     */
    public function storeLoan(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
            'due_date' => 'required|date|after:today',
        ]);
        
        $book = Buku::find($request->book_id);
        
        // Check if book is available
        if (!$book->isAvailable()) {
            return redirect()->back()->with('error', 'Buku tidak tersedia untuk dipinjam.');
        }
        
        // Check if user already has a pending or active loan for this book
        $existingLoan = Loan::where('user_id', $request->user_id)
            ->where('book_id', $book->id)
            ->whereIn('status', ['belum_diambil', 'dipinjam', 'terlambat'])
            ->first();
            
        if ($existingLoan) {
            return redirect()->back()->with('error', 'Pengguna ini sudah meminjam atau mengajukan peminjaman untuk buku ini.');
        }
        
        // Create loan with approved status directly
        Loan::create([
            'user_id' => $request->user_id,
            'book_id' => $book->id,
            'status' => 'dipinjam',
            'request_date' => Carbon::now(),
            'approval_date' => Carbon::now(),
            'approval_by' => Auth::id(),
            'due_date' => Carbon::parse($request->due_date),
        ]);
        
        // Decrease book availability
        $book->decreaseAvailability();
        
        return redirect()->route('loans.index')->with('success', 'Peminjaman buku berhasil dibuat.');
    }
} 