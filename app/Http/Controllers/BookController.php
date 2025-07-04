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
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 10; // Jumlah item per halaman
        $search = $request->input('search', '');
        
        $query = Buku::query()->select('id', 'judul', 'penulis', 'penerbit', 'tahun_terbit', 'isbn', 'no_panggil', 'kategori', 'eksemplar', 'ketersediaan', 'asal_koleksi', 'kota_terbit', 'cover', 'cover_type');
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('penulis', 'like', "%{$search}%")
                  ->orWhere('penerbit', 'like', "%{$search}%")
                  ->orWhere('tahun_terbit', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhere('no_panggil', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%");
            });
        }
        
        $books = $query->paginate($perPage)->withQueryString();
        
        // Tambahkan cover_url ke setiap buku dengan cara yang lebih efisien
        foreach ($books as $book) {
            if ($book->cover_type === 'url') {
                $book->cover_url = $book->cover;
            } else if ($book->cover) {
                $book->cover_url = '/storage/' . $book->cover;
            } else {
                $book->cover_url = null;
            }
        }
        
        return Inertia::render('BookManagement/Index', [
            'books' => $books,
            'filters' => [
                'search' => $search
            ]
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
        $perPage = 20; // Jumlah buku per halaman
        $query = $request->query('search', '');
        $categories = $request->query('categories', '');
        
        $booksQuery = Buku::query()->select('id', 'judul', 'penulis', 'penerbit', 'tahun_terbit', 'isbn', 'kategori', 'eksemplar', 'ketersediaan', 'cover', 'cover_type');
        
        // Filter berdasarkan pencarian
        if ($query) {
            $booksQuery->where(function($q) use ($query) {
                $q->where('judul', 'like', "%{$query}%")
                  ->orWhere('penulis', 'like', "%{$query}%")
                  ->orWhere('penerbit', 'like', "%{$query}%")
                  ->orWhere('isbn', 'like', "%{$query}%")
                  ->orWhere('kategori', 'like', "%{$query}%");
            });
        }
        
        // Filter berdasarkan kategori jika ada
        if ($categories) {
            $categoryArray = explode(',', $categories);
            $booksQuery->where(function($q) use ($categoryArray) {
                foreach ($categoryArray as $category) {
                    $q->orWhere('kategori', 'like', "%{$category}%");
                }
            });
        }
        
        $books = $booksQuery->paginate($perPage)->withQueryString();
        
        // Proses data buku untuk frontend
        foreach ($books as $book) {
            if ($book->cover_type === 'url') {
                $book->cover_url = $book->cover;
            } else if ($book->cover) {
                $book->cover_url = '/storage/' . $book->cover;
            } else {
                $book->cover_url = null;
            }
            // Kategori bisa lebih dari satu, dipisah koma
            $book->kategori_list = $book->kategori ? array_map('trim', explode(',', $book->kategori)) : [];
        }
        
        // Ambil semua kategori unik untuk filter sidebar
        $allCategories = DB::table('books')
                            ->select(\DB::raw('DISTINCT kategori'))
                            ->whereNotNull('kategori')
                            ->get()
                            ->flatMap(function($item) {
                                return array_map('trim', explode(',', $item->kategori));
                            })
                            ->unique()
                            ->values()
                            ->toArray();
        
        return Inertia::render('Home', [
            'books' => $books,
            'searchQuery' => $query,
            'selectedCategories' => $categories ? explode(',', $categories) : [],
            'allCategories' => $allCategories
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
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:books,id',
        ]);
        
        $ids = $validated['ids'];
        $chunkSize = 100; // Proses 100 buku sekaligus
        $deleteCount = 0;
        
        // Hapus cover file untuk buku-buku dengan cover upload (jika ada)
        // Proses ini dilakukan dalam chunk untuk mencegah overload
        foreach (array_chunk($ids, $chunkSize) as $chunk) {
            $books = Buku::whereIn('id', $chunk)
                      ->where('cover_type', '!=', 'url')
                      ->whereNotNull('cover')
                      ->get(['id', 'cover']);
            
            foreach ($books as $book) {
                if ($book->cover) {
                    Storage::disk('public')->delete($book->cover);
                }
            }
            
            // Hapus buku dalam chunk
            $deleted = Buku::whereIn('id', $chunk)->delete();
            $deleteCount += $deleted;
        }
        
        return response()->json([
            'success' => true,
            'message' => "{$deleteCount} buku berhasil dihapus.",
        ]);
    }
    
    public function bulkUpdateJumlah(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:books,id',
            'eksemplar' => 'required|integer|min:0',
        ]);
        
        $ids = $validated['ids'];
        $eksemplar = $validated['eksemplar'];
        $chunkSize = 100; // Proses 100 buku sekaligus
        $updateCount = 0;
        
        // Proses update dalam chunk
        foreach (array_chunk($ids, $chunkSize) as $chunk) {
            $affected = Buku::whereIn('id', $chunk)
                ->update([
                    'eksemplar' => $eksemplar,
                    'ketersediaan' => $eksemplar, // Reset ketersediaan juga
                ]);
            
            $updateCount += $affected;
        }
        
        return response()->json([
            'success' => true,
            'message' => "{$updateCount} buku berhasil diupdate.",
        ]);
    }

    public function getAllBookIds()
    {
        // Batasi jumlah maksimum ID yang diambil untuk mencegah overload server
        $maxBooks = 1000;
        $ids = Buku::select('id')->limit($maxBooks)->pluck('id')->toArray();
        
        $totalBooks = Buku::count();
        
        $response = [
            'ids' => $ids,
            'total' => $totalBooks,
            'limited' => $totalBooks > $maxBooks
        ];
        
        return response()->json($response);
    }

    /**
     * Menampilkan daftar buku yang telah dihapus (sampah)
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function recycle(Request $request)
    {
        $perPage = 10; // Jumlah item per halaman
        $search = $request->input('search', '');
        
        $query = Buku::onlyTrashed();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('penulis', 'like', "%{$search}%")
                  ->orWhere('penerbit', 'like', "%{$search}%")
                  ->orWhere('tahun_terbit', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhere('no_panggil', 'like', "%{$search}%")
                  ->orWhere('kategori', 'like', "%{$search}%");
            });
        }
        
        $books = $query->orderBy('deleted_at', 'desc')->paginate($perPage)->withQueryString();
        
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
        
        return Inertia::render('BookManagement/Recycle', [
            'books' => $books,
            'filters' => [
                'search' => $search
            ]
        ]);
    }
    
    /**
     * Memulihkan buku yang telah dihapus
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        $book = Buku::onlyTrashed()->findOrFail($id);
        $book->restore();
        
        return redirect()->route('books.recycle')->with('success', 'Buku berhasil dipulihkan.');
    }
} 