<template>
  <div class="docs-container">
    <!-- Sidebar -->
    <div class="docs-sidebar">
      <div class="docs-logo">
        <a href="/">MyPerpus</a>
      </div>
      <ul class="docs-menu">
        <li>
          <Link :href="route('documentation')" class="docs-menu-item" :class="{ active: currentPage === 'overview' }">
            Overview
          </Link>
        </li>
        <li>
          <Link :href="route('documentation.initialization')" class="docs-menu-item" :class="{ active: currentPage === 'initialization' }">
            Inisialisasi
          </Link>
        </li>
        <li>
          <Link :href="route('documentation.migration')" class="docs-menu-item" :class="{ active: currentPage === 'migration' }">
            Migration
          </Link>
        </li>
        <li>
          <Link :href="route('documentation.route')" class="docs-menu-item" :class="{ active: currentPage === 'route' }">
            Route
          </Link>
        </li>
        <li>
          <Link :href="route('documentation.model')" class="docs-menu-item" :class="{ active: currentPage === 'model' }">
            Model
          </Link>
        </li>
        <li>
          <Link :href="route('documentation.controller')" class="docs-menu-item" :class="{ active: currentPage === 'controller' }">
            Controller
          </Link>
        </li>
        <li>
          <Link :href="route('documentation.view')" class="docs-menu-item" :class="{ active: currentPage === 'view' }">
            View
          </Link>
        </li>
        <li>
          <Link :href="route('documentation.database')" class="docs-menu-item" :class="{ active: currentPage === 'database' }">
            Database
          </Link>
        </li>
        <li>
          <Link :href="route('documentation.features')" class="docs-menu-item" :class="{ active: currentPage === 'features' }">
            Fitur
          </Link>
        </li>
        <li>
          <Link :href="route('documentation.deployment')" class="docs-menu-item" :class="{ active: currentPage === 'deployment' }">
            Deployment
          </Link>
        </li>
      </ul>
    </div>

    <!-- Main Content -->
    <div class="docs-content">
      <h1>Dokumentasi Implementasi MyPerpus</h1>
      
      <!-- Konten dinamis sesuai halaman yang dipilih -->
      <div v-if="currentPage === 'overview'" class="docs-page">
        <h2>Overview Implementasi</h2>
        <p>Dokumentasi ini berisi penjelasan teknis tentang implementasi aplikasi perpustakaan digital MyPerpus.</p>
        
        <h3>Teknologi yang Digunakan</h3>
        <ul>
          <li><strong>Backend:</strong> Laravel 12</li>
          <li><strong>Frontend:</strong> Vue.js 3 dengan TypeScript</li>
          <li><strong>Database:</strong> MySQL</li>
          <li><strong>UI:</strong> Tailwind CSS dengan Shadcn UI</li>
          <li><strong>Komunikasi:</strong> Inertia.js</li>
        </ul>
        
        <h3>Struktur Implementasi</h3>
        <p>Implementasi MyPerpus dibagi menjadi beberapa bagian utama:</p>
        <ol>
          <li><strong>Inisialisasi</strong> - Setup project dan konfigurasi awal</li>
          <li><strong>Arsitektur</strong> - Struktur dan desain arsitektur aplikasi</li>
          <li><strong>Database</strong> - Schema dan relasi database</li>
          <li><strong>Backend</strong> - Implementasi server-side</li>
          <li><strong>Frontend</strong> - Implementasi client-side</li>
          <li><strong>Fitur</strong> - Detail implementasi fitur-fitur utama</li>
          <li><strong>Deployment</strong> - Cara menjalankan aplikasi di production</li>
        </ol>
        
        <p>Silakan navigasi menggunakan menu di sidebar untuk melihat detail implementasi masing-masing bagian.</p>
      </div>

      <!-- Halaman Inisialisasi -->
      <div v-if="currentPage === 'initialization'" class="docs-page">
        <h2>Inisialisasi Project</h2>
        <p>Dokumen ini menjelaskan langkah-langkah awal untuk menyiapkan project MyPerpus.</p>
        
        <h3>Prasyarat</h3>
        <p>Sebelum memulai instalasi, pastikan sistem memiliki semua komponen yang dibutuhkan:</p>
        <ul>
          <li><strong>PHP >= 8.2</strong> - Dibutuhkan oleh Laravel 12</li>
          <li><strong>Composer</strong> - Package manager untuk PHP yang digunakan untuk menginstal Laravel dan dependensinya</li>
          <li><strong>Node.js >= 18</strong> - Dibutuhkan untuk menjalankan aplikasi frontend dan npm</li>
          <li><strong>MySQL atau MariaDB</strong> - Sistem manajemen basis data untuk menyimpan data aplikasi</li>
        </ul>
        
        <h3>Langkah-langkah Instalasi</h3>
        <div class="code-block">
          <pre><code class="language-php"># Install Laravel
composer create-project laravel/laravel myperpus

# Masuk ke direktori project
cd myperpus

# Install dependensi PHP
composer require inertiajs/inertia-laravel
composer require maatwebsite/excel
composer require laravel/breeze --dev

# Install Breeze dengan Vue dan Inertia
php artisan breeze:install vue
npm install

# Install dependensi JavaScript tambahan
npm install -D tailwindcss postcss autoprefixer 
npm install chart.js lucide-vue-next @vueuse/core

# Setup Tailwind CSS
npx tailwindcss init -p

# Migrasi database
php artisan migrate

# Seed database
php artisan db:seed</code></pre>
        </div>
        
        <h3>Penjelasan Tahapan Instalasi</h3>
        
        <h4>1. Instalasi Laravel</h4>
        <p>Menggunakan composer untuk membuat project Laravel baru dengan nama "myperpus". Proses ini akan mengunduh semua dependensi Laravel dasar dan membuat struktur project.</p>
        
        <h4>2. Instalasi Dependensi PHP</h4>
        <ul>
          <li><strong>inertiajs/inertia-laravel</strong>: Adapter server-side untuk Inertia.js, yang memungkinkan pengembangan aplikasi SPA tanpa membangun API terpisah</li>
          <li><strong>maatwebsite/excel</strong>: Library untuk mengimpor dan mengekspor data dalam format Excel, digunakan untuk fitur ekspor data buku dan peminjaman</li>
          <li><strong>laravel/breeze</strong>: Package autentikasi ringan dari Laravel untuk menyediakan fitur login, registrasi, reset password, dan verifikasi email</li>
        </ul>
        
        <h4>3. Instalasi Breeze dengan Vue</h4>
        <p>Laravel Breeze diinstal dengan opsi Vue.js, yang mengkonfigurasi struktur frontend dengan Vue 3 dan Inertia. Perintah ini akan mengatur:</p>
        <ul>
          <li>View untuk autentikasi (login, registrasi, reset password)</li>
          <li>Controller untuk autentikasi</li>
          <li>Route yang diperlukan</li>
          <li>Konfigurasi Vue.js dan Inertia.js</li>
            </ul>
            
        <h4>4. Instalasi Dependensi Frontend</h4>
        <p>Menginstal tools dan library yang dibutuhkan untuk pengembangan frontend:</p>
        <ul>
          <li><strong>tailwindcss</strong>: Framework CSS utility-first untuk styling UI</li>
          <li><strong>postcss</strong>: Tool untuk mentransformasi CSS dengan JavaScript</li>
          <li><strong>autoprefixer</strong>: Plugin PostCSS untuk menambahkan prefiks vendor</li>
          <li><strong>chart.js</strong>: Library untuk membuat visualisasi data dan grafik</li>
          <li><strong>lucide-vue-next</strong>: Library icon untuk Vue 3</li>
          <li><strong>@vueuse/core</strong>: Koleksi komposabel Vue untuk mempermudah pengembangan</li>
        </ul>
        
        <h4>5. Setup Database</h4>
        <p>Dua perintah terakhir digunakan untuk menginisialisasi database:</p>
        <ul>
          <li><strong>php artisan migrate</strong>: Membuat tabel-tabel di database berdasarkan file migrasi</li>
          <li><strong>php artisan db:seed</strong>: Mengisi database dengan data awal seperti user admin dan data sampel</li>
            </ul>
        
        <h3>Konfigurasi .env</h3>
        <p>Setelah instalasi, konfigurasikan file .env dengan pengaturan aplikasi dan database:</p>
        
        <div class="code-block">
          <pre><code class="language-php">APP_NAME=MyPerpus
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=myperpus
DB_USERNAME=root
DB_PASSWORD=

