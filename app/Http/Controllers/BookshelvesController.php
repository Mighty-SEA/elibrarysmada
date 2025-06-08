<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class BookshelvesController extends Controller
{
    /**
     * Menampilkan halaman rak buku
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Bookshelves/Index');
    }
} 