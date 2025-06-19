<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Buku extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'books';

    protected $fillable = [
        'judul',
        'penulis',
        'penerbit',
        'tahun_terbit',
        'isbn',
        'eksemplar',
        'ketersediaan',
        'no_panggil',
        'asal_koleksi',
        'kota_terbit',
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
     * Decrease book availability by given amount
     * 
     * @param int $amount Amount to decrease
     * @return bool Whether the operation was successful
     */
    public function decreaseAvailability(int $amount = 1): bool
    {
        if ($this->ketersediaan < $amount) {
            return false;
        }
        
        $this->ketersediaan -= $amount;
        return $this->save();
    }

    /**
     * Increase book availability by given amount
     * 
     * @param int $amount Amount to increase
     * @return bool Whether the operation was successful
     */
    public function increaseAvailability(int $amount = 1): bool
    {
        $this->ketersediaan += $amount;
        if ($this->ketersediaan > $this->eksemplar) {
            $this->ketersediaan = $this->eksemplar;
        }
        return $this->save();
    }
    
    /**
     * Decrease book total eksemplar by given amount
     * This affects both eksemplar and ketersediaan
     * 
     * @param int $amount Amount to decrease
     * @return bool Whether the operation was successful
     */
    public function decreaseEksemplar(int $amount = 1): bool
    {
        if ($this->eksemplar < $amount) {
            return false;
        }
        $this->eksemplar -= $amount;
        if ($this->ketersediaan > $this->eksemplar) {
            $this->ketersediaan = $this->eksemplar;
        }
        return $this->save();
    }

    /**
     * Increase book total eksemplar by given amount
     * This affects both eksemplar and ketersediaan
     * 
     * @param int $amount Amount to increase
     * @return bool Whether the operation was successful
     */
    public function increaseEksemplar(int $amount = 1): bool
    {
        $this->eksemplar += $amount;
        $this->ketersediaan += $amount;
        return $this->save();
    }

    /**
     * Check if book is available for borrowing
     * 
     * @return bool Whether the book is available
     */
    public function isAvailable(): bool
    {
        return $this->ketersediaan > 0;
    }
} 