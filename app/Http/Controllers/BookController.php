<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Redirect;

class BookController extends Controller
{
    public function index()
    {
        $perPage = 10; // Jumlah item per halaman
        $books = Buku::paginate($perPage);
        
        // Tambahkan cover_url ke setiap buku
        $books->through(function ($book) {
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
            'eksemplar' => 'nullable|integer',
            'ketersediaan' => 'nullable|integer',
            'no_panggil' => 'nullable',
            'asal_koleksi' => 'nullable',
            'kota_terbit' => 'nullable',
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
        
        // Set ketersediaan to match jumlah
        $validated['ketersediaan'] = $validated['eksemplar'];
        
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
            'eksemplar' => 'nullable|integer',
            'ketersediaan' => 'nullable|integer',
            'no_panggil' => 'nullable',
            'asal_koleksi' => 'nullable',
            'kota_terbit' => 'nullable',
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
        
        // Make sure ketersediaan doesn't exceed jumlah
        if (!isset($validated['ketersediaan'])) {
            $validated['ketersediaan'] = $validated['eksemplar'];
        } else if ($validated['ketersediaan'] > $validated['eksemplar']) {
            $validated['ketersediaan'] = $validated['eksemplar'];
        }
        
        $book->update($validated);
        return redirect()->route('books.index')->with('success', 'Buku berhasil diupdate.');
    }

    public function destroy(Buku $book)
    {
        $book->delete();
        return redirect()->route('books.index')->with('success', 'Buku berhasil dihapus.');
    }

    public function exportBooks()
    {
        try {
            $fileName = 'buku_export_' . date('Ymd_His') . '.xlsx';
            return Excel::download(new \App\Exports\BooksExport, $fileName);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error exporting books: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengekspor buku: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls',
        ]);
        Excel::import(new \App\Imports\BooksImport, $request->file('file'));
        return Redirect::route('books.index')->with('success', 'Import data buku berhasil!');
    }

    public function home(Request $request)
    {
        $query = $request->query('search', '');
        
        $books = Buku::all()->map(function ($book) {
            if ($book->cover_type === 'url') {
                $book->cover_url = $book->cover;
            } else if ($book->cover) {
                $book->cover_url = '/storage/' . $book->cover;
            } else {
                $book->cover_url = null;
            }
            // Kategori bisa lebih dari satu, dipisah koma
            $book->kategori_list = $book->kategori ? array_map('trim', explode(',', $book->kategori)) : [];
            return $book;
        });
        
        return Inertia::render('Home', [
            'books' => $books,
            'searchQuery' => $query,
        ]);
    }

    public function detail(Buku $book)
    {
        // Siapkan data buku untuk detail view
        if ($book->cover_type === 'url') {
            $book->cover_url = $book->cover;
        } else if ($book->cover) {
            $book->cover_url = '/storage/' . $book->cover;
        } else {
            $book->cover_url = null;
        }
        
        // Proses kategori menjadi array untuk tampilan
        $book->kategori_list = $book->kategori ? array_map('trim', explode(',', $book->kategori)) : [];
        
        // Hitung jumlah peminjaman dalam status "belum_diambil"
        $book->pending_loans_count = $book->loans()->where('status', 'belum_diambil')->count();
        
        // Hitung jumlah peminjaman dalam status "dipinjam" atau "terlambat"
        $book->borrowed_loans_count = $book->loans()->whereIn('status', ['dipinjam', 'terlambat'])->count();
        
        // Ambil buku-buku terkait berdasarkan kategori
        $relatedBooks = Buku::where('id', '!=', $book->id)
            ->whereRaw('FIND_IN_SET(?, REPLACE(kategori, ", ", ","))', [$book->kategori_list[0] ?? ''])
            ->orWhereRaw('FIND_IN_SET(?, REPLACE(kategori, ", ", ","))', [$book->kategori_list[1] ?? ''])
            ->limit(6)
            ->get()
            ->map(function ($relatedBook) {
                if ($relatedBook->cover_type === 'url') {
                    $relatedBook->cover_url = $relatedBook->cover;
                } else if ($relatedBook->cover) {
                    $relatedBook->cover_url = '/storage/' . $relatedBook->cover;
                } else {
                    $relatedBook->cover_url = null;
                }
                $relatedBook->kategori_list = $relatedBook->kategori ? array_map('trim', explode(',', $relatedBook->kategori)) : [];
                return $relatedBook;
            });

        return Inertia::render('BookDetail', [
            'book' => $book,
            'relatedBooks' => $relatedBooks
        ]);
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:books,id'
        ]);

        $count = Buku::whereIn('id', $request->ids)->count();
        Buku::whereIn('id', $request->ids)->delete();
        
        return response()->json([
            'success' => true,
            'message' => $count . ' buku berhasil dihapus.'
        ]);
    }
    
    public function bulkUpdateJumlah(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:books,id',
            'eksemplar' => 'required|integer|min:0'
        ]);
        
        $count = Buku::whereIn('id', $request->ids)->count();
        Buku::whereIn('id', $request->ids)->update([
            'eksemplar' => $request->eksemplar,
            'ketersediaan' => $request->eksemplar
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Ekselampar ' . $count . ' buku berhasil diperbarui.'
        ]);
    }

    public function getAllBookIds()
    {
        try {
            $ids = Buku::pluck('id')->values()->toArray();
            return response()->json(['ids' => $ids]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in getAllBookIds: ' . $e->getMessage());
            return response()->json(['ids' => [], 'error' => 'Failed to retrieve book IDs'], 500);
        }
    }
} 