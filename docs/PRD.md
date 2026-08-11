# Product Requirements Document (PRD)

# TraceID – Sistem Dokumentasi dan Verifikasi Bukti Digital (Transfer Bank & Social Media)

Versi: 3.0 (MVP)
Platform: Web Application
Framework: Laravel 13 + Breeze + MySQL + Tailwind CSS + Alpine.js

> **Perubahan dari v2.0**: arsitektur tidak lagi berbasis banyak record per transaksi. Sistem kini memiliki **dua konfigurasi tunggal (singleton)** — Bank Transfer dan Social Media — yang dibuat otomatis dengan data kosong dan mudah dikonfigurasi. Satu link publik `/verify` dipakai bersama banyak pengunjung; setiap pengunjung yang menekan tombol (**Konfirmasi** untuk transfer bank, **Follow** untuk social media) menghasilkan satu verifikasi terpisah (foto + lokasi + metadata). Lihat §15 untuk changelog lengkap.

---

## 1. Ringkasan produk

TraceID adalah aplikasi berbasis web untuk membuat halaman verifikasi bukti digital dan mendokumentasikan konfirmasi dari pengunjung. Admin cukup mengkonfigurasi **satu bank transfer** (nama bank, rekening, nominal) dan **satu social media** (platform, username, link profil, caption) sekali saja, lalu membagikan **satu link publik** ke banyak orang. Setiap pengunjung yang membuka link dapat menekan tombol konfirmasi (**Konfirmasi** untuk transfer bank, **Follow** untuk social media); sistem mencatat foto, lokasi, dan metadata perangkat dari setiap tindakan tersebut.

Tujuan utama sistem adalah membantu pengelola dokumentasi bukti (mis. penjual yang menagih transfer dan meminta follow akun sosmed) agar setiap konfirmasi tercatat terstruktur dalam satu dashboard.

## 2. Tujuan produk

### Tujuan utama

* Membuat halaman verifikasi bukti (transfer bank & follow social media).
* Mendokumentasikan setiap konfirmasi pengunjung.
* Menyimpan bukti digital (foto + lokasi) secara terstruktur.
* Menampilkan riwayat aktivitas.
* Menghasilkan laporan dokumentasi.

### Target pengguna

* Individu.
* Pelaku usaha.
* Admin operasional.
* Pengguna yang membutuhkan dokumentasi konfirmasi.

## 3. Ruang lingkup

### Dalam ruang lingkup

* Login admin.
* Dashboard.
* Konfigurasi bank transfer (tunggal, dibuat otomatis, data diisi belakangan).
* Konfigurasi social media (tunggal, dibuat otomatis, data diisi belakangan).
* Halaman verifikasi publik satu link (`/verify`) yang dipakai bersama.
* Verifikasi satu klik: **Konfirmasi** (transfer bank) dan **Follow** (social media).
* Pencatatan metadata perangkat.
* Lokasi (dengan izin).
* Foto selfie (dengan izin).
* Riwayat aktivitas.
* Export PDF.
* Halaman error untuk link tidak aktif / data belum diisi.

### Di luar ruang lingkup

* Multi-role / multi-user permission.
* Mobile application (native).
* Integrasi WhatsApp.
* Notifikasi email.
* Pembayaran/integrasi payment gateway (sistem hanya mendokumentasikan, tidak memproses transfer).
* Lebih dari satu konfigurasi per jenis — masing-masing hanya ada **satu** konfigurasi Bank Transfer dan **satu** konfigurasi Social Media.
* Verifikasi jenis lain selain transfer bank & follow social media.

## 4. Role pengguna

Sistem hanya memiliki satu role.

### Admin

Admin memiliki akses penuh terhadap seluruh sistem.

Hak akses:

* Login.
* Melengkapi/mengubah konfigurasi bank transfer.
* Melengkapi/mengubah konfigurasi social media.
* Mengaktifkan / menonaktifkan (menutup) tiap konfigurasi.
* Melihat data verifikasi, lokasi, dan foto.
* Mengunduh laporan PDF.

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

* Total verifikasi.
* Verifikasi bank transfer.
* Verifikasi social media.
* Verifikasi hari ini.
* Lokasi berhasil direkam.
* Foto berhasil direkam.

Aktivitas terbaru (feed):

* Konfigurasi bank transfer dibuat/diperbarui.
* Konfigurasi social media dibuat/diperbarui.
* Link dibuka.
* Konfirmasi transfer berhasil.
* Follow social media berhasil.

