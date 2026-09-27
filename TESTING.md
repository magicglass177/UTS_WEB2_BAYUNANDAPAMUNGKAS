# Pengujian Sistem Informasi Data Riwayat Pasien Praktek THT

## 1. Informasi Pengujian

Nama aplikasi:

**Sistem Informasi Data Riwayat Pasien Praktek THT**

Teknologi:

- Backend: Laravel 13
- Frontend: Vue 3
- Database: MySQL
- Autentikasi: Laravel Sanctum
- State Management: Pinia
- HTTP Client: Axios
- Build Tool: Vite

Role pengguna:

1. Admin
2. Petugas

---

## 2. Matriks Hak Akses

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
dihapus untuk menjaga integritas relasi data.

---

# 3. Test Case

## TC-01 - Login

### Tujuan

Memastikan pengguna dengan akun valid dapat masuk ke sistem.

### Pengujian

Login dilakukan menggunakan akun Admin dan Petugas.

### Hasil

Login berhasil dan pengguna diarahkan ke Dashboard.

### Status

**PASS**

---

## TC-02 - Logout dan Proteksi Halaman

### Tujuan

Memastikan sesi berakhir setelah Logout dan halaman yang
dilindungi tidak dapat digunakan tanpa autentikasi.

### Pengujian

1. Login ke sistem.
2. Tekan Logout.
3. Akses kembali halaman yang membutuhkan autentikasi.

### Hasil

Logout berhasil dan pengguna diarahkan kembali ke halaman
Login.

Endpoint yang membutuhkan autentikasi mengembalikan:

**HTTP 401 Unauthorized**

### Status

**PASS**

---

## TC-03 - Menampilkan Data dan Detail

### Tujuan

Memastikan data dari backend dapat ditampilkan oleh frontend.

### Pengujian

Dilakukan pada:

- Dashboard.
- Data Pasien.
- Detail Pasien.
- Riwayat Pemeriksaan.
- Detail Riwayat Pemeriksaan.

### Hasil

Data berhasil diambil dari API dan ditampilkan pada frontend.

Relasi pasien, riwayat pemeriksaan, dan petugas berhasil
ditampilkan.

### Status

**PASS**

---

## TC-04 - Empty State dan Pencarian

### Tujuan

Memastikan aplikasi menangani data kosong dan pencarian dengan
benar.

### Empty State

Kondisi daftar riwayat kosong disimulasikan pada frontend.

### Hasil

Frontend menampilkan informasi bahwa data belum tersedia tanpa
menyebabkan halaman blank.

### Pencarian Data Pasien

Pencarian diuji berdasarkan:

- Nama pasien.
- NIK.
- Nomor telepon.
- Alamat.
- Huruf besar dan kecil.

Kata kunci yang tidak tersedia menampilkan informasi bahwa data
pasien tidak ditemukan.

### Pencarian Riwayat Pemeriksaan

Pencarian diuji menggunakan informasi pada riwayat pemeriksaan,
seperti data pasien, keluhan, diagnosis, penanganan, dan
petugas.

Kata kunci yang tidak tersedia menampilkan informasi bahwa
riwayat pemeriksaan tidak ditemukan.

### Pencarian Dashboard

Pencarian cepat pasien diuji berdasarkan:

- Nama pasien.
- NIK.

Hasil pencarian dapat dipilih dan mengarahkan pengguna ke
halaman Detail Pasien.

### Status

**PASS**

---

## TC-05 - Tambah Data

### Tujuan

Memastikan data dapat ditambahkan dan disimpan pada database.

### Data Pasien

Admin berhasil menambahkan pasien melalui frontend.

Setelah halaman dimuat ulang, data pasien tetap tersedia.

### Riwayat Pemeriksaan

Riwayat pemeriksaan berhasil ditambahkan melalui frontend.

`user_id` ditentukan oleh backend berdasarkan pengguna yang
sedang login.

Setelah halaman dimuat ulang, data tetap tersedia.

### Status

**PASS**

---

## TC-06 - Edit Data dan Ownership

### Tujuan

Memastikan data dapat diperbarui sesuai hak akses.

### Data Pasien

Admin berhasil mengubah data pasien.

### Riwayat Pemeriksaan

Petugas berhasil mengubah riwayat yang dibuatnya sendiri.

Petugas tidak dapat mengubah riwayat milik pengguna lain.

Admin dapat mengubah riwayat milik Petugas.

Ketika Admin mengubah riwayat milik Petugas, kepemilikan
riwayat tetap tercatat sebagai Petugas.

### Status

**PASS**

---

## TC-07 - Validasi Input

### Required

Request dengan field wajib kosong menghasilkan:

**HTTP 422 Unprocessable Entity**

### Whitespace

Keluhan dan diagnosis yang hanya berisi whitespace ditolak.

### Boundary

Diagnosis:

- 256 karakter: ditolak.
- 255 karakter: diterima.

### Relasi Tidak Valid

`patient_id` yang tidak tersedia ditolak dengan:

**HTTP 422 Unprocessable Entity**

### Tanggal Pemeriksaan

Tanggal pemeriksaan di masa depan ditolak.

### Status

**PASS**

---

## TC-08 - API Tanpa Autentikasi

### Tujuan

Memastikan endpoint yang dilindungi tidak dapat diakses tanpa
login.

