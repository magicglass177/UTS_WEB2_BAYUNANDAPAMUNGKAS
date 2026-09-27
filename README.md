# Sistem Informasi Data Riwayat Pasien Praktek THT

Sistem Informasi Data Riwayat Pasien Praktek THT merupakan
aplikasi berbasis web yang digunakan untuk membantu pengelolaan
data pasien dan riwayat pemeriksaan pasien pada sebuah praktek
THT.

Aplikasi dibangun menggunakan Laravel sebagai backend API,
Vue sebagai frontend, dan MySQL sebagai database.

---

## 1. Fitur Utama

Aplikasi memiliki fitur:

- Login dan Logout.
- Dashboard statistik.
- Pencarian cepat pasien pada Dashboard.
- Melihat data pasien.
- Pencarian data pasien.
- Melihat detail pasien.
- Menambah data pasien.
- Mengubah data pasien.
- Menghapus pasien yang belum memiliki riwayat pemeriksaan.
- Melihat riwayat pemeriksaan.
- Pencarian riwayat pemeriksaan.
- Melihat detail riwayat pemeriksaan.
- Menambah riwayat pemeriksaan.
- Mengubah riwayat pemeriksaan.
- Menghapus riwayat pemeriksaan.
- Pembatasan akses berdasarkan role.
- Pembatasan edit dan hapus berdasarkan ownership.
- Validasi data pada backend.
- Penanganan error API dan kegagalan jaringan.

---

## 2. Teknologi

### Backend

- PHP 8.3
- Laravel 13
- Laravel Sanctum
- MySQL 8

### Frontend

- Vue 3
- Vite
- Vue Router
- Pinia
- Axios

---

## 3. Role Pengguna

Aplikasi memiliki dua role pengguna.

### Admin

Admin mempunyai akses untuk mengelola data pasien dan seluruh
riwayat pemeriksaan.

### Petugas

Petugas dapat melihat data pasien dan menambahkan riwayat
pemeriksaan.

Petugas hanya dapat mengubah dan menghapus riwayat pemeriksaan
yang dibuat oleh dirinya sendiri.

---

## 4. Matriks Hak Akses

| Fitur | Admin | Petugas |
|---|---|---|
| Login | Ya | Ya |
| Logout | Ya | Ya |
| Dashboard | Ya | Ya |
| Pencarian cepat pasien | Ya | Ya |
| Melihat pasien | Ya | Ya |
| Mencari pasien | Ya | Ya |
| Melihat detail pasien | Ya | Ya |
| Menambah pasien | Ya | Tidak |
| Mengubah pasien | Ya | Tidak |
| Menghapus pasien tanpa riwayat | Ya | Tidak |
| Menghapus pasien dengan riwayat | Tidak | Tidak |
| Melihat riwayat pemeriksaan | Ya | Ya |
| Mencari riwayat pemeriksaan | Ya | Ya |
| Melihat detail pemeriksaan | Ya | Ya |
| Menambah riwayat pemeriksaan | Ya | Ya |
| Mengubah riwayat sendiri | Ya | Ya |
| Mengubah riwayat pengguna lain | Ya | Tidak |
| Menghapus riwayat sendiri | Ya | Ya |
| Menghapus riwayat pengguna lain | Ya | Tidak |

Pasien yang masih mempunyai riwayat pemeriksaan tidak dapat
dihapus agar integritas relasi data tetap terjaga.

---

## 5. Struktur Data

Aplikasi menggunakan tiga tabel utama.

### users

Digunakan untuk menyimpan akun pengguna.

Field utama:

- id
- name
- email
- password
- role

### patients

Digunakan untuk menyimpan data pasien.

Field:

- id
- nik
- name
- gender
- birth_date
- phone
- address

### medical_records

Digunakan untuk menyimpan riwayat pemeriksaan.

Field:

- id
- patient_id
- user_id
- examination_date
- complaint
- diagnosis
- treatment
- notes

---

## 6. Relasi Database

Relasi utama aplikasi:

```text
USERS
  |
  | 1
  |
  | N
MEDICAL_RECORDS
  N
  |
  | 1
  |
PATIENTS
```

Secara sederhana:

```text
Patient 1 -------- N MedicalRecord N -------- 1 User
```

Artinya:

- Satu pasien dapat mempunyai banyak riwayat pemeriksaan.
- Satu riwayat pemeriksaan hanya dimiliki oleh satu pasien.
- Satu pengguna dapat membuat banyak riwayat pemeriksaan.
- Satu riwayat pemeriksaan dicatat oleh satu pengguna.

---

## 7. Alur Data

Contoh alur ketika pengguna menambahkan riwayat pemeriksaan:

```text
Pengguna
   |
   v
Vue Form
   |
   v
Axios Request
   |
   v
Laravel API
   |
   v
Validasi
   |
   v
Controller
   |
   v
Eloquent Model
   |
   v
MySQL
   |
   v
JSON Response
   |
   v
Vue
   |
   v
Tampilan Data
```

`user_id` riwayat pemeriksaan tidak ditentukan oleh frontend.

Backend menentukan `user_id` berdasarkan pengguna yang sedang
login sehingga kepemilikan riwayat tidak dapat ditentukan
secara bebas dari request frontend.

---

## 8. Arsitektur Aplikasi

Aplikasi menggunakan pemisahan backend dan frontend.

```text
Browser
   |
   v
Vue SPA
   |
   | HTTP / Axios
   v
Laravel REST API
   |
   | Eloquent ORM
   v
MySQL
```

Frontend berjalan pada:

```text
http://127.0.0.1:5173
```

Backend berjalan pada:

```text
http://127.0.0.1:8000
```

---

## 9. Endpoint API

Endpoint API membutuhkan autentikasi kecuali proses Login.

### Authentication

```text
POST /login
POST /logout
GET  /api/user
```

### Dashboard

```text
GET /api/dashboard
```

### Patients

```text
GET    /api/patients
GET    /api/patients/{patient}
POST   /api/patients
PUT    /api/patients/{patient}
DELETE /api/patients/{patient}
```

Operasi POST, PUT, dan DELETE pasien hanya dapat dilakukan
oleh Admin.

### Medical Records

```text
GET    /api/medical-records
GET    /api/medical-records/{medicalRecord}
POST   /api/medical-records
PUT    /api/medical-records/{medicalRecord}
DELETE /api/medical-records/{medicalRecord}
```

Petugas hanya dapat mengubah atau menghapus riwayat yang
dibuat oleh dirinya sendiri.

Admin dapat mengubah dan menghapus seluruh riwayat
pemeriksaan.

---

## 10. Validasi

### Pasien

Data pasien memiliki validasi seperti:

- NIK wajib diisi.
- NIK harus unik.
- Nama wajib diisi.
- Jenis kelamin wajib diisi.
- Tanggal lahir wajib diisi.
- Nomor telepon bersifat opsional.
- Alamat bersifat opsional.

### Riwayat Pemeriksaan

Validasi riwayat pemeriksaan:

- Pasien wajib dipilih.
- Pasien harus tersedia pada database.
- Tanggal pemeriksaan wajib diisi.
- Tanggal pemeriksaan tidak boleh berada di masa depan.
- Keluhan wajib diisi.
- Diagnosis wajib diisi.
- Diagnosis maksimal 255 karakter.
- Penanganan bersifat opsional.
- Catatan bersifat opsional.

Validasi dilakukan pada backend agar request langsung ke API
tetap mengikuti aturan aplikasi.

---

## 11. Fitur Pencarian

### Dashboard

Dashboard menyediakan pencarian cepat pasien berdasarkan:

- Nama.
- NIK.

Hasil pencarian dapat diklik untuk membuka halaman Detail
Pasien.

### Data Pasien

Data pasien dapat dicari berdasarkan:

- NIK.
- Nama.
- Nomor telepon.
- Alamat.

### Riwayat Pemeriksaan

Riwayat pemeriksaan dapat dicari berdasarkan informasi yang
tersedia pada data pemeriksaan, seperti:

- Nama pasien.
- NIK.
- Keluhan.
- Diagnosis.
- Penanganan.
- Petugas.

Pencarian dilakukan pada data yang telah diperoleh frontend
dari API.

---

## 12. Instalasi Project

### Persyaratan

Pastikan komputer sudah mempunyai:

- PHP 8.3 atau versi yang kompatibel.
- Composer.
- MySQL.
- Node.js.
- npm.

Clone atau salin project ke komputer.

Masuk ke folder project.

---

## 13. Setup Backend

Masuk ke folder backend:

```powershell
cd backend
```