### 5.3 Konfigurasi bank transfer

Sistem hanya memiliki **satu** konfigurasi bank transfer (singleton).

Perilaku saat inisialisasi:

* Record dibuat otomatis (via seeder) dengan **data kosong** dan status `aktif`.
* Admin melengkapi data melalui halaman konfigurasi.

Data konfigurasi:

* Nama bank.
* Nomor rekening.
* Jumlah transfer.
* Catatan.

Status konfigurasi:

| Status | Deskripsi | Trigger |
|---|---|---|
| `aktif` | Section bank transfer tampil di halaman publik `/verify` | Default saat record dibuat |
| `ditutup` | Section bank transfer disembunyikan dari halaman publik | Aksi admin (menonaktifkan) |

Catatan penting:

* Status ini hanya **on/off tampilan section**, bukan status konsumsi — link dipakai bersama banyak pengunjung, jadi tidak ada `terverifikasi`/`link_dibuka` pada konfigurasi.
* Setiap pengunjung yang menekan tombol **Konfirmasi** menghasilkan satu record verifikasi baru (lihat §5.6).

Fitur:

* Melengkapi data bank transfer.
* Mengaktifkan / menonaktifkan section.

### 5.4 Konfigurasi social media

Sistem hanya memiliki **satu** konfigurasi social media (singleton).

Perilaku saat inisialisasi:

* Record dibuat otomatis (via seeder) dengan **data kosong** dan status `aktif`.
* Admin melengkapi data melalui halaman konfigurasi.

Data konfigurasi:

* Platform (mis. Instagram, TikTok, X, Facebook, YouTube).
* Username.
* Link profil.
* Instruksi/caption.

Status konfigurasi:

| Status | Deskripsi | Trigger |
|---|---|---|
| `aktif` | Section social media tampil di halaman publik `/verify` | Default saat record dibuat |
| `ditutup` | Section social media disembunyikan dari halaman publik | Aksi admin (menonaktifkan) |

Setiap pengunjung yang menekan tombol **Follow** menghasilkan satu record verifikasi baru (lihat §5.6).

### 5.5 Halaman verifikasi publik

Satu halaman publik dengan satu link:

```
https://traceid.app/verify
```

Halaman ini dapat diakses tanpa login. Kontennya ditentukan oleh konfigurasi yang berstatus `aktif`:

| Kondisi | Tampilan |
|---|---|
| Kedua konfigurasi `ditutup` | Halaman error "Link ini sudah tidak aktif." |
| Konfigurasi `aktif` dan data lengkap | Section tampil: informasi + tombol aksi |
| Konfigurasi `aktif` tapi data belum diisi | Section tampil pesan "Informasi belum tersedia. Hubungi pengirim." tanpa tombol |

Section Bank Transfer menampilkan:

* Nama bank.
* Nomor rekening.
* Jumlah transfer.
* Tombol **Konfirmasi**.

Section Social Media menampilkan:

* Platform dan username.
* Link profil (bisa dibuka).
* Instruksi/caption.
* Tombol **Follow**.

Alur interaksi publik dibuat sesederhana mungkin:

* Tidak ada field input manual.
* Pengunjung menekan tombol **Konfirmasi** dan/atau **Follow**.
* Setelah tombol ditekan, aplikasi meminta izin lokasi dan kamera dari browser lalu mengirim hasilnya otomatis jika izin diberikan.
* Setiap tombol yang ditekan = satu verifikasi terpisah; pengunjung boleh menekan keduanya.

Setelah aksi sukses: tampilkan halaman terima kasih/ringkasan dan catat timestamp di riwayat aktivitas. Halaman publik tetap bisa dipakai pengunjung lain (link tidak "habis").

### 5.6 Pencatatan verifikasi

Setiap penekanan tombol (**Konfirmasi** / **Follow**) menyimpan satu record ke tabel `verifications` dengan:

* `verification_type`: `bank_transfer` atau `social_media`.
* `reference_number`: otomatis, format `TRV-YYYYMMDD-0001`, untuk identifikasi di dashboard admin.
* Foto (jika izin diberikan).
* Lokasi (jika izin diberikan).
* Metadata perangkat.
* Status izin foto & lokasi.

### 5.7 Pengambilan foto selfie

Setelah tombol ditekan, browser meminta izin kamera.

Apabila pengguna menyetujui:

1. Kamera depan dibuka (WebRTC).
2. Sistem mengambil **maksimal 3 foto** secara berurutan (dengan jeda singkat antar pengambilan).
3. Foto dikompresi di sisi klien sebelum dikirim.
4. Foto dikirim ke server bersama payload verifikasi otomatis.
5. Foto disimpan di storage **private** (tidak dapat diakses via URL publik langsung — hanya lewat dashboard admin dengan signed URL sementara).

Validasi upload:

* Format: JPEG/PNG/WebP.
* Ukuran maksimum: 5 MB (sebelum kompresi klien).
* Maksimal 3 file per verifikasi; file ke-4 dan seterusnya diabaikan.
* Jika salah satu foto gagal validasi di server, foto yang valid tetap disimpan; jika tidak ada satupun yang valid, verifikasi tetap tersimpan tapi `photo_status = gagal`, tidak membatalkan submit.

Data yang disimpan:

* Path foto (array, hingga 3 entri).
* Waktu pengambilan.
* Status foto (`diberikan` / `ditolak` / `gagal`).

Jika izin ditolak oleh pengguna: `photo_status = ditolak`, proses verifikasi tetap lanjut.

### 5.8 Pelacakan lokasi

Browser meminta izin lokasi via Geolocation API.

Data yang disimpan:

* Latitude, longitude, accuracy.
* Timestamp GPS.

Status lokasi (`location_status`): `diberikan` / `ditolak` / `gagal` (mis. timeout atau browser tidak mendukung).

Jika izin ditolak: proses verifikasi tetap lanjut, status tersimpan sebagai `ditolak`.

### 5.9 Metadata perangkat

Dicatat otomatis dari request, tanpa perlu izin eksplisit:

* IP address.
* Browser, sistem operasi, device type, user agent (di-parse dari `User-Agent` header).
* Bahasa (`Accept-Language`).
* Timezone dan resolusi layar (dikirim dari klien via JS saat halaman dimuat).

### 5.10 Riwayat aktivitas

Setiap aktivitas dicatat dengan timestamp. Contoh timeline:

```
10:15  Konfigurasi bank transfer diperbarui
10:20  Link dibuka
10:21  Lokasi diberikan
10:21  Foto diberikan
10:22  Konfirmasi transfer berhasil
```

Fitur: filter tanggal, filter jenis, pencarian, lihat detail.

### 5.11 Peta lokasi

Menggunakan Leaflet.js.

Fitur: marker lokasi, popup informasi (alamat perkiraan via reverse geocoding), link buka di Google Maps, zoom/pan.

### 5.12 Laporan (Export PDF)

Isi laporan:

* Ringkasan konfigurasi aktif (bank transfer & social media).
* Daftar/timeline verifikasi.
* Foto (jika ada, dengan watermark timestamp).
* Lokasi (peta statis/screenshot, jika ada).
* Metadata perangkat.

## 6. Alur sistem

### Alur Admin

1. Login.
2. Melengkapi konfigurasi bank transfer (record sudah dibuat otomatis dengan data kosong).
3. Melengkapi konfigurasi social media.
4. Membagikan link `/verify` ke pengunjung (di luar sistem — copy-paste manual, sesuai §3).
5. Memantau verifikasi dari dashboard.
6. Melihat hasil verifikasi (foto, lokasi, metadata) di halaman detail.
7. Mengunduh laporan PDF.

### Alur Pengunjung

1. Membuka link `/verify`.
2. Sistem menampilkan section sesuai konfigurasi yang `aktif` (lihat tabel §5.5).
3. Menekan tombol **Konfirmasi** (transfer bank) dan/atau **Follow** (social media).
4. Browser meminta izin lokasi.
5. Browser meminta izin kamera/foto.
6. Sistem simpan data verifikasi (termasuk status izin), tampilkan halaman terima kasih.
7. Pengunjung lain tetap bisa membuka link yang sama dan verifikasi secara mandiri.

## 7. Struktur database

### users

* id, name, email, password, created_at, updated_at

### bank_transfers *(singleton — hanya 1 baris, dibuat otomatis via seeder, data kosong)*

* id, bank_name, account_number, amount, notes, status, created_at, updated_at

### social_media *(singleton — hanya 1 baris, dibuat otomatis via seeder, data kosong)*

* id, platform, username, profile_url, caption, status, created_at, updated_at

### verifications