# ... konfigurasi lainnya</code></pre>
        </div>
        
        <p>Pastikan untuk mengganti nilai DB_USERNAME dan DB_PASSWORD sesuai dengan pengaturan database Anda.</p>
        
        <h3>Struktur Project</h3>
        <p>Project MyPerpus mengikuti struktur standar Laravel dengan beberapa penyesuaian:</p>
        <ul>
          <li><strong>app/</strong> - Kode inti aplikasi</li>
          <li><strong>resources/js/</strong> - Kode frontend Vue</li>
          <li><strong>resources/js/pages/</strong> - Halaman-halaman Vue</li>
          <li><strong>resources/js/components/</strong> - Komponen-komponen Vue yang dapat digunakan kembali</li>
          <li><strong>database/migrations/</strong> - Schema database</li>
          <li><strong>routes/</strong> - Definisi routing aplikasi</li>
        </ul>
            
        <h3>Menjalankan Aplikasi</h3>
        <p>Setelah instalasi selesai, jalankan aplikasi dengan perintah:</p>
        
        <div class="code-block">
          <pre><code class="language-php"># Terminal 1: Menjalankan server Laravel
php artisan serve

# Terminal 2: Menjalankan Vite untuk hot-reload frontend
npm run dev</code></pre>
        </div>
        
        <p>Aplikasi akan dapat diakses melalui <strong>http://localhost:8000</strong></p>
        </div>
        
      <!-- Halaman Database -->
      <div v-if="currentPage === 'database'" class="docs-page">
        <h2>Database Schema</h2>
        <p>Halaman ini menjelaskan struktur database MyPerpus.</p>
        <!-- Konten database akan ditambahkan di sini -->
        </div>
      
      <!-- Halaman Migration -->
      <div v-if="currentPage === 'migration'" class="docs-page">
        <h2>Migrations</h2>
        <p>Halaman ini menjelaskan implementasi migrations pada aplikasi MyPerpus.</p>
        
        <h3>Apa itu Migration</h3>
        <p>Migration dalam Laravel adalah cara untuk mengelola perubahan struktur database seperti membuat tabel, menambahkan kolom, atau mengubah tipe data kolom secara terprogram dan terkendali. Migration memungkinkan tim pengembangan melacak perubahan dalam schema database dan menerapkannya dengan mudah di lingkungan pengembangan dan produksi.</p>
        
        <h3>Daftar Migration</h3>
        <ul>
          <li><strong>create_users_table</strong> - Membuat tabel users untuk menyimpan data pengguna aplikasi</li>
          <li><strong>create_cache_table</strong> - Membuat tabel cache untuk menyimpan data cache aplikasi</li>
          <li><strong>create_jobs_table</strong> - Membuat tabel jobs untuk menyimpan tugas yang akan dijalankan secara asinkron</li>
          <li><strong>create_books_table</strong> - Membuat tabel books untuk menyimpan data buku di perpustakaan</li>
          <li><strong>create_loans_table</strong> - Membuat tabel loans untuk menyimpan catatan peminjaman buku</li>
          <li><strong>add_tahun_angkatan_to_users_table</strong> - Menambahkan kolom tahun_angkatan ke tabel users untuk menyimpan informasi tahun angkatan siswa</li>
        </ul>
      
        <h3>Implementasi Migration</h3>
        
        <h4>1. Create Users Table</h4>
        <div class="code-block">
          <pre><code class="language-php">use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('user');
            $table->string('user_type')->default('siswa');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};</code></pre>
        </div>
        <p>
          Pada migration ini, <code>Schema::create('users')</code> digunakan untuk membuat tabel <b>users</b>. Baris <code>$table->id()</code> membuat kolom primary key bertipe big integer auto increment. <code>$table->string('name')</code> dan <code>$table->string('email')->unique()</code> masing-masing membuat kolom nama dan email, di mana email harus unik. <code>$table->timestamp('email_verified_at')->nullable()</code> digunakan untuk menyimpan waktu verifikasi email, dan boleh kosong. <code>$table->string('password')</code> untuk password terenkripsi. <code>$table->string('role')->default('user')</code> dan <code>$table->string('user_type')->default('siswa')</code> untuk menyimpan peran dan tipe user dengan nilai default. <code>$table->rememberToken()</code> untuk fitur remember me pada login. <code>$table->timestamps()</code> otomatis menambah kolom created_at dan updated_at.
        </p>
        
        <h4>2. Create Books Table</h4>
        <div class="code-block">
          <pre><code class="language-php">use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('penulis');
            $table->string('penerbit');
            $table->year('tahun_terbit');
            $table->string('isbn')->nullable();
            $table->integer('eksemplar');
            $table->integer('ketersediaan');
            $table->string('kategori')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('cover_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};</code></pre>
        </div>
        <p>
          Migration ini membuat tabel <b>books</b> untuk menyimpan data buku. <code>$table->id()</code> sebagai primary key. <code>$table->string('judul')</code>, <code>$table->string('penulis')</code>, dan <code>$table->string('penerbit')</code> untuk judul, penulis, dan penerbit buku. <code>$table->year('tahun_terbit')</code> untuk tahun terbit. <code>$table->string('isbn')->nullable()</code> untuk ISBN yang boleh kosong. <code>$table->integer('eksemplar')</code> dan <code>$table->integer('ketersediaan')</code> untuk jumlah eksemplar dan ketersediaan buku. <code>$table->string('kategori')->nullable()</code> dan <code>$table->text('deskripsi')->nullable()</code> untuk kategori dan deskripsi buku yang boleh kosong. <code>$table->string('cover_path')->nullable()</code> untuk path file cover buku. <code>$table->timestamps()</code> menambah kolom created_at dan updated_at, sedangkan <code>$table->softDeletes()</code> menambah kolom deleted_at untuk fitur soft delete.
        </p>
        
        <h4>3. Create Loans Table</h4>
        <div class="code-block">
          <pre><code class="language-php">use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali');
            $table->date('tanggal_pengembalian')->nullable();
            $table->string('status'); // dipinjam, dikembalikan, terlambat, belum_diambil
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};</code></pre>
        </div>
        <p>
          Migration ini membuat tabel <b>loans</b> untuk mencatat peminjaman buku. <code>$table->id()</code> sebagai primary key. <code>$table->foreignId('user_id')->constrained()->onDelete('cascade')</code> dan <code>$table->foreignId('book_id')->constrained('books')->onDelete('cascade')</code> membuat foreign key ke tabel users dan books, dengan penghapusan cascade. <code>$table->date('tanggal_pinjam')</code> dan <code>$table->date('tanggal_kembali')</code> untuk tanggal peminjaman dan pengembalian. <code>$table->date('tanggal_pengembalian')->nullable()</code> untuk tanggal pengembalian aktual (boleh kosong). <code>$table->string('status')</code> untuk status peminjaman (misal: dipinjam, dikembalikan, terlambat, belum_diambil). <code>$table->text('catatan')->nullable()</code> untuk catatan tambahan. <code>$table->timestamps()</code> menambah kolom created_at dan updated_at.
        </p>
        
        <h4>4. Add Tahun Angkatan to Users Table</h4>
        <div class="code-block">
          <pre><code class="language-php">use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->year('tahun_angkatan')->nullable()->after('user_type');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('tahun_angkatan');
        });
    }
};</code></pre>
        </div>
        <p>
          Migration ini menambahkan kolom <b>tahun_angkatan</b> ke tabel users. <code>$table->year('tahun_angkatan')->nullable()->after('user_type')</code> menambah kolom bertipe year yang boleh kosong, diletakkan setelah kolom user_type. Pada fungsi down, kolom ini akan dihapus jika migrasi di-rollback.
        </p>
        
        <h3>Menjalankan Migration</h3>
        <p>Untuk menjalankan semua migrasi yang belum dijalankan, gunakan perintah:</p>
        <div class="code-block">
          <pre><code class="language-php">php artisan migrate</code></pre>
        </div>
        
        <p>Untuk membatalkan migrasi terakhir, gunakan perintah:</p>
        <div class="code-block">
          <pre><code class="language-php">php artisan migrate:rollback</code></pre>
        </div>
        
        <p>Untuk mengatur ulang seluruh database dan menjalankan semua migrasi, gunakan perintah:</p>
        <div class="code-block">
          <pre><code class="language-php">php artisan migrate:fresh</code></pre>
        </div>
        
        <p>Untuk mengatur ulang seluruh database, menjalankan semua migrasi, dan mengisi dengan data awal, gunakan perintah:</p>
        <div class="code-block">
          <pre><code class="language-php">php artisan migrate:fresh --seed</code></pre>
        </div>
      </div>
      
      <!-- Halaman Route -->
      <div v-if="currentPage === 'route'" class="docs-page">
        <h2>Routes</h2>
        <p>
          Routing merupakan salah satu komponen fundamental dalam pengembangan aplikasi web berbasis Laravel. Routing berfungsi untuk mengatur alur permintaan (request) dari pengguna ke controller yang sesuai, sehingga setiap permintaan URL dapat diproses dan diarahkan ke logika bisnis yang tepat. Dengan sistem routing yang baik, aplikasi menjadi lebih terstruktur, mudah dikembangkan, dan aman.
        </p>

        <h3>Struktur File Route pada MyPerpus</h3>
        <p>
          Pada aplikasi MyPerpus, seluruh definisi route dikelola di dalam folder <code>routes/</code>. Laravel secara default memisahkan file route berdasarkan fungsinya, sehingga memudahkan pengelolaan dan pemeliharaan kode. Berikut adalah file-file route yang digunakan:
        </p>
        <ul>
          <li><strong>web.php</strong>: Berisi seluruh route utama aplikasi, baik untuk pengguna umum, admin, maupun user terautentikasi.</li>
          <li><strong>auth.php</strong>: Berisi route yang berkaitan dengan proses autentikasi seperti login, register, dan reset password.</li>
          <li><strong>settings.php</strong>: Berisi route untuk pengaturan profil dan password pengguna.</li>
          <li><strong>console.php</strong>: Berisi route untuk kebutuhan command artisan (umumnya untuk kebutuhan internal/console command).</li>
        </ul>

        <h3>Pembagian dan Pengelompokan Route</h3>
        <p>
          Untuk menjaga keamanan dan keteraturan aplikasi, route pada MyPerpus dikelompokkan berdasarkan hak akses dan peran pengguna. Pengelompokan ini dilakukan dengan memanfaatkan fitur <i>middleware</i> pada Laravel, yang berfungsi sebagai filter sebelum request diproses oleh controller. Secara umum, terdapat tiga kelompok utama route:
        </p>
        <ol>
          <li><b>Route Publik</b>: Dapat diakses oleh semua pengguna tanpa perlu login. Contohnya adalah halaman utama dan detail buku.</li>
          <li><b>Route Admin</b>: Hanya dapat diakses oleh pengguna dengan peran admin. Route ini dilindungi oleh middleware <code>auth</code>, <code>verified</code>, dan <code>CheckUserType:admin</code> untuk memastikan hanya admin yang dapat mengakses fitur administrasi.</li>
          <li><b>Route User</b>: Dapat diakses oleh pengguna yang sudah login dengan tipe user biasa (misal: siswa atau guru). Route ini menggunakan middleware <code>auth</code>, <code>verified</code>, dan <code>CheckUserType:user</code>.</li>
        </ol>

        <h3>Potongan Kode Route Publik</h3>
        <div class="code-block">
          <pre><code class="language-php">// routes/web.php
