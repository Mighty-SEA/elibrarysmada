<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Loan;
use App\Models\Buku;
use Carbon\Carbon;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat admin
        User::create([
            'id' => '2025001',
            'name' => 'Administrator',
            'username' => 'admin',
            'password' => Hash::make('password'),
            'role' => 'administrasi',
            'tahun_angkatan' => 2025,
            'nomor_telepon' => '081234567890',
        ]);
        
        // Buat guru
        User::create([
            'id' => '2025002',
            'name' => 'Bambang Setiawan',
            'username' => 'guru',
            'password' => Hash::make('password'),
            'role' => 'guru',
            'tahun_angkatan' => 2025,
            'nomor_telepon' => '081298765432',
        ]);
        
        // Buat murid
        User::create([
            'id' => '2025003',
            'name' => 'Dimas Pratama',
            'username' => 'murid',
            'password' => Hash::make('password'),
            'role' => 'murid',
            'tahun_angkatan' => 2025,
            'jenis_kelamin' => 'Laki-laki',
            'jurusan' => 'IPA',
            'nomor_telepon' => '085712345678',
        ]);

        // Seeder 100 murid
        \App\Models\User::factory()->count(100)->create([
            'role' => 'murid',
            'jenis_kelamin' => fn() => fake()->randomElement(['Laki-laki', 'Perempuan']),
            'jurusan' => fn() => fake()->randomElement(['IPA', 'IPS']),
            'tahun_angkatan' => 2025,
        ]);
        
        // Buat data buku
        $this->createBooks();
        
        // Jalankan LoanSeeder
        $this->call(LoanSeeder::class);
    }
    
    /**
     * Membuat data buku untuk pengujian
     */
    private function createBooks(): void
    {
        $categories = ['Fiksi', 'Non-Fiksi', 'Sejarah', 'Sains', 'Teknologi', 'Matematika', 'Bahasa', 'Sastra', 'Komputer'];
        $publishers = ['Gramedia', 'Erlangga', 'Mizan', 'Pustaka Jaya', 'Balai Pustaka', 'Grasindo'];
        
        // Buat 50 buku
        for ($i = 1; $i <= 50; $i++) {
            $title = 'Buku ' . $i;
            $author = fake()->name();
            $year = rand(2010, 2024);
            $eksemplar = rand(1, 10);
            
            // Pilih 1-3 kategori secara acak
            $bookCategories = [];
            $numCategories = rand(1, 3);
            for ($j = 0; $j < $numCategories; $j++) {
                $bookCategories[] = $categories[array_rand($categories)];
            }
            $bookCategories = array_unique($bookCategories);
            
            Buku::create([
                'judul' => $title,
                'penulis' => $author,
                'penerbit' => $publishers[array_rand($publishers)],
                'tahun_terbit' => $year,
                'isbn' => fake()->isbn13(),
                'eksemplar' => $eksemplar,
                'ketersediaan' => $eksemplar,
                'no_panggil' => strtoupper(substr($author, 0, 1)) . '-' . $i,
                'asal_koleksi' => 'Pembelian',
                'kota_terbit' => fake()->city(),
                'deskripsi' => fake()->paragraph(),
                'kategori' => implode(', ', $bookCategories),
                'cover_type' => 'url',
                'cover' => 'https://picsum.photos/seed/book' . $i . '/300/400',
                'created_at' => Carbon::create(2025, rand(1, 6), rand(1, 28)),
            ]);
        }
    }
}
