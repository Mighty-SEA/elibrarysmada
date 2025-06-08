<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Cek apakah user memiliki role tertentu
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Cek apakah user adalah administrasi
     *
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->role === 'administrasi';
    }

    /**
     * Cek apakah user adalah guru
     *
     * @return bool
     */
    public function isGuru(): bool
    {
        return $this->role === 'guru';
    }

    /**
     * Cek apakah user adalah murid
     *
     * @return bool
     */
    public function isMurid(): bool
    {
        return $this->role === 'murid';
    }

    /**
     * Get all loans for this user
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * Get active loans (belum_diambil, dipinjam, terlambat)
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activeLoans()
    {
        return $this->loans()->whereIn('status', ['belum_diambil', 'dipinjam', 'terlambat']);
    }
}