* id, verification_type, reference_number, photo_paths, latitude, longitude, accuracy, ip_address, browser, operating_system, device_type, language, timezone, screen_resolution, user_agent, photo_status, location_status, created_at

### activity_logs

* id, verification_type, activity, description, created_at

> `verification_type` adalah enum: `bank_transfer` / `social_media`. Tidak ada foreign key ke konfigurasi karena masing-masing konfigurasi hanya satu baris.
>
> `photo_paths` adalah JSON array (nullable) yang menampung hingga 3 path foto (§5.7).

> Skema ini konsisten dengan `AGENTS.md` §6. `bigint` auto-increment sebagai PK, snake_case, tanpa UUID/soft delete kecuali diminta.

## 8. API endpoint

### `POST /verify`

Middleware: `throttle` (rate limit), CSRF.

**Payload** (`multipart/form-data`, karena menyertakan file foto):

```json
{
  "type": "required, in:bank_transfer,social_media",
  "photo": "file[], nullable, maksimal 3 file, image, max:5120 (KB) per file, mimes:jpeg,png,webp",
  "latitude": "numeric, nullable, between:-90,90",
  "longitude": "numeric, nullable, between:-180,180",
  "accuracy": "numeric, nullable"
}
```

Endpoint ini tidak menerima input teks manual dari pengguna selain `type` (diisi otomatis dari tombol yang ditekan); payload lainnya diisi otomatis dari hasil izin perangkat/browser.

**Response sukses (200):**

```json
{
  "success": true,
  "message": "Verifikasi berhasil"
}
```

**Response gagal — konfigurasi ditutup / section tidak aktif (410 Gone):**

```json
{
  "success": false,
  "message": "Link ini sudah tidak aktif"
}
```

**Response gagal — validasi (422 Unprocessable Entity):** format error standar Laravel (`{"message": "...", "errors": {...}}`).

## 9. Keamanan

* CSRF protection pada form verifikasi.
* Rate limiting pada halaman dan endpoint `/verify` (GET dan POST, mis. 10 request/menit per IP) untuk mencegah spam submission.
* Signed URL untuk akses foto dari dashboard admin (bukan public URL permanen).
* HTTPS wajib di production.
* Laravel Validation (Form Request) di semua input, termasuk file upload (tipe & ukuran).
* Activity logging untuk audit trail (siapa menekan tombol, kapan, dari IP mana).
* Middleware `auth` di semua route dashboard admin.

## 10. Kebutuhan non-fungsional

**Performa:**

* Dashboard render < 2 detik (koneksi normal).
* Upload foto (setelah kompresi klien) selesai terkirim < 3 detik.

**Kompatibilitas browser:** Chrome, Edge, Safari, Firefox — 2 versi mayor terakhir.

**Responsif:** Desktop, tablet, mobile (halaman verifikasi harus mobile-first karena mayoritas pengunjung akan membuka dari HP).

**Ketersediaan:** Tidak ada SLA formal untuk MVP.

**Aksesibilitas dasar:** Tombol konfirmasi harus bisa diakses via keyboard, dan prompt izin perangkat harus memiliki instruksi yang jelas.

**Privasi & retensi data:** Foto dan lokasi hanya dikumpulkan dengan izin eksplisit (lihat §14). Tidak ada kebijakan retensi/penghapusan otomatis di MVP — data disimpan permanen kecuali dihapus manual oleh admin.

## 11. Tampilan halaman

**Login:** Email, password, tombol login.

**Dashboard:** Statistik, aktivitas terbaru, tombol "Konfigurasi".

**Konfigurasi Bank Transfer:** Form data bank (nama bank, rekening, nominal, catatan) + toggle aktif/ditutup + tombol "Salin Link".

**Konfigurasi Social Media:** Form data (platform, username, link profil, caption) + toggle aktif/ditutup + tombol "Salin Link".

**Halaman Verifikasi (publik):** Section per konfigurasi aktif — bank transfer (info + tombol **Konfirmasi**) dan social media (info + link profil + tombol **Follow**); prompt izin lokasi dan kamera.

**Halaman Terima Kasih (publik):** Konfirmasi bahwa verifikasi berhasil disimpan, ringkasan singkat (tanpa data sensitif tambahan).

**Halaman Error (publik):** Pesan sesuai tabel §5.5, tanpa membocorkan detail transaksi.

## 12. Roadmap MVP

