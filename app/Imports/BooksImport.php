<?php

namespace App\Imports;

use App\Models\Buku;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BooksImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $eksemplar = isset($row['eksemplar']) && $row['eksemplar'] !== '' ? $row['eksemplar'] : null;
        $ketersediaan = isset($row['ketersediaan']) && $row['ketersediaan'] !== '' ? $row['ketersediaan'] : $eksemplar;
        return new Buku([
            'judul' => $row['judul'] ?? '',
            'penulis' => $row['penulis'] ?? '',
            'penerbit' => $row['penerbit'] ?? '',
            'tahun_terbit' => isset($row['tahun_terbit']) && $row['tahun_terbit'] !== '' ? $row['tahun_terbit'] : null,
            'isbn' => $row['isbn'] ?? '',
            'eksemplar' => $eksemplar,
            'ketersediaan' => $ketersediaan,
            'no_panggil' => $row['no_panggil'] ?? '',
            'asal_koleksi' => $row['asal_koleksi'] ?? '',
            'kota_terbit' => $row['kota_terbit'] ?? '',
            'deskripsi' => $row['deskripsi'] ?? '',
            'kategori' => $row['kategori'] ?? '',
            'cover' => $row['cover'] ?? '',
            'cover_type' => $row['cover_type'] ?? 'upload',
        ]);
    }
} 