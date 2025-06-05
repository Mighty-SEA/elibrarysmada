<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    /**
     * Menampilkan halaman keranjang
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Cart/Index');
    }
}