Route::get('/', [BookController::class, 'home'])->name('home');
Route::get('/book/{book}', [BookController::class, 'detail'])->name('book.detail');
Route::get('/documentation', function () {
    return Inertia::render('Documentation', [
        'useCustomLayout' => true
    ]);
})->name('documentation');</code></pre>
        </div>
        <p>
          Pada contoh di atas, route <code>/</code> akan mengarahkan permintaan ke method <code>home</code> pada <code>BookController</code> untuk menampilkan halaman utama. Route <code>/book/{book}</code> digunakan untuk menampilkan detail buku berdasarkan parameter <code>book</code> yang diterima. Route <code>/documentation</code> menggunakan closure untuk langsung me-render halaman dokumentasi menggunakan Inertia. Seluruh route ini dapat diakses tanpa autentikasi, sehingga cocok untuk halaman yang bersifat publik.
        </p>

        <h3>Potongan Kode Route Admin</h3>
        <div class="code-block">
          <pre><code class="language-php">// routes/web.php
Route::prefix('admin')->middleware(['auth', 'verified', CheckUserType::class.':admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/export', [DashboardController::class, 'export'])->name('dashboard.export');
    Route::resource('user-management', UserManagementController::class)->parameters([
        'user-management' => 'user'
    ]);
    Route::resource('books', BookController::class);
    Route::get('export-books', [BookController::class, 'exportBooks'])->name('export.books');
    Route::post('books/import', [BookController::class, 'import'])->name('books.import');
    Route::delete('books/bulk-delete', [BookController::class, 'bulkDelete'])->name('books.bulk-delete');
    Route::post('books/bulk-update-jumlah', [BookController::class, 'bulkUpdateJumlah'])->name('books.bulk-update-jumlah');
    Route::get('books/all-ids', [BookController::class, 'getAllBookIds'])->name('books.all-ids');
    Route::get('/loans', [LoanController::class, 'index'])->name('loans.index');
    Route::get('/loans/pending', [LoanController::class, 'pendingApproval'])->name('loans.pending');
    Route::get('/loans/active', [LoanController::class, 'activeLoans'])->name('loans.active');
    Route::get('/loans/create', [LoanController::class, 'create'])->name('loans.create');
    Route::post('/loans/store', [LoanController::class, 'storeLoan'])->name('loans.store');
    Route::post('/loans/{loan}/approve', [LoanController::class, 'approveLoan'])->name('loans.approve');
    Route::post('/loans/{loan}/return', [LoanController::class, 'returnBook'])->name('loans.return');
    Route::delete('/loans/{loan}/reject', [LoanController::class, 'rejectRequest'])->name('loans.reject');
});</code></pre>
        </div>
        <p>
          Pada blok kode di atas, seluruh route dikelompokkan di bawah prefix <code>/admin</code> dan hanya dapat diakses oleh admin. Middleware <code>auth</code> memastikan user sudah login, <code>verified</code> memastikan email sudah diverifikasi, dan <code>CheckUserType:admin</code> memastikan hanya admin yang dapat mengakses. Route <code>Route::resource</code> digunakan untuk membuat route CRUD secara otomatis untuk manajemen user dan buku. Route tambahan seperti <code>exportBooks</code>, <code>import</code>, dan <code>bulkDelete</code> digunakan untuk fitur administrasi khusus. Route peminjaman buku juga dikelola di sini agar hanya admin yang dapat mengelola seluruh data peminjaman.
        </p>

        <h3>Potongan Kode Route User</h3>
        <div class="code-block">
          <pre><code class="language-php">// routes/web.php
