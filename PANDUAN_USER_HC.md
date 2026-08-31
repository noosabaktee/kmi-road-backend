# 📘 Buku Panduan Penggunaan Sistem KMI Road
### Khusus Tim Human Capital (HC) & Panduan Karyawan
**PT Sanghiang Perkasa • Kalbe Nutritionals**

---

## 📌 1. Gambaran Umum Sistem

Sistem **KMI Road** dirancang untuk memudahkan pengelolaan perjalanan dinas karyawan dan mobil operasional perusahaan secara transparan, otomatis, dan akurat. 

Sistem ini memfasilitasi 3 kelompok pengguna:
1. **Karyawan Pemohon Dinas**: Mengajukan peminjaman kendaraan operasional secara mandiri tanpa perlu membuat akun.
2. **Tim Human Capital (HC / Admin)**: Mengelola jadwal tugas dinas, menugaskan driver dan armada, memantau pergerakan mobil secara *Live GPS*, serta memvalidasi pengeluaran BBM dan bukti foto dokumentasi.
3. **Driver Operasional**: Menerima tugas dinas melalui aplikasi Android, memancarkan koordinat GPS secara otomatis, dan mengunggah foto bukti fisik (*checkpoint*).

---

## 👥 2. Panduan Karyawan (Pengajuan Mandiri)

Karyawan tidak memerlukan akun untuk membuat permohonan dinas.

### A. Mengisi Formulir Pengajuan
1. Buka tautan portal: **`http://127.0.0.1:8000/`** (atau domain server kantor).
2. Isi **Identitas Pemohon**:
   - Nama Lengkap Karyawan
   - Departemen (pilih dari dropdown list)
   - Nomor WhatsApp / Handphone yang dapat dihubungi.
3. Pilih **Tanggal Dinas**:
   - Setelah tanggal dipilih, sistem secara otomatis mengecek ketersediaan seluruh mobil.
4. Pilih **Mobil Operasional**:
   - Sistem menampilkan kartu setiap mobil beserta badge sisa kapasitas kursi (contoh: *5 Kursi Tersedia*).
   - **Kapasitas Penuh**: Jika kuota kursi pada mobil tersebut di tanggal bersangkutan sudah habis (`booked_seats >= intMaxSeat`), kartu mobil otomatis terkunci/disabled dengan label *"Kapasitas Penuh (0 Kursi Tersisa)"*. Karyawan harus memilih kendaraan lain yang masih tersedia.
5. Isi **Tujuan & Keperluan Dinas**:
   - Lokasi tujuan (misal: *Pabrik Kalbe Cikarang / Distributor Pulogadung*).
   - Keperluan dinas (misal: *Audit Kalibrasi Mesin Mixing*).
6. Klik tombol **"Kirim Pengajuan Dinas"**.

### B. Bukti Tiket & Pengecekan Status Mandiri
1. Setelah pengajuan terkirim, halaman konfirmasi sukses akan menampilkan **Kode Referensi Tiket** (contoh: `REQ-20260830-4721`).
2. Karyawan dapat mengecek perkembangan penugasan driver sewaktu-waktu pada menu **"Cek Status Tiket"** (`/cek-status`) dengan memasukkan kode tiket tersebut.

---

## 🛡️ 3. Panduan Tim Human Capital (Admin Portal)

### A. Masuk ke Portal Admin HC
1. Buka tautan: **`http://127.0.0.1:8000/admin/login`**
2. Masukkan kredensial Admin HC:
   - **Email**: `admin@kmi.kalbe.co.id`
   - **Password**: `admin123`
3. Klik **"Masuk ke Portal HC"**.

---

### B. Dashboard Monitoring Armada
Halaman Dashboard menyajikan ringkasan real-time operasional:
- **Total Armada & Mobil Aktif Berdinas**: Jumlah mobil yang sedang jalan di lapangan.
- **Kesiapan Driver**: Jumlah driver dengan status *Siap Dinas (Available)* vs *Sedang Bertugas*.
- **Antrean Tiket Pending**: Pengajuan karyawan yang belum dijadwalkan driver-nya.
- **Estimasi Biaya BBM Bulan Berjalan**: Total akumulasi biaya pengisian bensin.
- **Tabel Tugas Dinas Aktif**: Akses cepat melihat perjalanan yang sedang berlangsung.

---

### C. Live Tracking GPS Radar Armada (`/admin/live-tracking`)
Fitur ini bekerja mirip *WhatsApp Live Location* untuk memantau posisi seluruh armada secara real-time:
1. Buka menu **"Live Tracking GPS"** di sidebar.
2. Peta interaktif Leaflet akan menampilkan penanda posisi (*marker*) setiap kendaraan yang sedang bertugas.
3. Posisi armada dan rute diperbarui secara otomatis setiap **5 detik** dari sinyal telemetry aplikasi Android driver.
4. **Interaksi Peta**:
   - Klik kartu armada di panel kiri atau klik marker mobil di peta untuk melihat detail:
     - Nama Driver & No. Telepon
     - Kecepatan Kendaraan Saat Ini (km/h)
     - Angka Odometer Terakhir
     - Waktu Terakhir Terdeteksi (*Last Ping*)
     - Daftar Karyawan Penumpang di dalam mobil tersebut.

