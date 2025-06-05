<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Dashboard');
    }
}