Route::middleware(['auth', 'verified', CheckUserType::class.':user'])->group(function () {
    Route::get('/bookshelves', [BookshelvesController::class, 'index'])->name('bookshelves');
    Route::post('/bookshelves/loans/request', [BookshelvesController::class, 'requestLoan'])->name('loans.request');
    Route::delete('/bookshelves/loans/{loan}/cancel', [BookshelvesController::class, 'cancelRequest'])->name('loans.cancel');
    Route::get('/loans', function() {
        return redirect()->route('bookshelves');
    });
});</code></pre>
        </div>
        <p>
          Route di atas hanya dapat diakses oleh user biasa (misal: siswa atau guru) yang sudah login dan terverifikasi. Middleware <code>CheckUserType:user</code> memastikan hanya user dengan tipe user yang dapat mengakses. Route <code>/bookshelves</code> menampilkan rak buku milik user, sedangkan <code>/bookshelves/loans/request</code> dan <code>/bookshelves/loans/{loan}/cancel</code> digunakan untuk request dan pembatalan peminjaman buku. Route <code>/loans</code> akan mengarahkan user ke halaman rak buku jika mencoba mengakses langsung.
        </p>
        
        <h3>Potongan Kode Route Autentikasi</h3>
        <div class="code-block">
          <pre><code class="language-php">// routes/auth.php
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    // ... route autentikasi lainnya
});</code></pre>
        </div>
        <p>
          File <code>auth.php</code> berisi route untuk proses autentikasi seperti login, register, dan reset password. Middleware <code>guest</code> memastikan hanya user yang belum login yang dapat mengakses route ini, sehingga user yang sudah login tidak dapat mengakses halaman login atau register lagi.
        </p>
        
        <h3>Potongan Kode Route Settings</h3>
        <div class="code-block">
          <pre><code class="language-php">// routes/settings.php
