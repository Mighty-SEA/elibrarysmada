<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Buku extends Model
{
    use HasFactory;

    protected $table = 'books';

    protected $fillable = [
        'judul',
        'penulis',
        'penerbit',
        'tahun_terbit',
        'isbn',
        'jumlah',
        'lokasi',
        'deskripsi',
        'kategori',
        'cover',
        'cover_type',
    ];

    /**
     * Get the kategori as an array
     */
    public function getKategoriListAttribute()
    {
        if (!$this->kategori) {
            return [];
        }
        
        return array_map('trim', explode(',', $this->kategori));
    }

    /**
     * Get the cover URL
     */
    public function getCoverUrlAttribute()
    {
        if ($this->cover_type === 'url') {
            return $this->cover;
        } else if ($this->cover) {
            return '/storage/' . $this->cover;
        }
        
        return null;
    }

    /**
     * Get all loans for this book
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class, 'book_id');
    }

    /**
     * Get active loans (belum_diambil, dipinjam, terlambat)
     */
    public function activeLoans(): HasMany
    {
        return $this->loans()->whereIn('status', ['belum_diambil', 'dipinjam', 'terlambat']);
    }

    /**
     * Decrease book stock by given amount
     * 
     * @param int $amount Amount to decrease
     * @return bool Whether the operation was successful
     */
    public function decreaseStock(int $amount = 1): bool
    {
        if ($this->jumlah < $amount) {
            return false;
        }
        
        $this->jumlah -= $amount;
        return $this->save();
    }

    /**
     * Increase book stock by given amount
     * 
     * @param int $amount Amount to increase
     * @return bool Whether the operation was successful
     */
    public function increaseStock(int $amount = 1): bool
    {
        $this->jumlah += $amount;
        return $this->save();
    }

    /**
     * Check if book is available for borrowing
     * 
     * @return bool Whether the book is available
     */
    public function isAvailable(): bool
    {
        return $this->jumlah > 0;
    }
} 