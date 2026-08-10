# Product Requirements Document (PRD)

# TraceID – Sistem Dokumentasi dan Verifikasi Bukti Digital Transaksi

Versi: 1.1 (MVP)
Platform: Web Application
Framework: Laravel 13 + Breeze + MySQL + Tailwind CSS + Alpine.js

> **Perubahan dari v1.0**: memperbaiki inkonsistensi token vs reference number pada link verifikasi, melengkapi alur status kasus, menambahkan aturan validasi upload, halaman error/expired, dan struktur non-fungsional yang lebih rinci. Lihat §15 untuk changelog lengkap.

---

## 1. Ringkasan produk

TraceID adalah aplikasi berbasis web yang digunakan untuk membuat halaman verifikasi transaksi dan mendokumentasikan bukti digital dari proses konfirmasi transaksi. Sistem memungkinkan admin membuat kasus, menghasilkan link verifikasi unik, serta mencatat informasi seperti waktu akses, alamat IP, metadata perangkat, lokasi, dan foto apabila pengguna memberikan izin melalui browser.

Tujuan utama sistem adalah membantu pengguna mengelola dokumentasi transaksi secara terstruktur sehingga setiap aktivitas konfirmasi dapat tercatat dalam satu dashboard.

## 2. Tujuan produk

### Tujuan utama

* Membuat halaman verifikasi transaksi.
* Mendokumentasikan aktivitas konfirmasi.
* Menyimpan bukti digital secara terstruktur.
* Menampilkan riwayat aktivitas.
* Menghasilkan laporan dokumentasi.

### Target pengguna

* Individu.
* Pelaku usaha.
* Admin operasional.
* Pengguna yang membutuhkan dokumentasi transaksi.

## 3. Ruang lingkup

### Dalam ruang lingkup

* Login admin.
* Dashboard.
* Manajemen kasus.
* Link verifikasi unik (dengan opsi regenerate/nonaktifkan).
* Verifikasi transaksi satu klik (tanpa input manual).
* Pencatatan metadata perangkat.
* Lokasi (dengan izin).
* Foto selfie (dengan izin).
* Riwayat aktivitas.
* Export PDF.
* Halaman error untuk link tidak valid / kedaluwarsa / sudah diverifikasi.

### Di luar ruang lingkup

* Multi-role / multi-user permission.
* Mobile application (native).
* Integrasi WhatsApp.
* Notifikasi email.
* Pembayaran/integrasi payment gateway (sistem hanya mendokumentasikan, tidak memproses transfer).

## 4. Role pengguna

Sistem hanya memiliki satu role.

### Admin

Admin memiliki akses penuh terhadap seluruh sistem.

Hak akses:

* Login.
* Membuat, mengubah, menghapus kasus.
* Menghasilkan, meregenerasi, dan menonaktifkan link verifikasi.
* Melihat data verifikasi, lokasi, dan foto.
* Mengunduh laporan PDF.
* Mengelola status kasus.

### Pengunjung (tanpa akun)

Pihak yang menerima link verifikasi. Tidak memiliki akses ke dashboard, hanya ke halaman verifikasi publik yang diberikan.

## 5. Fitur utama

### 5.1 Autentikasi (Laravel Breeze)

Fitur:

* Login.
* Logout.
* Update profil.
* Ganti password.

Tidak ada registrasi publik. Akun admin dibuat melalui database atau seeder.

### 5.2 Dashboard

Widget ringkasan:

* Total kasus.
* Total verifikasi.
* Verifikasi hari ini.
* Lokasi berhasil direkam.
* Foto berhasil direkam.

Aktivitas terbaru (feed):

* Link dibuat.
* Link dibuka.
* Verifikasi berhasil.

### 5.3 Manajemen kasus

Data kasus:

* Nomor referensi otomatis, format `TRC-YYYYMMDD-0001`.
* Nama target.
* Nama bank.
* Nomor rekening.
* Jumlah transfer.
* Catatan.
* Status.
* Tanggal dibuat.

