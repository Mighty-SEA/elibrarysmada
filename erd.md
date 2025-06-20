```mermaid
erDiagram
  USERS {
    string id PK
    string name
    string username
    enum role
    year tahun_angkatan
    string jenis_kelamin
    string jurusan
    string nomor_telepon
    string foto_profil
    timestamp email_verified_at
    string password
    rememberToken remember_token
    timestamps timestamps
  }
  BOOKS {
    int id PK
    string judul
    string penulis
    string penerbit
    year tahun_terbit
    string isbn
    int eksemplar
    int ketersediaan
    string no_panggil
    string asal_koleksi
    string kota_terbit
    text deskripsi
    string kategori
    string cover
    enum cover_type
    timestamps timestamps
    softDeletes soft_deletes
  }
  LOANS {
    int id PK
    string user_id FK
    int book_id FK
    enum status
    datetime request_date
    datetime approval_date
    string approval_by FK
    datetime due_date
    datetime return_date
    decimal fine_amount
    text notes
    timestamps timestamps
  }
  USERS ||--o{ LOANS : meminjam
  BOOKS ||--o{ LOANS : dipinjam
  USERS ||--o{ LOANS : menyetujui
