# Production Planner

## 1. Overview

Production Planner adalah aplikasi sederhana untuk membagi rencana produksi kendaraan ke beberapa slot secara lebih merata tanpa mengubah total produksinya.

## 2. Tech Stack

- PHP 8.2+
- Laravel
- MySQL 8
- Blade
- Vanilla JavaScript
- PHPUnit / Laravel Testing

## 3. How to Run

### Requirements

Pastikan sudah terinstall:

- PHP 8.2+
- Composer 2.x
- MySQL 8.x

### Installation

Clone repository kemudian jalankan:

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Atur koneksi database pada `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=production_planner
DB_USERNAME=root
DB_PASSWORD=
```

Jalankan migration:

```bash
php artisan migrate
```

Kemudian jalankan aplikasi:

```bash
php artisan serve
```

Aplikasi dapat diakses melalui:

```text
http://localhost:8000
```

API tersedia di:

```text
http://localhost:8000/api/plannings
```

## 4. Running Tests

Untuk menjalankan seluruh test:

```bash
php artisan test
```

Untuk menjalankan test tertentu:

```bash
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature
```

## 5. Case 3 SQL

File SQL untuk Case 3 tersedia di `case3.sql`.

Untuk menjalankannya:

```bash
mysql -u root -p production_planner < case3.sql
```

## 6. API

| Method | Endpoint                    | Description                    |
| ------ | --------------------------- | ------------------------------ |
| POST   | `/api/plannings`            | Membuat dan memproses planning |
| GET    | `/api/plannings`            | Menampilkan riwayat planning   |
| GET    | `/api/plannings/{planning}` | Menampilkan detail planning    |

## 7. Balancing Logic

Balancing dilakukan hanya pada slot yang memiliki `original_quantity > 0`.

Total produksi dibagi secara merata ke seluruh slot aktif:

1. Hitung total produksi.
2. Hitung jumlah slot aktif.
3. Tentukan jumlah dasar menggunakan pembagian integer.
4. Jika masih ada sisa, tambahkan 1 ke slot dengan jumlah produksi awal terbesar.
5. Jika jumlah produksi awal sama, slot dengan urutan lebih kecil mendapat prioritas.
6. Slot yang tidak aktif tetap memiliki nilai `0`.

## 8. Business Rules

Beberapa aturan yang digunakan:

- Quantity tidak boleh negatif.
- Input kosong tidak diperbolehkan.
- Quantity harus berupa bilangan bulat.
- `slot_order` tidak boleh sama dalam satu planning.
- `RequestCode` tidak boleh sama.
- Jika request dengan `RequestCode` yang sama dikirim kembali, sistem akan menggunakan planning yang sudah ada dan tidak membuat data baru.
- Slot dengan quantity awal `0` dianggap tidak aktif.
- Total quantity sebelum dan sesudah balancing harus tetap sama.

## 9. Database Design

Data planning dipisahkan menjadi dua tabel:

```text
plannings
    |
    +-- planning_slots
```

`plannings` menyimpan informasi utama planning, sedangkan `planning_slots` menyimpan detail quantity untuk setiap slot.

Pemisahan ini membuat data slot lebih mudah dikelola dan memudahkan proses seperti menghitung total produksi, melihat riwayat, mencari data yang tidak sesuai, dan melihat perubahan quantity terbesar.

Aturan database yang digunakan:

- Primary key pada setiap tabel.
- Foreign key pada `planning_slots.planning_id`.
- `request_code` harus unik.
- `slot_order` harus unik dalam satu planning.
- Quantity tidak boleh bernilai negatif.

## 10. Assumptions

- Balancing dilakukan berdasarkan urutan `slot_order`, bukan urutan slot pada request.
- Slot dengan `original_quantity = 0` dianggap tidak aktif.
- Total produksi dihitung dari seluruh slot.
- `balanced_quantity` dapat bernilai `NULL` sebelum proses balancing selesai.
- Request dengan `RequestCode` yang sama dianggap sebagai request yang sama dan tidak membuat planning baru.
- Balancing dilakukan langsung saat request diproses.

## 11. Transactions

Penyimpanan planning dan slot dilakukan dalam satu database transaction.

Jika seluruh proses berhasil, data akan disimpan.

Jika terjadi error di tengah proses, perubahan akan dibatalkan sehingga tidak ada planning yang tersimpan tanpa detail slot.

## 12. Trade-offs

### Validation

Input diperiksa di bagian request dan juga di bagian balancing. Ada sedikit pengecekan yang berulang, tetapi hal ini membuat proses balancing tetap aman jika nantinya digunakan dari bagian aplikasi yang lain.

### Synchronous Processing

Balancing dilakukan langsung saat request diproses. Pendekatan ini cukup untuk jumlah slot yang kecil seperti pada assessment ini. Jika jumlah data atau proses menjadi lebih besar, balancing dapat dipindahkan ke proses background.

### Simple UI

UI menggunakan Blade, vanilla JavaScript, dan CSS sederhana. Pendekatan ini membuat project lebih ringan dan tidak membutuhkan proses build frontend yang kompleks. Jika aplikasi berkembang lebih besar, frontend dapat dikembangkan menggunakan framework yang lebih lengkap.