Status kasus dan transisinya:

| Status | Deskripsi | Trigger |
|---|---|---|
| `aktif` | Kasus dibuat, link belum pernah dibuka | Setelah kasus & token dibuat |
| `link_dibuka` | Pengunjung sudah membuka halaman verifikasi minimal 1x, belum klik konfirmasi | Kunjungan pertama ke `/verify/{token}` |
| `terverifikasi` | Pengunjung berhasil klik konfirmasi transfer | Aksi konfirmasi sukses |
| `ditutup` | Admin menutup kasus secara manual (tidak ada aktivitas lanjutan yang mungkin) | Aksi admin |
| `kedaluwarsa` *(implisit, ditentukan dari `expires_at`, bukan kolom status terpisah)* | Token sudah lewat 24 jam dan kasus belum `terverifikasi` | Dicek saat token diakses |

Catatan: `kedaluwarsa` tidak wajib jadi nilai kolom `status` — cukup dihitung dari `expires_at < now()` saat request masuk, supaya status tetap sinkron tanpa perlu scheduled job. Kalau butuh tampil di tabel dashboard sebagai badge terpisah, hitung di accessor model (`isExpired()`), bukan disimpan sebagai status baru.

Fitur:

* Tambah, edit, hapus, lihat detail kasus.
* Regenerate link (membuat token baru + reset `expires_at`, hanya bisa dilakukan sebelum `terverifikasi`).
* Nonaktifkan link (set `status = ditutup` tanpa menghapus data kasus/riwayat).

### 5.4 Link verifikasi unik

Setiap kasus memiliki satu token unik yang dipakai sebagai bagian dari URL verifikasi.

Contoh:

```
https://traceid.app/verify/{token}
```

di mana `{token}` adalah string acak 32 karakter (bukan reference number — reference number bersifat human-readable dan tidak digunakan di URL publik agar tidak mudah ditebak/di-enumerate).

Karakteristik:

* Token: 32 karakter random, unik per kasus.
* Berlaku 24 jam sejak dibuat (`expires_at`), dicek setiap kali token diakses.
* Setelah kasus berstatus `terverifikasi`, mengakses ulang link menampilkan halaman "sudah diverifikasi" (read-only), bukan form isian ulang.
* Admin dapat menonaktifkan link kapan saja (§5.3).
* Admin dapat meregenerasi token jika link lama sudah terlanjur tersebar tapi belum diverifikasi.

### 5.5 Halaman verifikasi transaksi

Halaman ini dapat diakses tanpa login, selama token valid.

**Kondisi token tidak valid** — tampilkan halaman error yang sesuai, bukan 404 generik:

| Kondisi | Pesan yang ditampilkan |
|---|---|
| Token tidak ditemukan | "Link verifikasi tidak valid." |
| Token kedaluwarsa | "Link verifikasi sudah kedaluwarsa. Hubungi pengirim untuk link baru." |
| Kasus berstatus `ditutup` | "Link ini sudah tidak aktif." |
| Kasus berstatus `terverifikasi` | Tampilkan ringkasan konfirmasi (read-only), bukan error. |

Informasi yang ditampilkan (untuk token valid, belum diverifikasi):

* Nama penerima.
* Nama bank.
* Nomor rekening.
* Jumlah transfer.
* Nomor referensi.

Tombol utama: **Konfirmasi Transfer**

Alur interaksi publik dibuat sesederhana mungkin:

* Tidak ada field input manual.
* Pengunjung cukup klik **Konfirmasi Transfer** satu kali.
* Setelah tombol diklik, aplikasi meminta izin lokasi dan kamera dari browser lalu mengirim hasilnya otomatis jika izin diberikan.

Setelah aksi konfirmasi sukses: tampilkan halaman terima kasih/ringkasan, ubah status kasus menjadi `terverifikasi`, dan catat timestamp di riwayat aktivitas.

