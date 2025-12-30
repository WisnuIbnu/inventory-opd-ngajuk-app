# Sistem Informasi Inventaris OPD (Project PKL Diskominfo Nganjuk)

Sistem Informasi Inventaris OPD adalah aplikasi berbasis web yang digunakan untuk mengelola data inventaris barang pada setiap Dinas/OPD. Sistem ini dirancang dengan konsep **role-based access control** sehingga setiap pengguna hanya dapat mengakses data sesuai dengan kewenangannya.

Aplikasi dibangun menggunakan **Laravel 10** sebagai backend framework dan **Filament 3** sebagai admin panel untuk mempercepat pengembangan dan menjaga konsistensi UI.

---

## 🚀 Teknologi yang Digunakan

- PHP ^8.1
- Laravel Framework ^10.10
- Filament ^3.2
- Laravel Sanctum (Authentication)
- MySQL (Database)

### 📦 Package Tambahan
- `barryvdh/laravel-dompdf` → Generate laporan PDF (Download Stiker Berisi Detail Barang & Stiker)
- `maatwebsite/excel` → Download laporan dalam format Excel
- `flowframe/laravel-trend` → Grafik dan tren data inventaris
- `milon/barcode` → Generate barcode
- `simplesoftwareio/simple-qrcode` → Generate & scan QR Code
- `guzzlehttp/guzzle` → HTTP client

---

## ⚙️ DB Structure
![db structure](https://github.com/WisnuIbnu/inventory-opd-ngajuk-app/blob/main/public/db_structure.png?raw=true)

---

## 👥 Role Pengguna

Sistem memiliki dua peran utama:

### 1️⃣ Pegawai / Staf OPD

Sebagai Pegawai atau Staf OPD, pengguna memiliki akses terbatas sesuai dengan dinas/OPD tempatnya terdaftar.

#### Fitur Utama:
- Login menggunakan akun yang didaftarkan oleh Admin
- Setiap akun hanya terhubung ke **satu dinas/OPD**
- Akses dashboard inventaris khusus dinas sendiri
- Melihat ringkasan data:
  - Total barang
  - Barang rusak
  - Barang tidak digunakan
  - Diagram kondisi barang (baik, rusak, tidak digunakan)

#### Manajemen Inventaris:
- Akses katalog barang
- Scan QR Code untuk menampilkan detail barang otomatis
- Melihat detail barang:
  - Jenis barang
  - Merk
  - Nomor register
  - Tahun perolehan
  - Harga
  - Lokasi/gudang
  - Kondisi barang
  - Penanggung jawab

#### Hak Akses:
- Menambah, mengubah, dan menghapus data barang inventaris
- Mengunduh laporan inventaris sesuai kebutuhan dinas
- Mengunduh stiker QR Code:
  - Per barang
  - Seluruh barang dalam dinas
- ❌ Tidak dapat:
  - Mengubah nama dinas/OPD
  - Mengelola akun pengguna lain

---

### 2️⃣ Admin Inventaris

Admin Inventaris memiliki **hak akses penuh** terhadap seluruh sistem dan data.

#### Fitur Dashboard:
- Total seluruh barang inventaris
- Total barang rusak
- Total barang tidak digunakan
- Total gudang
- Total nilai aset (rupiah)
- Diagram kondisi barang
- Grafik tren penambahan aset (5 tahun terakhir)
- Grafik penambahan & perubahan aset per bulan
- Filter data berdasarkan dinas/OPD

#### Manajemen Inventaris:
- Katalog barang dengan scan QR Code
- Generate & download stiker QR Code unik
- Manajemen master data:
  - Gudang
  - Jenis barang
  - Penanggung jawab barang
- Validasi penghapusan data master:
  - Tidak dapat dihapus jika masih digunakan oleh data barang

#### Manajemen Laporan:
- Laporan barang rusak
- Laporan barang tidak digunakan
- Laporan barang per lokasi/gudang
- Filter laporan berdasarkan:
  - Dinas/OPD
  - Jenis barang
  - Kondisi barang
  - Rentang waktu (bulanan / tahunan / semua waktu)
- Export laporan ke : 
  - Download Stiker **PDF**
  - Download Laporan Inventaris **Excel**

#### Manajemen Pengguna:
- Tambah & edit akun pengguna
- Atur peran (Admin / OPD)
- Tentukan dinas/OPD pengguna
- Nonaktifkan akun pengguna

---

## 🔐 Keamanan & Akses Data

- Setiap data inventaris terisolasi berdasarkan dinas/OPD
- Hak akses dibatasi berdasarkan peran pengguna
- QR Code & Barcode bersifat **unik** untuk setiap barang
- Integritas relasi data dijaga untuk mencegah inkonsistensi

---