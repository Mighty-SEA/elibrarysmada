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
            $book->cover_url = $book->cover ? '/storage/' . $book->cover : null;
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
        Log::info('Request all', ['data' => $request->all()]);
        Log::info('Has file cover', ['has_file' => $request->hasFile('cover')]);
        
        if ($request->hasFile('cover')) {
            Log::info('Cover file info', [
                'name' => $request->file('cover')->getClientOriginalName(),
                'size' => $request->file('cover')->getSize(),
                'mime' => $request->file('cover')->getMimeType(),
            ]);
        }
        
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
            'cover' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('cover')) {
            // Buat nama file dari judul buku
            $judul = $validated['judul'];
            $slug = Str::slug($judul);
            $extension = $request->file('cover')->getClientOriginalExtension();
            $filename = $slug . '-' . time() . '.' . $extension;
            
            // Simpan file dengan nama yang sudah dibuat
            $validated['cover'] = $request->file('cover')->storeAs('covers', $filename, 'public');
        }

        Buku::create($validated);
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
        Log::info('Update request all', ['data' => $request->all()]);
        Log::info('Has file cover', ['has_file' => $request->hasFile('cover')]);
        
        if ($request->hasFile('cover')) {
            Log::info('Cover file info', [
                'name' => $request->file('cover')->getClientOriginalName(),
                'size' => $request->file('cover')->getSize(),
                'mime' => $request->file('cover')->getMimeType(),
            ]);
        }
        
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
            'cover' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('cover')) {
            // Hapus cover lama jika ada
            if ($book->cover) {
                Storage::disk('public')->delete($book->cover);
            }
            
            // Buat nama file dari judul buku
            $judul = $validated['judul'];
            $slug = Str::slug($judul);
            $extension = $request->file('cover')->getClientOriginalExtension();
            $filename = $slug . '-' . time() . '.' . $extension;
            
            // Simpan file dengan nama yang sudah dibuat
            $validated['cover'] = $request->file('cover')->storeAs('covers', $filename, 'public');
        }

        $book->update($validated);
        return redirect()->route('books.index')->with('success', 'Buku berhasil diupdate.');
    }

    public function destroy(Buku $book)
    {
        $book->delete();
        return redirect()->route('books.index')->with('success', 'Buku berhasil dihapus.');
    }
} 