### 5.6 Pengambilan foto selfie

Setelah tombol konfirmasi ditekan, browser meminta izin kamera.

Apabila pengguna menyetujui:

1. Kamera depan dibuka (WebRTC).
2. Foto diambil.
3. Foto dikompresi di sisi klien sebelum dikirim.
4. Foto dikirim ke server bersama payload verifikasi otomatis.
5. Foto disimpan di storage **private** (tidak dapat diakses via URL publik langsung — hanya lewat dashboard admin dengan signed URL sementara).

Validasi upload:

* Format: JPEG/PNG/WebP.
* Ukuran maksimum: 5 MB (sebelum kompresi klien).
* Jika validasi gagal di server, verifikasi tetap tersimpan tapi `photo_status = gagal`, tidak membatalkan submit form.

Data yang disimpan:

* Path foto.
* Waktu pengambilan.
* Status foto (`diberikan` / `ditolak` / `gagal`).

Jika izin ditolak oleh pengguna: `photo_status = ditolak`, proses verifikasi tetap lanjut.

### 5.7 Pelacakan lokasi

Browser meminta izin lokasi via Geolocation API.

Data yang disimpan:

* Latitude, longitude, accuracy.
* Timestamp GPS.

Status lokasi (`location_status`): `diberikan` / `ditolak` / `gagal` (mis. timeout atau browser tidak mendukung).

Jika izin ditolak: proses verifikasi tetap lanjut, status tersimpan sebagai `ditolak`.

### 5.8 Metadata perangkat

Dicatat otomatis dari request, tanpa perlu izin eksplisit:

* IP address.
* Browser, sistem operasi, device type, user agent (di-parse dari `User-Agent` header).
* Bahasa (`Accept-Language`).
* Timezone dan resolusi layar (dikirim dari klien via JS saat halaman dimuat).

### 5.9 Riwayat aktivitas

Setiap aktivitas dicatat dengan timestamp. Contoh timeline:

```
10:15  Link dibuat
10:20  Link dibuka
10:21  Lokasi diberikan
10:21  Foto diberikan
10:22  Verifikasi selesai
```

Fitur: filter tanggal, pencarian, lihat detail.

### 5.10 Peta lokasi

Menggunakan Leaflet.js.

Fitur: marker lokasi, popup informasi (alamat perkiraan via reverse geocoding), link buka di Google Maps, zoom/pan.

### 5.11 Laporan (Export PDF)

Isi laporan:

* Detail kasus dan transaksi.
* Timeline aktivitas.
* Foto (jika ada, dengan watermark timestamp).
* Lokasi (peta statis/screenshot, jika ada).
* Metadata perangkat.

## 6. Alur sistem

### Alur Admin

1. Login.
2. Membuat kasus (isi data transaksi) → sistem generate `reference_number` + `token` + `expires_at`.
3. Mengirim link verifikasi ke penerima (di luar sistem — copy-paste manual, sesuai §3 "di luar ruang lingkup" untuk WhatsApp/email otomatis).
4. Memantau status kasus dari dashboard.
5. Melihat hasil verifikasi (foto, lokasi, metadata) di halaman detail kasus.
6. Mengunduh laporan PDF.

### Alur Pengunjung

1. Membuka link verifikasi.
2. Sistem validasi token (lihat tabel kondisi di §5.5).
3. Jika valid: melihat informasi transaksi, klik tombol **Konfirmasi Transfer**.
4. Browser meminta izin lokasi.
5. Browser meminta izin kamera/foto.
6. Sistem simpan data verifikasi (termasuk status izin), ubah status kasus jadi `terverifikasi`.
7. Melihat halaman konfirmasi/terima kasih.

## 7. Struktur database

### users

* id, name, email, password, created_at, updated_at

### cases

* id, reference_number, target_name, bank_name, account_number, amount, notes, status, token, expires_at, created_at, updated_at

### verifications