**Fase 1** — Login, Dashboard, konfigurasi bank transfer (auto-created + isi data belakangan), halaman verifikasi publik dasar.

**Fase 2** — Konfigurasi social media, verifikasi satu klik (Konfirmasi/Follow), metadata perangkat, riwayat aktivitas, halaman error.

**Fase 3** — Foto, lokasi, peta, PDF.

> Untuk breakdown teknis per-step (migration → model → ... → test), lihat `AGENTS.md` §13 "Roadmap MVP" — pemetaan Fase 1–3 di sini ke Step 1–8 di sana: Fase 1 ≈ Step 1–2, Fase 2 ≈ Step 3–4, Fase 3 ≈ Step 5–8.

## 13. Indikator keberhasilan

* Admin dapat login.
* Konfigurasi bank transfer dan social media tersedia otomatis (data kosong) dan dapat dilengkapi, diubah, serta diaktifkan/dinonaktifkan.
* Link `/verify` menampilkan section sesuai konfigurasi yang `aktif`.
* Setiap penekanan tombol **Konfirmasi** / **Follow** menyimpan satu verifikasi dengan `verification_type` dan `reference_number` yang benar.
* Metadata perangkat tercatat otomatis di setiap kunjungan.
* Lokasi tercatat jika izin diberikan, status tersimpan dengan benar jika ditolak.
* Foto tercatat jika izin diberikan dan lolos validasi, status tersimpan dengan benar jika ditolak/gagal.
* Laporan PDF berhasil dibuat dan berisi seluruh data yang relevan.

## 14. Catatan privasi

Foto dan lokasi hanya dikumpulkan apabila pengguna memberikan izin secara eksplisit melalui browser. Sistem tidak mengambil data kamera atau lokasi tanpa persetujuan pengguna. Metadata perangkat (IP, user agent, dsb.) dicatat otomatis sebagai bagian dari log akses standar dan tidak memerlukan izin terpisah.

## 15. Changelog

**v1.1**
- Perbaikan: contoh link verifikasi sebelumnya memakai reference number (`TRC-8X2A91`), padahal reference number bersifat human-readable dan seharusnya tidak dipakai di URL publik. Diganti memakai token 32-karakter.
- Tambahan: tabel transisi status, perilaku saat link diakses ulang, aturan validasi upload foto, fitur regenerate/nonaktifkan link, response error API, halaman error/expired, detail keamanan dan kebutuhan non-fungsional.

**v1.2**
- Perubahan: pengambilan foto selfie dari 1 foto menjadi maksimal 3 foto per verifikasi (`photo_paths` JSON array).

**v1.3**
- Tambahan: konsep verification template engine dan master data `verification_templates`.

**v2.0**
- Penghapusan total: konsep `cases`, `verification_templates`, dan verification template engine. Entitas inti diganti `bank_transfers`.
- Penghapusan: token 32-karakter dan masa berlaku 24 jam. Link verifikasi berbasis `reference_number` dan berlaku sampai `ditutup`/`terverifikasi`.
- Perubahan: saat membuat bank transfer, data bank dibuat kosong dan diisi admin belakangan.

**v3.0**
- **Perubahan arsitektur**: tidak lagi per-record. Sistem memiliki **dua konfigurasi tunggal (singleton)** — `bank_transfers` dan `social_media` — masing-masing dibuat otomatis (via seeder) dengan data kosong, mudah dikonfigurasi (§5.3, §5.4, §7).
- **Penambahan**: konfigurasi social media (platform, username, link profil, caption) dengan tombol **Follow** (§5.4, §5.5).
- **Perubahan**: satu halaman publik `/verify` yang menampilkan section sesuai konfigurasi `aktif`; tombol **Konfirmasi** (transfer bank) dan **Follow** (social media) (§5.5).
- **Perubahan**: link dipakai bersama banyak pengunjung — semua pengunjung bisa verifikasi; status konfigurasi hanya `aktif`/`ditutup` (on/off section), bukan status konsumsi (§5.3).
- **Perubahan**: `verifications` memakai `verification_type` (enum) + `reference_number` format `TRV-YYYYMMDD-0001` menggantikan FK `bank_transfer_id` dan format `TRC-*`; `activity_logs` ikut memakai `verification_type` (§7).
- Penyesuaian: API endpoint `POST /verify` kini menerima payload `type` (§8), keamanan (hapus response 409 "sudah diverifikasi"), roadmap, tampilan halaman, dan indikator keberhasilan.
