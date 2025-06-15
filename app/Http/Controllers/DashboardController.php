<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Buku;
use App\Models\Loan;
use App\Models\User;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');

        $bookQuery = Buku::query();
        $loanQuery = Loan::query();
        $userQuery = User::query()->where('role', '!=', 'administrasi');
        $pendingQuery = Loan::where('status', 'belum_diambil');

        if ($start) {
            $loanQuery->whereDate('created_at', '>=', $start);
            $pendingQuery->whereDate('created_at', '>=', $start);
            $userQuery->whereDate('created_at', '>=', $start);
            // Buku biasanya tidak berdasarkan tanggal, tapi bisa ditambah jika ingin
        }
        if ($end) {
            $loanQuery->whereDate('created_at', '<=', $end);
            $pendingQuery->whereDate('created_at', '<=', $end);
            $userQuery->whereDate('created_at', '<=', $end);
        }

        $totalBooks = $bookQuery->count();
        $totalLoans = $loanQuery->count();
        $pendingRequests = $pendingQuery->count();
        $totalUsers = $userQuery->count();

        // Hitung buku yang ditambah dan dihapus pada rentang tanggal
        $booksAdded = null;
        $booksDeleted = null;
        if ($start || $end) {
            $addedQuery = Buku::query();
            $deletedQuery = Buku::onlyTrashed();
            if ($start) {
                $addedQuery->whereDate('created_at', '>=', $start);
                $deletedQuery->whereDate('deleted_at', '>=', $start);
            }
            if ($end) {
                $addedQuery->whereDate('created_at', '<=', $end);
                $deletedQuery->whereDate('deleted_at', '<=', $end);
            }
            $booksAdded = $addedQuery->count();
            $booksDeleted = $deletedQuery->count();
        }

        return Inertia::render('Dashboard', [
            'totalBooks' => $totalBooks,
            'totalLoans' => $totalLoans,
            'pendingRequests' => $pendingRequests,
            'totalUsers' => $totalUsers,
            'startDate' => $start,
            'endDate' => $end,
            'booksAdded' => $booksAdded,
            'booksDeleted' => $booksDeleted,
        ]);
    }
}