Route::middleware(['auth'])->group(function () {
    Route::get('/settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/settings/password', [PasswordController::class, 'edit'])->name('password.edit');
    Route::put('/settings/password', [PasswordController::class, 'update'])->name('password.update');
    // ... route pengaturan lainnya
});</code></pre>
        </div>
        <p>
          File <code>settings.php</code> digunakan untuk mengelola pengaturan profil dan password pengguna. Middleware <code>auth</code> memastikan hanya user yang sudah login yang dapat mengakses halaman pengaturan ini. Dengan demikian, keamanan data pengguna tetap terjaga.
        </p>

        <h3>Kesimpulan</h3>
        <p>
          Dengan pengelolaan route yang terstruktur dan penggunaan middleware yang tepat, aplikasi MyPerpus dapat memastikan setiap permintaan pengguna diarahkan ke logika bisnis yang sesuai dan hanya dapat diakses oleh pihak yang berwenang. Hal ini sangat penting untuk menjaga keamanan, keteraturan, dan skalabilitas aplikasi, terutama dalam konteks pengembangan perangkat lunak untuk institusi pendidikan.
        </p>
      </div>
      
      <!-- Halaman Model -->
      <div v-if="currentPage === 'model'" class="docs-page">
        <h2>Model</h2>
        <p>
          Model merupakan representasi data dan logika bisnis pada aplikasi MyPerpus. Setiap model berhubungan langsung dengan tabel pada database dan bertanggung jawab untuk mengelola data serta relasi antar entitas. Pada aplikasi ini, model utama yang digunakan adalah <b>Buku</b>, <b>User</b>, dan <b>Loan</b>.
        </p>
        
        <h3>Model Buku</h3>
        <div class="code-block">
          <pre><code class="language-php">namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'kategori',
        'deskripsi',
        'cover_path',
    ];
    
    // Relasi: satu buku bisa memiliki banyak peminjaman
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class, 'book_id');
    }
    
    // Method untuk mengurangi ketersediaan buku
    public function decreaseAvailability(int $amount = 1): bool
    {
        if ($this->ketersediaan < $amount) {
            return false;
        }
        $this->ketersediaan -= $amount;
        return $this->save();
    }
    
    // Method untuk menambah ketersediaan buku
    public function increaseAvailability(int $amount = 1): bool
    {
        if ($this->ketersediaan + $amount > $this->eksemplar) {
            return false;
        }
        $this->ketersediaan += $amount;
        return $this->save();
    }
}
</code></pre>
        </div>
        <p>
          Model <b>Buku</b> merepresentasikan tabel <code>books</code> pada database. Model ini menggunakan fitur <code>SoftDeletes</code> untuk mendukung penghapusan data secara sementara. Atribut <code>$fillable</code> mendefinisikan kolom yang dapat diisi secara massal. Relasi <code>loans()</code> menunjukkan bahwa satu buku dapat memiliki banyak peminjaman. Terdapat juga method untuk mengurangi dan menambah ketersediaan buku secara otomatis.
        </p>
        
        <h3>Model User</h3>
        <div class="code-block">
          <pre><code class="language-php">namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'user_type',
        'tahun_angkatan',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relasi: satu user bisa memiliki banyak peminjaman
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class, 'user_id');
    }
}
</code></pre>
        </div>
        <p>
          Model <b>User</b> merepresentasikan tabel <code>users</code> dan menggunakan fitur autentikasi Laravel. Model ini juga menggunakan <code>SoftDeletes</code> untuk mendukung penghapusan data secara sementara. Relasi <code>loans()</code> menunjukkan bahwa satu user dapat memiliki banyak peminjaman buku. Atribut <code>$hidden</code> digunakan untuk menyembunyikan data sensitif seperti password saat model diubah menjadi array atau JSON.
        </p>
        
        <h3>Model Loan</h3>
        <div class="code-block">
          <pre><code class="language-php">namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    use HasFactory;

    protected $table = 'loans';

    protected $fillable = [
        'user_id',
        'book_id',
        'tanggal_pinjam',
        'tanggal_kembali',
        'tanggal_pengembalian',
        'status',
        'catatan',
    ];

    // Relasi: satu peminjaman milik satu user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi: satu peminjaman milik satu buku
    public function book(): BelongsTo
    {
        return $this->belongsTo(Buku::class, 'book_id');
    }
}
</code></pre>
        </div>
        <p>
          Model <b>Loan</b> merepresentasikan tabel <code>loans</code> pada database. Model ini memiliki relasi <code>user()</code> dan <code>book()</code> yang menunjukkan bahwa setiap peminjaman terhubung ke satu user dan satu buku. Atribut <code>$fillable</code> mendefinisikan kolom yang dapat diisi secara massal.
        </p>

        <h3>Relasi Antar Model</h3>
        <p>
          Relasi antar model pada aplikasi MyPerpus sangat penting untuk menjaga integritas data dan memudahkan pengambilan data terkait. Berikut adalah ringkasan relasi utama:
        </p>
        <ul>
          <li><b>Buku</b> <span style="font-weight:normal">memiliki banyak</span> <b>Loan</b> (One-to-Many)</li>
          <li><b>User</b> <span style="font-weight:normal">memiliki banyak</span> <b>Loan</b> (One-to-Many)</li>
          <li><b>Loan</b> <span style="font-weight:normal">milik satu</span> <b>Buku</b> dan <b>User</b> (Many-to-One)</li>
        </ul>
        <p>
          Dengan pemodelan relasi yang baik, proses query data menjadi lebih efisien dan kode aplikasi lebih mudah dipelihara.
        </p>
      </div>
      
      <!-- Halaman Controller -->
      <div v-if="currentPage === 'controller'" class="docs-page">
        <h2>Controller</h2>
        <p>
          Controller merupakan komponen inti dalam arsitektur MVC (Model-View-Controller) pada Laravel. Controller bertugas menerima permintaan (request) dari pengguna, memproses logika bisnis, berinteraksi dengan model, serta mengembalikan response ke view. Dengan controller, kode aplikasi menjadi lebih terstruktur, terpisah antara logika bisnis dan tampilan, serta mudah dikembangkan dan diuji.
        </p>
        <p>
          Berikut adalah penjelasan BookController beserta seluruh function di dalamnya:
        </p>

        <!-- BookController -->
        <h3>BookController</h3>
        <p>
          <b>BookController</b> bertanggung jawab untuk seluruh proses manajemen data buku, mulai dari menampilkan daftar buku, pencarian, penambahan, pengeditan, penghapusan, ekspor, hingga impor data buku. Controller ini juga mengelola validasi data dan relasi antara buku dan peminjaman.
        </p>
        <ul>
          <li><b>index(Request $request)</b><br/>
            <div class="code-block"><pre><code class="language-php">public function index(Request $request)
{
    $perPage = 10; // Jumlah item per halaman
    $search = $request->input('search', '');
    $query = Buku::query();
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
        'books' => $books,
        'filters' => [
            'search' => $search
        ]
    ]);
}</code></pre></div>
            <p><b>Penjelasan:</b> <br>
            Method <b>index</b> berfungsi sebagai pintu utama untuk menampilkan daftar seluruh buku yang tersedia di aplikasi. Pada awalnya, method ini menentukan jumlah item per halaman (<code>$perPage</code>) untuk keperluan paginasi, sehingga tampilan daftar buku tetap terstruktur dan tidak membebani antarmuka pengguna. Selanjutnya, method ini mengambil parameter pencarian (<code>$search</code>) dari request, yang memungkinkan pengguna melakukan pencarian berdasarkan judul, penulis, penerbit, tahun terbit, ISBN, nomor panggil, maupun kategori buku. <br><br>
            Proses pencarian dilakukan dengan membangun query dinamis menggunakan Eloquent, di mana setiap kolom yang relevan akan dicocokkan dengan kata kunci pencarian secara <i>case-insensitive</i>. Setelah query selesai, hasilnya dipaginasi sesuai jumlah per halaman yang telah ditentukan. <br><br>
            Sebelum data dikirim ke view, setiap objek buku diproses untuk menentukan URL cover yang tepat, baik dari file upload maupun URL eksternal. Hal ini penting agar tampilan daftar buku selalu konsisten dan gambar cover dapat diakses dengan benar. <br><br>
            Akhirnya, data buku beserta filter pencarian dikirim ke view <code>BookManagement/Index</code> menggunakan Inertia.js, sehingga frontend dapat menampilkan data secara reaktif dan interaktif. Dengan pendekatan ini, aplikasi mampu menangani dataset besar secara efisien dan memberikan pengalaman pengguna yang optimal.</p>
          </li>
          <li><b>create()</b><br/>
            <div class="code-block"><pre><code class="language-php">public function create()
{
    return Inertia::render('BookManagement/Create');
}</code></pre></div>
            <p><b>Penjelasan:</b> <br>
            Method <b>create</b> pada <b>BookController</b> berfungsi secara spesifik untuk menampilkan halaman form pembuatan buku baru. Pada implementasi aplikasi MyPerpus, method ini tidak melakukan proses logika bisnis apapun, tidak melakukan query ke database, maupun validasi data. Method ini hanya mengembalikan response berupa render halaman <code>BookManagement/Create</code> menggunakan Inertia.js. Dengan demikian, seluruh proses input data buku baru dilakukan sepenuhnya di sisi frontend, dan validasi serta penyimpanan data akan diproses pada method <code>store</code>. Pendekatan ini membuat pemisahan tanggung jawab menjadi jelas antara tampilan form dan proses penyimpanan data, serta memudahkan pemeliharaan kode dan pengujian aplikasi.</p>
          </li>
          <li><b>store(Request $request)</b><br/>
            <div class="code-block"><pre><code class="language-php">public function store(Request $request)
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
    $validated['ketersediaan'] = $validated['eksemplar'];
    \App\Models\Buku::create($validated);
    return redirect()->route('books.index')->with('success', 'Buku berhasil ditambahkan.');
}</code></pre></div>
            <p><b>Penjelasan:</b> <br>
            Method <b>store</b> merupakan inti dari proses penambahan data buku ke dalam sistem. Pada tahap awal, seluruh data yang dikirimkan dari form akan divalidasi secara ketat untuk memastikan integritas dan keamanan data. Validasi meliputi pengecekan kolom wajib seperti <code>judul</code> serta tipe data pada kolom lain seperti <code>eksemplar</code> dan <code>ketersediaan</code>. <br><br>
            Untuk pengelolaan cover buku, method ini mendukung dua tipe input: upload file gambar atau URL eksternal. Jika pengguna memilih upload, file gambar akan divalidasi ulang untuk memastikan format dan ukuran file sesuai standar keamanan aplikasi. File yang valid akan disimpan di direktori <code>public/covers</code> dengan nama unik berbasis slug judul dan timestamp, sehingga menghindari konflik nama file. Jika menggunakan URL, validasi memastikan URL yang diberikan valid dan dapat diakses. <br><br>
            Setelah seluruh data valid, method ini akan menyimpan data ke database melalui model <code>Buku</code>. Nilai <code>ketersediaan</code> akan disamakan dengan jumlah eksemplar sebagai default. Setelah proses penyimpanan berhasil, pengguna akan diarahkan kembali ke halaman daftar buku dengan notifikasi sukses. Proses ini memastikan hanya data yang valid dan aman yang masuk ke sistem, serta mendukung audit trail melalui notifikasi dan redirect.</p>
          </li>
          <li><b>show(Buku $book)</b><br/>
            <div class="code-block"><pre><code class="language-php">public function show(Buku $book)
{
    $book->cover_url = $book->cover ? '/storage/' . $book->cover : null;
    return Inertia::render('BookManagement/Show', [
        'book' => $book
    ]);
}</code></pre></div>
            <p><b>Penjelasan:</b> <br>
            Method <b>show</b> digunakan untuk menampilkan detail lengkap dari sebuah buku berdasarkan ID yang dipilih. Pada method ini, data buku diproses untuk menentukan URL cover yang benar, sehingga gambar cover dapat ditampilkan dengan baik pada halaman detail. Seluruh data buku kemudian dikirim ke view <code>BookManagement/Show</code> menggunakan Inertia.js. Dengan demikian, pengguna dapat melihat informasi lengkap buku secara terstruktur dan informatif.</p>
          </li>
          <li><b>edit(Buku $book)</b><br/>
            <div class="code-block"><pre><code class="language-php">public function edit(Buku $book)
{
    $book->cover_url = $book->cover ? '/storage/' . $book->cover : null;
    return Inertia::render('BookManagement/Edit', [
        'book' => $book
    ]);
}</code></pre></div>
            <p><b>Penjelasan:</b> <br>
            Method <b>edit</b> menampilkan form edit untuk buku yang dipilih. Data buku yang akan diedit diproses untuk memastikan URL cover sudah benar, lalu dikirim ke view <code>BookManagement/Edit</code>. Dengan demikian, pengguna dapat melakukan perubahan data buku dengan mudah dan data yang ditampilkan pada form selalu akurat.</p>
          </li>
          <li><b>update(Request $request, Buku $book)</b><br/>
            <div class="code-block"><pre><code class="language-php">public function update(Request $request, Buku $book)
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
    if (!isset($validated['ketersediaan'])) {
        $validated['ketersediaan'] = $validated['eksemplar'];
    } else if ($validated['ketersediaan'] > $validated['eksemplar']) {
        $validated['ketersediaan'] = $validated['eksemplar'];
    }
    $book->update($validated);
    return redirect()->route('books.index')->with('success', 'Buku berhasil diupdate.');
}</code></pre></div>
            <p><b>Penjelasan:</b> <br>
            Method <b>update</b> memvalidasi dan memperbarui data buku yang sudah ada, termasuk mengelola perubahan cover buku. Jika cover diubah, file lama akan dihapus dari storage dan diganti dengan file baru.</p>
          </li>
          <li><b>destroy(Buku $book)</b><br/>
            <div class="code-block"><pre><code class="language-php">public function destroy(Buku $book)
{
    $book->delete();
    return redirect()->route('books.index')->with('success', 'Buku berhasil dihapus.');
}</code></pre></div>
            <p><b>Penjelasan:</b> <br>
            Method <b>destroy</b> menghapus data buku dari database. Setelah dihapus, pengguna akan diarahkan kembali ke halaman daftar buku dengan notifikasi sukses. Proses ini penting untuk menjaga integritas data dan mendukung audit trail pada aplikasi.</p>
          </li>
          <li><b>exportBooks()</b><br/>
            <div class="code-block"><pre><code class="language-php">public function exportBooks()
{
    try {
        $fileName = 'buku_export_' . date('Ymd_His') . '.xlsx';
        return Excel::download(new \App\Exports\BooksExport, $fileName);
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Error exporting books: ' . $e->getMessage());
        return back()->with('error', 'Gagal mengekspor buku: ' . $e->getMessage());
    }
}</code></pre></div>
            <p><b>Penjelasan:</b> <br>
            Method <b>exportBooks</b> mengekspor seluruh data buku ke dalam file Excel (.xlsx) menggunakan package <code>Maatwebsite Excel</code>. Proses ekspor ini sangat bermanfaat untuk kebutuhan pelaporan, backup, atau migrasi data. Jika terjadi error selama proses ekspor, pesan error akan dicatat di log aplikasi untuk keperluan debugging, dan pengguna akan mendapatkan notifikasi kegagalan ekspor. Dengan demikian, keamanan dan keandalan proses ekspor tetap terjaga.</p>
          </li>
          <li><b>import(Request $request)</b><br/>
            <div class="code-block"><pre><code class="language-php">public function import(Request $request)
{
    $request->validate([
        'file' => 'required|file|mimes:xlsx,xls',
    ]);
    Excel::import(new \App\Imports\BooksImport, $request->file('file'));
    return Redirect::route('books.index')->with('success', 'Import data buku berhasil!');
}</code></pre></div>
            <p><b>Penjelasan:</b> <br>
            Method <b>import</b> bertugas mengimpor data buku dari file Excel ke database. File yang diupload akan divalidasi untuk memastikan format dan ekstensi file sesuai standar keamanan aplikasi. Proses impor menggunakan package <code>Maatwebsite Excel</code> yang akan membaca dan memproses setiap baris data pada file. Setelah proses impor selesai, pengguna akan diarahkan ke halaman daftar buku dengan notifikasi sukses. Proses ini sangat membantu dalam mempercepat input data massal dan migrasi data dari sistem lain.</p>
          </li>
          <li><b>home(Request $request)</b><br/>
            <div class="code-block"><pre><code class="language-php">public function home(Request $request)
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
        $book->kategori_list = $book->kategori ? array_map('trim', explode(',', $book->kategori)) : [];
        return $book;
    });
    return Inertia::render('Home', [
        'books' => $books,
        'searchQuery' => $query,
    ]);
}</code></pre></div>
            <p><b>Penjelasan:</b> <br>
            Method <b>home</b> menampilkan halaman utama aplikasi beserta daftar seluruh buku yang tersedia. Setiap buku diproses untuk menentukan URL cover dan daftar kategori, sehingga tampilan halaman utama menjadi lebih informatif dan menarik. Data yang sudah diproses kemudian dikirim ke view <code>Home</code> menggunakan Inertia.js. Dengan demikian, pengguna dapat langsung melihat koleksi buku yang tersedia secara interaktif.</p>
          </li>
          <li><b>detail(Buku $book)</b><br/>
            <div class="code-block"><pre><code class="language-php">public function detail(Buku $book)
{
    if ($book->cover_type === 'url') {
        $book->cover_url = $book->cover;
    } else if ($book->cover) {
        $book->cover_url = '/storage/' . $book->cover;
    } else {
        $book->cover_url = null;
    }
    $book->kategori_list = $book->kategori ? array_map('trim', explode(',', $book->kategori)) : [];
    $book->pending_loans_count = $book->loans()->where('status', 'belum_diambil')->count();
    return Inertia::render('BookManagement/Show', [
        'book' => $book
    ]);
}</code></pre></div>
            <p><b>Penjelasan:</b> <br>
            Method <b>detail</b> menampilkan detail buku untuk halaman publik. Selain menampilkan informasi dasar buku, method ini juga memproses data cover dan kategori, serta menghitung jumlah peminjaman yang masih berstatus "belum diambil". Informasi ini sangat penting untuk memberikan gambaran ketersediaan buku kepada pengguna. Data yang sudah diproses dikirim ke view <code>BookManagement/Show</code> untuk ditampilkan secara lengkap.</p>
          </li>
          <li><b>bulkDelete(Request $request)</b><br/>
            <div class="code-block"><pre><code class="language-php">public function bulkDelete(Request $request)
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
}</code></pre></div>
            <p><b>Penjelasan:</b> <br>
            Method <b>bulkDelete</b> pada <b>BookController</b> digunakan untuk melakukan penghapusan data buku secara massal berdasarkan daftar ID yang dikirimkan melalui request. Pada tahap awal, dilakukan validasi untuk memastikan bahwa parameter <code>ids</code> wajib ada, bertipe array, dan setiap elemen array tersebut benar-benar merupakan ID buku yang valid di database. Validasi ini sangat penting untuk mencegah terjadinya penghapusan data yang tidak diinginkan atau manipulasi data oleh pihak yang tidak berwenang.<br><br>
            Setelah validasi berhasil, method ini menghitung jumlah buku yang akan dihapus untuk keperluan notifikasi. Proses penghapusan dilakukan secara efisien menggunakan query <code>whereIn</code> pada model <code>Buku</code>, sehingga seluruh data yang sesuai dapat dihapus dalam satu operasi database. Setelah proses penghapusan selesai, method ini mengembalikan response dalam format JSON yang berisi status sukses dan pesan jumlah buku yang berhasil dihapus. <br><br>
            Dengan pendekatan ini, proses administrasi data buku menjadi lebih efisien, aman, dan mudah dipantau. Penggunaan response JSON juga memudahkan integrasi dengan frontend berbasis SPA seperti Vue.js dan Inertia.js.</p>
          </li>
          <li><b>bulkUpdateJumlah(Request $request)</b><br/>
            <div class="code-block"><pre><code class="language-php">public function bulkUpdateJumlah(Request $request)
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
}</code></pre></div>
            <p><b>Penjelasan:</b> <br>
            Method <b>bulkUpdateJumlah</b> pada <b>BookController</b> digunakan untuk memperbarui jumlah eksemplar dan ketersediaan beberapa buku sekaligus berdasarkan daftar ID yang dikirimkan melalui request. Validasi dilakukan untuk memastikan parameter <code>ids</code> wajib ada, bertipe array, setiap ID valid di database, dan nilai <code>eksemplar</code> merupakan bilangan bulat minimal 0. <br><br>
            Setelah validasi, method ini menghitung jumlah buku yang akan diperbarui untuk keperluan notifikasi. Proses update dilakukan secara efisien menggunakan query <code>whereIn</code> dan <code>update</code> massal pada model <code>Buku</code>, sehingga seluruh data yang sesuai dapat diperbarui dalam satu operasi database. Nilai <code>eksemplar</code> dan <code>ketersediaan</code> akan disamakan agar data konsisten. Setelah proses update selesai, method ini mengembalikan response JSON berisi status sukses dan pesan jumlah buku yang berhasil diperbarui. <br><br>
            Dengan pendekatan ini, pengelolaan stok buku secara massal menjadi lebih efisien, aman, dan mudah dipantau oleh admin.</p>
          </li>
          <li><b>getAllBookIds()</b><br/>
            <div class="code-block"><pre><code class="language-php">public function getAllBookIds()
{
    try {
        $ids = Buku::pluck('id')->values()->toArray();
        return response()->json(['ids' => $ids]);
    } catch (\Exception $e) {
        \Illuminate\Support\Facades\Log::error('Error in getAllBookIds: ' . $e->getMessage());
        return response()->json(['ids' => [], 'error' => 'Failed to retrieve book IDs'], 500);
    }
}</code></pre></div>
            <p><b>Penjelasan:</b> <br>
            Method <b>getAllBookIds</b> digunakan untuk mengambil seluruh ID buku yang ada di database dan mengembalikannya dalam format array melalui response JSON. Proses pengambilan data dilakukan menggunakan <code>pluck</code> pada model <code>Buku</code> untuk efisiensi dan performa. Method ini juga dilengkapi dengan penanganan error menggunakan blok <code>try-catch</code>. Jika terjadi error saat pengambilan data, pesan error akan dicatat di log aplikasi dan response akan mengembalikan array kosong beserta pesan error dengan status HTTP 500. <br><br>
            Fungsi ini sangat berguna untuk kebutuhan administrasi, proses batch, atau integrasi dengan modul lain yang memerlukan daftar ID buku secara cepat dan efisien.</p>
          </li>
        </ul>
        </div>
      
      <!-- Halaman View -->
      <div v-if="currentPage === 'view'" class="docs-page">
        <h2>Views</h2>
        <p>Halaman ini menjelaskan implementasi tampilan (view) pada aplikasi MyPerpus yang menggunakan Vue.js dengan Inertia.js.</p>
        
        <h3>Struktur View</h3>
        <p>Aplikasi MyPerpus menggunakan struktur view sebagai berikut:</p>
        <ul>
          <li><strong>resources/views/app.blade.php</strong> - Template utama blade</li>
          <li><strong>resources/js/pages/</strong> - Halaman-halaman Vue.js</li>
          <li><strong>resources/js/components/</strong> - Komponen Vue.js yang dapat digunakan kembali</li>
          <li><strong>resources/js/layouts/</strong> - Layout untuk halaman-halaman Vue.js</li>
        </ul>
        
        <h3>Contoh Implementasi View</h3>
        <h4>app.blade.php</h4>
        <div class="code-block">
          <pre><code>&lt;!DOCTYPE html&gt;