* id, case_id, photo_path, latitude, longitude, accuracy, ip_address, browser, operating_system, device_type, language, timezone, screen_resolution, user_agent, photo_status, location_status, created_at

> Skema ini konsisten dengan `AGENTS.md` §6. `bigint` auto-increment sebagai PK, snake_case, tanpa UUID/soft delete kecuali diminta.

## 8. API endpoint

### `POST /verify/{token}`

Middleware: `throttle` (rate limit), validasi token (bukan Laravel auth — token-based).

**Payload** (`multipart/form-data`, karena menyertakan file foto):

Endpoint ini tidak menerima input teks manual dari pengguna; payload diisi otomatis dari hasil izin perangkat/browser.

```json
{
  "photo": "file, nullable, image, max:5120 (KB), mimes:jpeg,png,webp",
  "latitude": "numeric, nullable, between:-90,90",
  "longitude": "numeric, nullable, between:-180,180",
  "accuracy": "numeric, nullable"
}
```

**Response sukses (200):**

```json
{
  "success": true,
  "message": "Verifikasi berhasil"
}
```

**Response gagal — token tidak valid (410 Gone):**

```json
{
  "success": false,
  "message": "Link verifikasi sudah kedaluwarsa atau tidak valid"
}
```

**Response gagal — validasi (422 Unprocessable Entity):** format error standar Laravel (`{"message": "...", "errors": {...}}`).

## 9. Keamanan

* CSRF protection pada form verifikasi (meski token-based, tetap perlu CSRF untuk mencegah cross-site submission).
* Rate limiting pada endpoint `POST /verify/{token}` (mis. 5 request/menit per IP) untuk mencegah brute-force token atau spam submission.
* Signed URL untuk akses foto dari dashboard admin (bukan public URL permanen).
* Token expiration (24 jam) dicek di setiap request ke `/verify/{token}`.
* HTTPS wajib di production.
* Laravel Validation (Form Request) di semua input, termasuk file upload (tipe & ukuran).
* Activity logging untuk audit trail (siapa membuka, kapan, dari IP mana).
* Middleware `auth` di semua route dashboard admin.

## 10. Kebutuhan non-fungsional

**Performa:**

* Dashboard render < 2 detik (koneksi normal).
* Upload foto (setelah kompresi klien) selesai terkirim < 3 detik.

**Kompatibilitas browser:** Chrome, Edge, Safari, Firefox — 2 versi mayor terakhir.

**Responsif:** Desktop, tablet, mobile (halaman verifikasi harus mobile-first karena mayoritas pengunjung akan membuka dari HP).

**Ketersediaan:** Tidak ada SLA formal untuk MVP, tapi downtime saat token pengunjung aktif harus diminimalkan (idealnya tidak deploy saat ada link yang sedang berlaku, atau pastikan zero-downtime deploy).

**Aksesibilitas dasar:** Form verifikasi harus bisa diisi dengan keyboard saja (tanpa mouse), label form jelas untuk screen reader dasar.
**Aksesibilitas dasar:** Tombol **Konfirmasi Transfer** harus bisa diakses via keyboard, dan prompt izin perangkat harus memiliki instruksi yang jelas.

**Privasi & retensi data:** Foto dan lokasi hanya dikumpulkan dengan izin eksplisit (lihat §14). Tidak ada kebijakan retensi/penghapusan otomatis di MVP — data disimpan permanen kecuali dihapus manual oleh admin.

## 11. Tampilan halaman

**Login:** Email, password, tombol login.

**Dashboard:** Statistik, aktivitas terbaru, tombol "Buat Kasus".

**Kasus (list):** Tabel kasus (dengan filter status), tombol "Generate Link" per baris, tombol detail.

**Detail Kasus:** Informasi transaksi, link verifikasi (dengan tombol copy, regenerate, nonaktifkan), riwayat verifikasi, foto, peta lokasi, tombol "Unduh PDF".

