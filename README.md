## Generate Nomer Surat

Aplikasi untuk menyusun, mengurutkan, dan mengarsipkan nomor surat sesuai format instansi. Mendukung surat masuk/keluar, format nomor fleksibel berbasis segmen, dan pencegahan duplikasi nomor.

## Fitur Utama

- Format nomor surat kustom (segmen sequence, text, unit_code, tanggal/bulan/tahun).
- Generate nomor otomatis dengan counter global atau per unit.
- Surat keluar: wajib upload scan sebelum nomor berikutnya.
- Surat masuk: arsip scan dan metadata tanpa generate nomor.
- Log aktivitas untuk audit draft tertahan dan riwayat aksi.
- Role: admin (full), staff (surat & dashboard).

## Alur Singkat

1. Admin buat format nomor surat keluar (sekali).
2. Staff/admin generate nomor berdasarkan format.
3. Lengkapi data surat + upload scan PDF.
4. Nomor berikutnya hanya bisa jika draft sebelumnya selesai.

## Persyaratan

- PHP 8.2+
- Composer
- Node.js + npm
- Database (default SQLite)

## Instalasi

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
```

## Menjalankan Aplikasi

```bash
php artisan serve
```

## Seeder & Sample Data

```bash
php artisan migrate --seed
```

Seeder akan membuat:
- 1 admin + beberapa staff (dengan `unit_code`).
- Beberapa format surat keluar.
- Contoh surat masuk/keluar + log aktivitas.

## Akun Default

- Admin: `admin@gmail.com`
- Password: `password`

## Catatan Penting

- Jika format memakai `unit_code` atau scope counter per unit, user wajib punya `unit_code`.
- File scan disimpan di `storage/app/public/letters` (jalankan `php artisan storage:link`).

## Struktur Fitur Surat

- Surat Keluar: generate nomor → isi metadata → upload scan → selesai.
- Surat Masuk: isi metadata → upload scan → selesai.

## License

MIT