&lt;html&gt;
  &lt;head&gt;
    &lt;meta charset="utf-8" /&gt;
    &lt;meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0" /&gt;
    @vite(['resources/js/app.ts'])
    @inertiaHead
  &lt;/head&gt;
  &lt;body&gt;
    @inertia
  &lt;/body&gt;
&lt;/html&gt;</code></pre>
        </div>
        
        <h4>Vue Component</h4>
        <div class="code-block">
          <pre><code>// resources/js/pages/BookManagement/Index.vue
&lt;template&gt;
  &lt;AppLayout title="Manajemen Buku"&gt;
    &lt;div class="container mx-auto py-6"&gt;
      &lt;div class="flex justify-between items-center mb-6"&gt;
        &lt;h1 class="text-2xl font-bold"&gt;Manajemen Buku&lt;/h1&gt;
        &lt;Link
          :href="route('books.create')"
          class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        &gt;
          Tambah Buku
        &lt;/Link&gt;
      &lt;/div&gt;
      
      &lt;!-- Filter dan pencarian --&gt;
      &lt;div class="mb-6"&gt;
        &lt;BookFilters :filters="filters" /&gt;
      &lt;/div&gt;
      
      &lt;!-- Tabel buku --&gt;
      &lt;div class="bg-white rounded-lg shadow overflow-hidden"&gt;
        &lt;!-- Implementasi tabel --&gt;
      &lt;/div&gt;
      
      &lt;!-- Pagination --&gt;
      &lt;div class="mt-4"&gt;
        &lt;Pagination :links="books.links" /&gt;
      &lt;/div&gt;
    &lt;/div&gt;
  &lt;/AppLayout&gt;
