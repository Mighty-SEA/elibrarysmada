<?php

namespace App\Exports;

use App\Models\Buku;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BooksExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Buku::select('judul', 'penulis', 'penerbit', 'tahun_terbit', 'isbn', 'jumlah', 'lokasi', 'deskripsi', 'kategori', 'cover', 'cover_type')->get();
    }

    public function headings(): array
    {
        return [
            'Judul',
            'Penulis',
            'Penerbit',
            'Tahun Terbit',
            'ISBN',
            'Jumlah',
            'Lokasi',
            'Deskripsi',
            'Kategori',
            'Cover',
            'Cover Type',
        ];
    }
} 