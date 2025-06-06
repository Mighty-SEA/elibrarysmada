<?php

namespace App\Imports;

use App\Models\Buku;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BooksImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Buku([
            'judul' => $row['judul'] ?? '',
            'penulis' => $row['penulis'] ?? '',
            'penerbit' => $row['penerbit'] ?? '',
            'tahun_terbit' => isset($row['tahun_terbit']) && $row['tahun_terbit'] !== '' ? $row['tahun_terbit'] : null,
            'isbn' => $row['isbn'] ?? '',
            'jumlah' => isset($row['jumlah']) && $row['jumlah'] !== '' ? $row['jumlah'] : null,
            'lokasi' => $row['lokasi'] ?? '',
            'deskripsi' => $row['deskripsi'] ?? '',
            'kategori' => $row['kategori'] ?? '',
            'cover' => $row['cover'] ?? '',
            'cover_type' => $row['cover_type'] ?? 'upload',
        ]);
    }
} 