**Halaman Verifikasi (publik):** Informasi transaksi, tombol "Konfirmasi Transfer", prompt izin lokasi, dan prompt izin kamera.

**Halaman Error/Expired (publik):** Pesan sesuai tabel §5.5, tanpa membocorkan detail kasus.

**Halaman Terima Kasih (publik):** Konfirmasi bahwa verifikasi berhasil disimpan, ringkasan singkat (tanpa data sensitif tambahan).

## 12. Roadmap MVP

**Fase 1** — Login, Dashboard, CRUD kasus, Generate link.

**Fase 2** — Verifikasi satu klik, metadata perangkat, riwayat aktivitas, halaman error/expired.

**Fase 3** — Foto, lokasi, peta, PDF.

> Untuk breakdown teknis per-step (migration → model → ... → test), lihat `AGENTS.md` §13 "Roadmap MVP" — pemetaan Fase 1–3 di sini ke Step 1–8 di sana: Fase 1 ≈ Step 1–2, Fase 2 ≈ Step 3–4, Fase 3 ≈ Step 5–8.

## 13. Indikator keberhasilan

* Admin dapat login.
* Kasus dapat dibuat dengan reference number yang benar formatnya.
* Link verifikasi dapat diakses selama token valid, dan menampilkan halaman yang sesuai (konfirmasi satu klik / error / read-only) sesuai kondisi token.
* Verifikasi berhasil tersimpan setelah klik **Konfirmasi Transfer** dan status kasus berubah menjadi `terverifikasi`.
* Metadata perangkat tercatat otomatis di setiap kunjungan.
* Lokasi tercatat jika izin diberikan, status tersimpan dengan benar jika ditolak.
* Foto tercatat jika izin diberikan dan lolos validasi, status tersimpan dengan benar jika ditolak/gagal.
* Laporan PDF berhasil dibuat dan berisi seluruh data yang relevan.

## 14. Catatan privasi

Foto dan lokasi hanya dikumpulkan apabila pengguna memberikan izin secara eksplisit melalui browser. Sistem tidak mengambil data kamera atau lokasi tanpa persetujuan pengguna. Metadata perangkat (IP, user agent, dsb.) dicatat otomatis sebagai bagian dari log akses standar dan tidak memerlukan izin terpisah.

## 15. Changelog

**v1.1**
- Perbaikan: contoh link verifikasi sebelumnya memakai reference number (`TRC-8X2A91`), padahal reference number bersifat human-readable dan seharusnya tidak dipakai di URL publik. Diganti memakai token 32-karakter sesuai §5.4 dan `AGENTS.md`.
- Tambahan: tabel transisi status kasus (§5.3) — sebelumnya status hanya disebutkan tanpa dijelaskan trigger-nya.
- Tambahan: perilaku saat link diakses ulang setelah `terverifikasi`, dan saat status `ditutup`/kedaluwarsa (§5.5).
- Tambahan: aturan validasi upload foto (format, ukuran maksimum) — sebelumnya tidak disebutkan.
- Tambahan: fitur regenerate/nonaktifkan link (§5.3, §5.4) — disebutkan sebagai karakteristik token di v1.0 tapi belum ada di daftar fitur.
- Perbaikan: format JSON di §8 (sebelumnya key/value tanpa tanda kutip, bukan JSON valid), ditambah response error (410, 422) yang sebelumnya tidak ada.
- Tambahan: halaman error/expired dan halaman terima kasih di §3 dan §11 — alur sebelumnya berhenti di "Selesai" tanpa menjelaskan apa yang dilihat pengunjung.
- Tambahan: detail keamanan (rate limit spesifik, signed URL untuk foto) dan kebutuhan non-fungsional (aksesibilitas, retensi data, ketersediaan) yang sebelumnya terlalu tipis.
- Ditambahkan referensi silang ke `AGENTS.md` supaya roadmap dan skema database tidak perlu dijaga sinkron secara manual di dua tempat.
