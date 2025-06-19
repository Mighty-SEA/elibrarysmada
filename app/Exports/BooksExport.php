<?php

namespace App\Exports;

use App\Models\Buku;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BooksExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Buku::select('judul', 'penulis', 'penerbit', 'tahun_terbit', 'isbn', 'eksemplar', 'ketersediaan', 'no_panggil', 'asal_koleksi', 'kota_terbit', 'deskripsi', 'kategori', 'cover', 'cover_type')->get();
    }

    public function headings(): array
    {
        return [
            'Judul',
            'Penulis',
            'Penerbit',
            'Tahun Terbit',
            'ISBN',
            'Ekselampar',
            'Ketersediaan',
            'No. Panggil',
            'Asal Koleksi',
            'Kota Terbit',
            'Deskripsi',
            'Kategori',
            'Cover',
            'Cover Type',
        ];
    }
} 