---

### D. Manajemen Penugasan Dinas (`/admin/trips`)

Terdapat dua cara pembuatan jadwal dinas:

#### 1. Mengelompokkan Pengajuan Karyawan (Assign Form)
- Pada halaman **Jadwal & Dinas**, klik tab **"Pengajuan Karyawan Baru"**.
- Tim HC dapat memilih beberapa pengajuan karyawan dengan tujuan yang sama, kemudian klik **"Assign Mobil & Driver"**.
- Sistem akan otomatis membentuk 1 Tugas Dinas (`trDutyTrip`) dengan multi-penumpang (`trDutyTrip_Details`).

#### 2. Membuat Jadwal Tugas Dinas Manual Langsung
- Klik tombol **"+ Buat Jadwal Dinas Baru"** (`/admin/trips/create`).
- Pilih Armada Mobil dan Driver yang bertugas.
- Tentukan Tanggal & Jam Keberangkatan, Lokasi Tujuan, dan Keperluan.
- Tambahkan nama-nama karyawan penumpang (dapat menambah baris penumpang secara dinamis).
- Klik **"Simpan & Tugaskan ke Driver"**. Notifikasi tugas akan langsung muncul di HP driver bersangkutan.

---

### E. Inspeksi Rute & Verifikasi Bukti Foto Checkpoint (`/admin/trips/{id}`)
Untuk memastikan transparansi dan keaslian perjalanan dinas:
1. Buka detail tugas dinas dengan mengklik tombol **"Detail & Foto"**.
2. Halaman ini menyajikan:
   - **Informasi Perjalanan**: Driver, kendaraan, waktu berangkat, waktu sampai, dan selisih total KM tempuh (Odometer Akhir - Odometer Awal).
   - **Rekap Pengisian BBM**: Total liter dan total biaya (Rp) yang diinput driver.
   - **Riwayat Rute Breadcrumbs GPS**: Jalur yang dilalui kendaraan di peta.
   - **Galeri Foto Bukti Checkpoint**:
     1. 📸 *Foto Sebelum Berangkat*: Bukti kondisi fisik mobil sebelum jalan.
     2. ⛽ *Foto Isi BBM*: Bukti foto struk bensin / dispenser pom SPBU.
     3. 📍 *Foto Sampai Tujuan*: Bukti fisik tiba di lokasi kantor/pabrik tujuan.
     4. 🏁 *Foto Selesai*: Bukti foto akhir perjalanan dan speedometer.

---

### F. Manajemen Master Kendaraan & Driver
- **Master Mobil (`/admin/vehicles`)**: Mengatur data mobil, nomor plat, tipe kendaraan, jenis BBM rekomendasi, serta batas maksimal kapasitas kursi (`intMaxSeat`) yang menjadi acuan validasi form karyawan.
- **Master Driver (`/admin/drivers`)**: Mendaftarkan akun driver baru, mengatur lisensi SIM, nomor HP, dan mereset kata sandi driver.

---

### G. Laporan & Ekspor Data (`/admin/reports`)
- Memfilter riwayat perjalanan dinas berdasarkan rentang tanggal, status perjalanan, atau driver tertentu.
- Melihat total jarak tempuh (KM) dan efisiensi konsumsi bahan bakar.
- Klik **"Ekspor Data (CSV/Excel)"** untuk mengunduh rekapitulasi laporan pertanggungjawaban operasional HC.

---

## 📱 4. Panduan Driver Operasional (Aplikasi Android)

Driver dibekali aplikasi Flutter `kmi-road-driver`:

1. **Login & Toggle Status**:
   - Driver masuk dengan email (contoh: `joko.santoso@kmi.kalbe.co.id` / `driver123`).
   - Driver dapat menyalakan status **"Siap Dinas"** atau mematikan menjadi **"Istirahat"**.
2. **Menerima Tugas**:
   - Tugas dinas yang telah di-assign Admin HC otomatis tampil di layar utama.
   - Driver dapat melihat daftar nama penumpang dan tombol chat WA penumpang.
3. **Mulai Perjalanan & Odometer Awal**:
   - Tekan **"Mulai Perjalanan Dinas"**, masukkan angka odometer awal.
   - GPS Telemetry otomatis aktif dan memancarkan koordinat berkala ke Admin.
4. **Mengunggah Bukti Foto Checkpoint (4 Titik Wajib)**:
   - Tekan tombol kamera sesuai checkpoint:
     - 📸 **Sebelum Berangkat**
     - ⛽ **Isi BBM & Struk**: Masukkan nominal Rp, liter, dan foto struk SPBU.
     - 📍 **Sampai Lokasi**: Tekan saat tiba di tujuan.
     - 🏁 **Selesai Dinas**: Masukkan odometer akhir dan foto kondisi akhir.
5. **Selesaikan Tugas**:
   - Tekan **"Selesaikan Perjalanan"**. Status mobil otomatis kembali *Available* dan seluruh data tercatat rapi di portal Admin HC.
