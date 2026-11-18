# KabarKita — Portal Berita PHP & MySQL

Project portal berita sederhana untuk tugas PKL. Dibangun dengan PHP native, MySQL, PDO, HTML, CSS, dan JavaScript.

## Fitur
- Halaman publik: berita utama, daftar berita, kategori, pencarian, pagination, detail artikel, dan berita terkait.
- Admin: login, dashboard, tambah/edit/hapus berita, upload gambar, status draft/publish, penanda berita utama, serta CRUD kategori.
- Password admin disimpan menggunakan `password_hash` (bcrypt) dan diverifikasi dengan `password_verify`.
- Query database menggunakan PDO prepared statements.
- Tampilan responsif untuk desktop dan ponsel.

## Persyaratan
- XAMPP (Apache + MySQL)
- PHP 8.0+ disarankan (ekstensi PDO MySQL, Fileinfo, dan mbstring aktif)
- Browser modern

## Cara instalasi di XAMPP
1. Ekstrak folder `portal_berita_pkl` ke `C:\xampp\htdocs\`.
2. Jalankan Apache dan MySQL dari XAMPP Control Panel.
3. Buka `http://localhost/phpmyadmin`.
4. Pilih menu **Import**, pilih file `database/db_berita.sql`, lalu klik **Import**. File SQL juga membuat database `db_berita`.
5. Periksa `config/database.php`. Konfigurasi bawaan XAMPP biasanya:
   - host: `localhost`
   - database: `db_berita`
   - username: `root`
   - password: kosong
6. Buka `http://localhost/portal_berita_pkl/`.
7. Login admin melalui `http://localhost/portal_berita_pkl/admin/login.php`.

## Akun admin awal
- Username: `admin`
- Password: `admin123`

**Penting:** ganti password admin sebelum website dipublikasikan/diunggah ke hosting. Akun bawaan hanya untuk instalasi lokal dan pembelajaran.

## Struktur folder
```text
portal_berita_pkl/
├── admin/
│   ├── index.php
│   ├── login.php
│   ├── logout.php
│   ├── berita.php
│   ├── kategori.php
│   ├── layout_top.php
│   └── layout_bottom.php
├── assets/
│   ├── app.js
│   └── style.css
├── config/
│   ├── database.php
│   └── helpers.php
├── database/
│   └── db_berita.sql
├── partials/
│   ├── header.php
│   └── footer.php
├── uploads/
├── detail.php
└── index.php
```

## Pengembangan database dari rancangan awal
Selain kolom inti dari instruksi, ditambahkan:
- `status` pada tabel berita untuk membedakan draft dan publish.
- `is_featured` untuk memilih berita utama.
- `created_at` dan `updated_at` untuk pencatatan waktu.
- `created_at` pada kategori dan admin.
- Foreign key berita ke kategori, indeks, serta aturan agar kategori yang masih dipakai tidak bisa dihapus.

## Catatan
- Format gambar yang diterima: JPG, PNG, WEBP; ukuran maksimal 3 MB.
- Pastikan folder `uploads` dapat ditulisi oleh PHP.
- Tanggal posting otomatis saat artikel baru disimpan.
- Ini adalah starter project pembelajaran. Sebelum digunakan secara publik, tambahkan CSRF token, pengaturan akun admin, pembatasan percobaan login, dan konfigurasi HTTPS.
# Web-berita
