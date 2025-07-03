<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Buku;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

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
            $loanQuery->whereDate('request_date', '>=', $start);
            $pendingQuery->whereDate('request_date', '>=', $start);
            $userQuery->whereDate('created_at', '>=', $start);
        }
        if ($end) {
            $loanQuery->whereDate('request_date', '<=', $end);
            $pendingQuery->whereDate('request_date', '<=', $end);
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

        // Chart per bulan
        $loanChartMonthly = [];
        $userChartMonthly = [];
        $showMonthlyCharts = false;

        // Jika ada tanggal start dan end, periksa apakah rentangnya lebih dari satu bulan
        if ($start && $end) {
            $startDate = Carbon::parse($start);
            $endDate = Carbon::parse($end);
            
            // Jika rentang lebih dari satu bulan atau bulan berbeda
            if ($startDate->format('Ym') != $endDate->format('Ym')) {
                $showMonthlyCharts = true;

                // Ambil semua peminjaman per bulan berdasarkan request_date
                $loanChartMonthly = $loans
                    ->groupBy(function($loan) {
                        return Carbon::parse($loan->request_date)->format('Y-m');
                    })
                    ->map(function($group) {
                        return $group->count();
                    })
                    ->sortKeys();

                // Ambil semua user yang dibuat per bulan
                $userChartMonthly = $users
                    ->groupBy(function($user) {
                        return Carbon::parse($user->created_at)->format('Y-m');
                    })
                    ->map(function($group) {
                        return $group->count();
                    })
                    ->sortKeys();

                // Format label bulan menjadi lebih ramah pengguna
                $loanChartMonthly = $loanChartMonthly->mapWithKeys(function($value, $key) {
                    $date = Carbon::createFromFormat('Y-m', $key);
                    return [$date->translatedFormat('M Y') => $value];
                });

                $userChartMonthly = $userChartMonthly->mapWithKeys(function($value, $key) {
                    $date = Carbon::createFromFormat('Y-m', $key);
                    return [$date->translatedFormat('M Y') => $value];
                });
            }
        }

        $totalEdisi = $bookQuery->sum('eksemplar');
        return Inertia::render('Dashboard', [
            'totalBooks' => $totalBooks,
            'totalEdisi' => $totalEdisi,
            'totalLoans' => $totalLoans,
            'pendingRequests' => $pendingRequests,
            'totalUsers' => $totalUsers,
            'startDate' => $start,
            'endDate' => $end,
            'booksAdded' => $booksAdded,
            'booksDeleted' => $booksDeleted,
            'loanChart' => $loanChart,
            'userChart' => $userChart,
            'loanChartMonthly' => $loanChartMonthly,
            'userChartMonthly' => $userChartMonthly,
            'showMonthlyCharts' => $showMonthlyCharts,
        ]);
    }

    public function export(Request $request)
    {
        $start = $request->query('start');
        $end = $request->query('end');

        $bookQuery = Buku::query();
        $loanQuery = Loan::query();
        $userQuery = User::query()->where('role', '!=', 'administrasi');
        $pendingQuery = Loan::where('status', 'belum_diambil');

        if ($start) {
            $loanQuery->whereDate('request_date', '>=', $start);
            $pendingQuery->whereDate('request_date', '>=', $start);
            $userQuery->whereDate('created_at', '>=', $start);
        }
        if ($end) {
            $loanQuery->whereDate('request_date', '<=', $end);
            $pendingQuery->whereDate('request_date', '<=', $end);
            $userQuery->whereDate('created_at', '<=', $end);
        }

        $loans = $loanQuery->whereIn('status', ['dipinjam', 'terlambat', 'dikembalikan'])->with(['user', 'book'])->get();
        $users = $userQuery->get();

        $totalBooks = $bookQuery->count();
        $totalLoans = $loans->count();
        $pendingRequests = $pendingQuery->count();
        $totalUsers = $users->count();

        $loanChart = $loans
            ->groupBy(function($loan) {
                $jk = $loan->user->jenis_kelamin ?? '-';
                $jurusan = $loan->user->jurusan ?? '-';
                return trim($jk . ' ' . $jurusan);
            })
            ->map(function($group) {
                return $group->count();
            });

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

        // Chart per bulan
        $loanChartMonthly = [];
        $userChartMonthly = [];
        $showMonthlyCharts = false;

        // Jika ada tanggal start dan end, periksa apakah rentangnya lebih dari satu bulan
        if ($start && $end) {
            $startDate = Carbon::parse($start);
            $endDate = Carbon::parse($end);
            
            // Jika rentang lebih dari satu bulan atau bulan berbeda
            if ($startDate->format('Ym') != $endDate->format('Ym')) {
                $showMonthlyCharts = true;

                // Ambil semua peminjaman per bulan berdasarkan request_date
                $loanChartMonthly = $loans
                    ->groupBy(function($loan) {
                        return Carbon::parse($loan->request_date)->format('Y-m');
                    })
                    ->map(function($group) {
                        return $group->count();
                    })
                    ->sortKeys();

                // Ambil semua user yang dibuat per bulan
                $userChartMonthly = $users
                    ->groupBy(function($user) {
                        return Carbon::parse($user->created_at)->format('Y-m');
                    })
                    ->map(function($group) {
                        return $group->count();
                    })
                    ->sortKeys();

                // Format label bulan menjadi lebih ramah pengguna
                $loanChartMonthly = $loanChartMonthly->mapWithKeys(function($value, $key) {
                    $date = Carbon::createFromFormat('Y-m', $key);
                    return [$date->translatedFormat('M Y') => $value];
                });

                $userChartMonthly = $userChartMonthly->mapWithKeys(function($value, $key) {
                    $date = Carbon::createFromFormat('Y-m', $key);
                    return [$date->translatedFormat('M Y') => $value];
                });
            }
        }

        return Inertia::render('DashboardExport', [
            'totalBooks' => $totalBooks,
            'totalLoans' => $totalLoans,
            'pendingRequests' => $pendingRequests,
            'totalUsers' => $totalUsers,
            'loanChart' => $loanChart,
            'userChart' => $userChart,
            'loanChartMonthly' => $loanChartMonthly,
            'userChartMonthly' => $userChartMonthly,
            'showMonthlyCharts' => $showMonthlyCharts,
            'loans' => $loans,
            'users' => $users,
            'startDate' => $start,
            'endDate' => $end,
            'autoDownload' => true,
        ]);
    }
}
