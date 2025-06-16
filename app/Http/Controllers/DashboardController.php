<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Buku;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

        // Query Loan dan User sudah difilter tanggal, gunakan untuk statistik dan chart
        $loans = $loanQuery->whereIn('status', ['dipinjam', 'terlambat', 'dikembalikan'])->with('user')->get();
        $users = $userQuery->get();

        $totalBooks = $bookQuery->count();
        $totalLoans = $loans->count();
        $pendingRequests = $pendingQuery->count();
        $totalUsers = $users->count();

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

        // Data chart 1: peminjam per jurusan+jenis_kelamin
        $loanChart = $loans
            ->groupBy(function($loan) {
                $jk = $loan->user->jenis_kelamin ?? '-';
                $jurusan = $loan->user->jurusan ?? '-';
                return trim($jk . ' ' . $jurusan);
            })
            ->map(function($group) {
                return $group->count();
            });

        // Data chart 2: user per jurusan+jenis_kelamin (hanya murid)
        $userChart = $users
            ->where('role', 'murid')
            ->groupBy(function($user) {
                $jk = $user->jenis_kelamin ?? '-';
                $jurusan = $user->jurusan ?? '-';
                return trim($jk . ' ' . $jurusan);
            })
            ->map(function($group) {
                return $group->count();
            });

        return Inertia::render('Dashboard', [
            'totalBooks' => $totalBooks,
            'totalLoans' => $totalLoans,
            'pendingRequests' => $pendingRequests,
            'totalUsers' => $totalUsers,
            'startDate' => $start,
            'endDate' => $end,
            'booksAdded' => $booksAdded,
            'booksDeleted' => $booksDeleted,
            'loanChart' => $loanChart,
            'userChart' => $userChart,
        ]);
    }
}