&lt;/template&gt;

&lt;script setup&gt;
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import BookFilters from '@/components/BookFilters.vue';
import Pagination from '@/components/Pagination.vue';

// Props yang diterima dari controller
const props = defineProps({
  books: Object,
  categories: Array,
  filters: Object,
});
&lt;/script&gt;</code></pre>
        </div>
        
        <h3>Inertia.js dan Laravel</h3>
        <p>Inertia.js menjembatani Laravel dengan Vue.js tanpa memerlukan API terpisah. Setiap request controller akan mengembalikan respon Inertia yang merender komponen Vue.js dengan data yang diperlukan.</p>
      </div>
      
      <!-- Halaman Fitur -->
      <div v-if="currentPage === 'features'" class="docs-page">
        <h2>Fitur-fitur Aplikasi</h2>
        <p>Halaman ini menjelaskan implementasi fitur-fitur utama MyPerpus.</p>
        <!-- Konten fitur akan ditambahkan di sini -->
      </div>
      
      <!-- Halaman Deployment -->
      <div v-if="currentPage === 'deployment'" class="docs-page">
        <h2>Deployment Aplikasi</h2>
        <p>Halaman ini menjelaskan cara melakukan deployment MyPerpus.</p>
        <!-- Konten deployment akan ditambahkan di sini -->
      </div>
      
      <!-- Footer -->
      <footer class="docs-footer">
        <p>&copy; {{ currentYear }} MyPerpus - Dokumentasi Implementasi</p>
      </footer>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted, ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