Install dependency:

```powershell
composer install
```

Salin file environment:

```powershell
Copy-Item .env.example .env
```

Generate application key:

```powershell
php artisan key:generate
```

Atur konfigurasi database pada `.env`.

Contoh:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=uts_riwayat_pasien_tht
DB_USERNAME=root
DB_PASSWORD=
```

Buat database:

```text
uts_riwayat_pasien_tht
```

Kemudian jalankan migration dan seeder:

```powershell
php artisan migrate --seed
```

Jalankan backend:

```powershell
php artisan serve --host=127.0.0.1 --port=8000
```

Backend dapat diakses pada:

```text
http://127.0.0.1:8000
```

---

## 14. Setup Frontend

Buka terminal baru kemudian masuk ke folder frontend:

```powershell
cd frontend
```

Install dependency:

```powershell
npm install
```

Jalankan development server:

```powershell
npm run dev -- --host 127.0.0.1
```

Frontend dapat diakses pada:

```text
http://127.0.0.1:5173
```

---

## 15. Akun Demo

Seeder menyediakan dua akun:

### Admin

```text
Email: admin@tht.test
Role : admin
```

### Petugas

```text
Email: petugas@tht.test
Role : petugas
```

Password akun demo ditentukan pada `UserSeeder` project.

Akun tersebut hanya digunakan sebagai akun demonstrasi dan
pengujian lokal.

---

## 16. Menjalankan Aplikasi

Jalankan backend:

```powershell
cd backend
php artisan serve --host=127.0.0.1 --port=8000
```

Kemudian pada terminal lain jalankan frontend:

```powershell
cd frontend
npm run dev -- --host 127.0.0.1
```

Buka browser:

```text
http://127.0.0.1:5173
```

Login menggunakan salah satu akun demo.

---

## 17. Pengujian

Pengujian aplikasi meliputi:

- Login.
- Logout.
- Proteksi halaman.
- Proteksi API.
- Menampilkan data.
- Detail data.
- Tambah data.
- Edit data.
- Hapus data.
- Persistence.
- Empty state.
- Pencarian.
- Required validation.
- Whitespace validation.
- Boundary validation.
- Invalid relation.
- Role authorization.
- Ownership.
- HTTP 401.
- HTTP 403.
- HTTP 404.
- HTTP 409.
- HTTP 422.
- Network failure.
- Fresh database migration dan seeding.
- Pengujian relasi Eloquent setelah fresh database.

Dokumentasi pengujian lebih lengkap tersedia pada:

```text
TESTING.md
```

---

## 18. Keterbatasan Sistem

Versi aplikasi saat ini difokuskan pada kebutuhan dasar
pengelolaan riwayat pasien.

Fitur yang belum tersedia antara lain:

- Pendaftaran pasien secara online.
- Sistem antrean pasien.
- Pembayaran.
- Manajemen resep secara kompleks.
- Upload dokumen.
- Export PDF.
- Integrasi WhatsApp.
- Notifikasi otomatis.
- Pagination dan pencarian server-side.

Pencarian saat ini dilakukan pada frontend sehingga lebih
sesuai untuk jumlah data yang belum terlalu besar.

---

## 19. Keamanan dan Hak Akses

Aplikasi menerapkan:

- Session authentication.
- Laravel Sanctum.
- Middleware autentikasi.
- Middleware role Admin.
- Pemeriksaan ownership riwayat pemeriksaan.
- Server-side validation.
- Pembatasan penghapusan pasien yang memiliki riwayat.

Pembatasan akses tidak hanya dilakukan dengan menyembunyikan
tombol pada frontend.

Backend tetap melakukan pemeriksaan sehingga request langsung
yang tidak mempunyai izin akan ditolak.

---

## 20. Status Project

Fungsi utama yang telah tersedia:

- Authentication.
- Dashboard.
- Statistik.
- Pencarian.
- CRUD Pasien.
- CRUD Riwayat Pemeriksaan.
- Detail Pasien.
- Detail Riwayat Pemeriksaan.
- Role authorization.
- Ownership authorization.
- Server validation.
- Error handling.
- Persistence database.

Pengujian fresh database telah berhasil dilakukan menggunakan
database pengujian terpisah. Migration, seeder, dan relasi
database berhasil dijalankan tanpa error.

Project berada pada tahap finalisasi sebelum pengumpulan.