### Pengujian

Endpoint pasien diakses dalam keadaan Logout.

### Hasil

Backend mengembalikan:

**HTTP 401 Unauthorized**

### Status

**PASS**

---

## TC-09 - Otorisasi Data Pasien

### Tujuan

Memastikan operasi khusus Admin juga dilindungi pada backend.

### Pengujian Sebagai Petugas

Petugas mencoba:

- POST pasien.
- PUT pasien.
- DELETE pasien.

### Hasil

Backend menolak operasi tersebut dengan:

**HTTP 403 Forbidden**

### Status

**PASS**

---

## TC-10 - Otorisasi Riwayat Pemeriksaan

### Petugas Mengubah Riwayat Sendiri

**Berhasil.**

### Petugas Mengubah Riwayat Admin

**HTTP 403 Forbidden.**

### Petugas Menghapus Riwayat Admin

**HTTP 403 Forbidden.**

### Admin Mengubah Riwayat Petugas

**Berhasil.**

Kepemilikan riwayat tetap Petugas.

### Admin Menghapus Riwayat Petugas

**Berhasil.**

### Status

**PASS**

---

## TC-11 - Penghapusan Data

### Pasien Tanpa Riwayat

Admin berhasil menghapus pasien yang tidak mempunyai riwayat
pemeriksaan.

Setelah halaman dimuat ulang, pasien tetap tidak tersedia.

### Pasien Dengan Riwayat

Admin mencoba menghapus pasien yang masih mempunyai riwayat
pemeriksaan.

Backend mengembalikan:

**HTTP 409 Conflict**

Data pasien tetap tersedia.

### Riwayat Pemeriksaan

Petugas berhasil menghapus riwayat miliknya sendiri.

Admin berhasil menghapus riwayat milik Petugas.

### Konfirmasi Penghapusan

Ketika pengguna memilih Cancel pada dialog konfirmasi, data
tidak dihapus.

### Status

**PASS**

---

## TC-12 - Error Handling

### ID Tidak Ditemukan

Request terhadap ID riwayat yang tidak tersedia menghasilkan:

**HTTP 404 Not Found**

### Network Failure

Backend dimatikan sementara ketika frontend masih berjalan.

Frontend menampilkan pesan kegagalan memuat data dan tidak
mengalami blank page.

Setelah backend dijalankan kembali, data dapat dimuat kembali.

### Status

**PASS**

---

# 4. Ringkasan Pengujian

| Test Case | Pengujian | Status |
|---|---|---|
| TC-01 | Login | PASS |
| TC-02 | Logout dan proteksi halaman | PASS |
| TC-03 | List dan detail data | PASS |
| TC-04 | Empty state dan pencarian | PASS |
| TC-05 | Tambah data | PASS |
| TC-06 | Edit dan ownership | PASS |
| TC-07 | Validasi input | PASS |
| TC-08 | API tanpa autentikasi | PASS |
| TC-09 | Otorisasi pasien | PASS |
| TC-10 | Otorisasi riwayat | PASS |
| TC-11 | Penghapusan data | PASS |
| TC-12 | Error handling | PASS |

---

# 5. Persistence

Pengujian dilakukan dengan memuat ulang halaman setelah operasi
tambah, edit, dan hapus.

Data yang ditambahkan dan diperbarui tetap tersedia setelah
refresh.

Data yang telah dihapus tidak muncul kembali setelah refresh.

Hal ini menunjukkan bahwa perubahan data tersimpan secara
persisten pada database dan bukan hanya pada state frontend.

---

# 6. Fresh Database

Pengujian fresh database dilakukan menggunakan database
pengujian terpisah:

`uts_riwayat_pasien_tht_test`

Database utama:

`uts_riwayat_pasien_tht`

tidak digunakan untuk proses `migrate:fresh` sehingga data
pengujian pada database utama tetap aman.

Pengujian dilakukan dengan perintah:

`php artisan migrate:fresh --seed`

### Hasil Migration

Seluruh migration berhasil dijalankan tanpa error, meliputi:

- users
- cache
- jobs
- patients
- medical_records
- personal_access_tokens
- penambahan role pada users

### Hasil Seeder

Seluruh seeder berhasil dijalankan:

- UserSeeder
- PatientSeeder
- MedicalRecordSeeder

Setelah proses fresh database, jumlah data hasil seeder adalah:

| Data | Jumlah |
|---|---:|
| User | 2 |
| Patient | 3 |
| Medical Record | 3 |

Relasi `MedicalRecord` dengan `Patient` dan `User` juga berhasil
dibaca menggunakan Eloquent tanpa error.

Setelah pengujian selesai, koneksi dikembalikan ke database
utama dan data utama tetap tersedia.

### Status

**PASS**

# 7. Kesimpulan

Berdasarkan pengujian yang telah dilakukan, fungsi utama Sistem
Informasi Data Riwayat Pasien Praktek THT telah berjalan sesuai
kebutuhan yang diuji.

Sistem telah memiliki autentikasi, pembatasan akses berdasarkan
role dan ownership, pengelolaan pasien, pengelolaan riwayat
pemeriksaan, validasi server, persistence database, pencarian
data, perlindungan relasi saat penghapusan, serta penanganan
error API dan kegagalan jaringan.