// Tahun saat ini untuk footer
const currentYear = ref(new Date().getFullYear());
// Halaman aktif
const currentPage = computed(() => {
  // Default page adalah overview jika tidak ada parameter
  const params = usePage().props.params as { page?: string } || {};
  return params.page || 'overview';
});

onMounted(() => {
  // Pastikan body memiliki kelas docs-body
  document.body.classList.add('docs-body');
});

onUnmounted(() => {
  // Hapus kelas docs-body saat meninggalkan halaman
  document.body.classList.remove('docs-body');
});
</script>

<style>
/* CSS untuk dokumentasi */
:root {
  --primary-color: #3b82f6;
  --secondary-color: #1e40af;
  --text-color: #1f2937;
  --text-light: #6b7280;
  --bg-color: #f9fafb;
  --sidebar-bg: #ffffff;
  --sidebar-width: 280px;
  --border-color: #e5e7eb;
  --code-bg: #f3f4f6;
}

body.docs-body {
  margin: 0;
  padding: 0;
  font-family: 'Inter', system-ui, -apple-system, sans-serif;
  background-color: var(--bg-color);
  color: var(--text-color);
}

.docs-container {
  display: flex;
  min-height: 100vh;
}

/* Sidebar Styles */
.docs-sidebar {
  width: var(--sidebar-width);
  background-color: var(--sidebar-bg);
  border-right: 1px solid var(--border-color);
  position: fixed;
  height: 100vh;
  overflow-y: auto;
  padding: 1.5rem 1rem;
  z-index: 10;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
}

.docs-logo {
  margin-bottom: 2rem;
  padding: 0 0.5rem;
  text-align: center;
}

.docs-logo a {
  font-size: 1.75rem;
  font-weight: 700;
  color: var(--primary-color);
  text-decoration: none;
}

.docs-menu {
  list-style: none;
  padding: 0;
  margin: 0;
}

.docs-menu li {
  margin-bottom: 0.25rem;
}

.docs-menu-item {
  display: block;
  padding: 0.75rem 1rem;
  color: var(--text-light);
  text-decoration: none;
  border-radius: 0.375rem;
  transition: all 0.2s ease;
  font-size: 0.95rem;
}

.docs-menu-item:hover {
  background-color: rgba(59, 130, 246, 0.1);
  color: var(--primary-color);
}

.docs-menu-item.active {
  background-color: rgba(59, 130, 246, 0.15);
  color: var(--primary-color);
  font-weight: 600;
  border-left: 3px solid var(--primary-color);
}

/* Content Styles */
.docs-content {
  flex: 1;
  margin-left: var(--sidebar-width);
  padding: 2.5rem 3rem 2rem;
  max-width: 900px;
  line-height: 1.6;
}

.docs-content h1 {
  font-size: 2.25rem;
  font-weight: 800;
  margin-bottom: 2rem;
  color: var(--secondary-color);
  border-bottom: 2px solid var(--border-color);
  padding-bottom: 1rem;
}

.docs-content h2 {
  font-size: 1.75rem;
  margin-top: 2rem;
  margin-bottom: 1rem;
  color: var(--secondary-color);
}

.docs-content h3 {
  font-size: 1.5rem;
  margin-top: 1.5rem;
  margin-bottom: 0.75rem;
  color: var(--primary-color);
}

.docs-content p,
.docs-content ul,
.docs-content ol {
  margin-bottom: 1rem;
}

.docs-content ul,
.docs-content ol {
  padding-left: 1.5rem;
}

.docs-content li {
  margin-bottom: 0.5rem;
}

/* Code Block Styles */
.code-block {
  background-color: var(--code-bg);
  border-radius: 0.5rem;
  overflow: auto;
  margin: 1.5rem 0;
  border: 1px solid var(--border-color);
}

.code-block pre {
  margin: 0;
  padding: 1rem;
}

.code-block code {
  font-family: 'Fira Code', 'Consolas', monospace;
  font-size: 0.9rem;
  display: block;
  line-height: 1.5;
}

/* Footer Styles */
.docs-footer {
  margin-top: 5rem;
  padding-top: 2rem;
  border-top: 1px solid var(--border-color);
  color: var(--text-light);
  font-size: 0.875rem;
  text-align: center;
}

.docs-footer p {
  margin: 0.5rem 0;
}

/* Page Styles */
.docs-page {
  margin-bottom: 2rem;
}

/* Responsive Design */
@media (max-width: 768px) {
  .docs-sidebar {
    transform: translateX(-100%);
    transition: transform 0.3s ease;
  }
  
  .docs-sidebar.show {
    transform: translateX(0);
  }
  
  .docs-content {
    margin-left: 0;
    padding: 1.5rem;
  }
  
  .sidebar-toggle {
    display: block;
    position: fixed;
    top: 1rem;
    left: 1rem;
    z-index: 20;
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: 0.25rem;
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
  }
}
</style> 