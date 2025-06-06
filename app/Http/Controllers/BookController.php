<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BookController extends Controller
{
    public function index()
    {
        $books = Buku::all()->map(function ($book) {
            if ($book->cover_type === 'url') {
                $book->cover_url = $book->cover;
            } else if ($book->cover) {
                $book->cover_url = '/storage/' . $book->cover;
            } else {
                $book->cover_url = null;
            }
            return $book;
        });
        
        return Inertia::render('BookManagement/Index', [
            'books' => $books
        ]);
    }

    public function create()
    {
        return Inertia::render('BookManagement/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required',
            'penulis' => 'nullable',
            'penerbit' => 'nullable',
            'tahun_terbit' => 'nullable',
            'isbn' => 'nullable',
            'jumlah' => 'nullable|integer',
            'lokasi' => 'nullable',
            'deskripsi' => 'nullable',
            'kategori' => 'nullable',
            'cover_type' => 'required|in:upload,url',
            'cover' => 'nullable',
        ]);

        if ($request->cover_type === 'upload') {
            $request->validate([
                'cover' => 'nullable|image|max:2048',
            ]);
            if ($request->hasFile('cover')) {
                $judul = $validated['judul'];
                $slug = \Illuminate\Support\Str::slug($judul);
                $extension = $request->file('cover')->getClientOriginalExtension();
                $filename = $slug . '-' . time() . '.' . $extension;
                $validated['cover'] = $request->file('cover')->storeAs('covers', $filename, 'public');
            } else {
                $validated['cover'] = null;
            }
        } else if ($request->cover_type === 'url') {
            $request->validate([
                'cover' => 'required|url',
            ]);
            $validated['cover'] = $request->cover;
        }
        $validated['cover_type'] = $request->cover_type;
        
        \App\Models\Buku::create($validated);
        return redirect()->route('books.index')->with('success', 'Buku berhasil ditambahkan.');
    }

    public function show(Buku $book)
    {
        $book->cover_url = $book->cover ? '/storage/' . $book->cover : null;
        
        return Inertia::render('BookManagement/Show', [
            'book' => $book
        ]);
    }

    public function edit(Buku $book)
    {
        $book->cover_url = $book->cover ? '/storage/' . $book->cover : null;
        
        return Inertia::render('BookManagement/Edit', [
            'book' => $book
        ]);
    }

    public function update(Request $request, Buku $book)
    {
        $validated = $request->validate([
            'judul' => 'required',
            'penulis' => 'nullable',
            'penerbit' => 'nullable',
            'tahun_terbit' => 'nullable',
            'isbn' => 'nullable',
            'jumlah' => 'nullable|integer',
            'lokasi' => 'nullable',
            'deskripsi' => 'nullable',
            'kategori' => 'nullable',
            'cover_type' => 'required|in:upload,url',
            'cover' => 'nullable',
        ]);

        if ($request->cover_type === 'upload') {
            $request->validate([
                'cover' => 'nullable|image|max:2048',
            ]);
            if ($request->hasFile('cover')) {
                if ($book->cover && $book->cover_type === 'upload') {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($book->cover);
                }
                $judul = $validated['judul'];
                $slug = \Illuminate\Support\Str::slug($judul);
                $extension = $request->file('cover')->getClientOriginalExtension();
                $filename = $slug . '-' . time() . '.' . $extension;
                $validated['cover'] = $request->file('cover')->storeAs('covers', $filename, 'public');
            } else {
                $validated['cover'] = $book->cover_type === 'upload' ? $book->cover : null;
            }
        } else if ($request->cover_type === 'url') {
            $request->validate([
                'cover' => 'required|url',
            ]);
            if ($book->cover && $book->cover_type === 'upload') {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($book->cover);
            }
            $validated['cover'] = $request->cover;
        }
        $validated['cover_type'] = $request->cover_type;
        $book->update($validated);
        return redirect()->route('books.index')->with('success', 'Buku berhasil diupdate.');
    }

    public function destroy(Buku $book)
    {
        $book->delete();
        return redirect()->route('books.index')->with('success', 'Buku berhasil dihapus.');
    }
} 