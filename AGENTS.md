# AGENTS.md — TraceID

Aturan pengembangan untuk AI agent (Laravel Boost + Claude Code, dsb.) yang bekerja di repo ini. Dokumen ini menggabungkan pedoman standar **Laravel Boost** dengan aturan spesifik **proyek TraceID**. Jika ada pertentangan, aturan proyek TraceID menang, kecuali disebutkan lain.

---

> **⚠️ KRITIS**: Sebelum mengerjakan **tugas apapun**, WAJIB baca dan patuhi:
> 1. **Product Requirements Document (PRD)** di `docs/PRD.md` — sumber kebenaran utama untuk semua fitur, alur bisnis, dan spesifikasi.
> 2. **Bagian 7 (Business Rules)** di bawah untuk aturan inti yang sering dipakai.
>
> PRD berisi detail yang tidak diulang di sini (status transisi, validasi upload, error handling, dsb.). Jika ada konflik antara AGENTS.md dan PRD, **PRD yang menang**.

## 1. Project Overview

**TraceID** adalah aplikasi Laravel untuk dokumentasi dan verifikasi bukti digital transaksi.

| Aspek | Pilihan |
|---|---|
| Framework | Laravel 13, PHP 8.3 |
| Auth | Laravel Breeze (Blade) |
| Frontend | Blade + Tailwind CSS + Alpine.js |
| Database | MySQL |
| Maps | Leaflet.js |
| Export | DomPDF |
| Arsitektur | MVC + Service Layer |
| Scope saat ini | MVP |

Jangan pernah mengasumsikan versi API sebuah package. Sebelum memakai API package tertentu, cek versi yang terpasang:
- PHP: `composer show --direct` (semua dependency langsung) atau `composer show <vendor/package>` (satu package).
- JS: cek `package.json`.

---

## 2. Prinsip Inti

- AI **wajib** mengikuti PRD dan alur kerja bertahap di bagian §7.
- AI **dilarang** mengubah arsitektur, database, teknologi, atau dependency tanpa instruksi eksplisit dari user (lihat §9 "AI Restrictions").
- Setiap step pada roadmap harus selesai dan bisa diuji sebelum lanjut ke step berikutnya — jangan melompati urutan.
- Jika ada instruksi yang tidak jelas atau berpotensi melanggar aturan di atas, **tanya dulu** sebelum membuat perubahan.
- Hanya buat file dokumentasi baru jika diminta eksplisit oleh user.
- Ikuti konvensi kode yang sudah ada di repo — cek file sejenis (sibling files) untuk struktur, pendekatan, dan penamaan sebelum menulis kode baru. Cek dulu apakah sudah ada komponen yang bisa dipakai ulang sebelum membuat yang baru.
- Jangan buat script verifikasi manual atau pakai Tinker kalau sudah ada test (unit/feature) yang meng-cover fungsionalitas tersebut — test lebih diutamakan.
- Jangan ubah struktur folder dasar aplikasi tanpa persetujuan.
- Balasan ke user: ringkas, fokus ke hal penting, tidak perlu menjelaskan hal yang sudah jelas.

---

## 3. Tooling & Riset (Laravel Boost)

Repo ini terhubung ke **Laravel Boost MCP server**. Utamakan tool Boost dibanding alternatif manual (shell command, baca file manual, dsb).

| Tool | Kegunaan |
|---|---|
| `database-query` | Query read-only ke database, ganti raw SQL di tinker |
| `database-schema` | Cek struktur tabel sebelum bikin migration/model |
| `get-absolute-url` | Resolve scheme/domain/port yang benar sebelum share URL ke user |
| `browser-logs` | Baca log browser/error/exception (fokus entry terbaru saja) |
| `search-docs` | **Wajib dipanggil sebelum ubah kode apa pun.** Docs versi-spesifik sesuai package yang terpasang |
| `record-rule` | Simpan aturan baru yang durable agar terwarisi ke agent/teammate berikutnya |

### Cara pakai `search-docs`
- Kirim array `packages` untuk mempersempit hasil kalau sudah tahu package-nya.
- Pakai beberapa query luas berbasis topik: `['rate limiting', 'routing rate limiting', 'routing']`. Hasil paling relevan muncul duluan.
- Jangan sertakan nama package di query (info package sudah diketahui sistem) — pakai `test resource table`, bukan `laravel 13 test resource table`.
- Sintaks: kata biasa = AND (auto-stem); `"frasa dikutip"` = exact match posisi berdekatan; kombinasi keduanya boleh; beberapa query = OR logic.

### Project rules directory (`.ai/rules`)
Jika direktori `.ai/rules` ada di repo:
1. Baca `@.ai/rules/index.md` dulu — memetakan glob file ke rule file.
2. Baca semua rule file yang glob-nya mencakup path yang sedang dikerjakan (termasuk `.ai/rules/boost` untuk guideline framework/package spesifik-path).
3. `grep -rin 'keyword' .ai/rules` untuk menangkap aturan yang tidak tertangkap dari glob match saja.
4. **Jangan mulai menulis kode** sebelum semua rule yang relevan dibaca dan diikuti.

Jika `.ai/rules` tidak ada, lewati langkah ini.

### Artisan
- Jalankan Artisan langsung: `php artisan route:list`, dst. Selalu tambahkan `--no-interaction` di semua command Artisan.
- `php artisan list` untuk lihat command yang tersedia, `php artisan [command] --help` untuk parameter.
- Filter route: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Baca config: `php artisan config:show app.name` atau langsung dari file di `config/`.

### Tinker
- Untuk debugging saja. **Jangan buat model lewat Tinker tanpa persetujuan user** — pakai test dengan factory sebagai gantinya.
- Utamakan Artisan command yang sudah ada daripada kode Tinker custom.
- Selalu pakai single quote di shell agar tidak ada expansion: `php artisan tinker --execute 'User::where("active", true)->count();'` (double quote untuk string PHP di dalamnya).

---

## 4. Standar Kode PHP

- Selalu pakai curly braces untuk semua control structure, termasuk body satu baris.
- Pakai PHP 8 constructor property promotion: `public function __construct(private CaseService $caseService) {}`. Jangan biarkan `__construct()` kosong tanpa parameter kecuali memang private constructor.
- Deklarasikan return type dan type hint eksplisit di semua method: `function isAccessible(User $user, ?string $path = null): bool`.
- Enum key pakai TitleCase: `FavoritePerson`, `Monthly`.
- Utamakan PHPDoc block dibanding inline comment; inline comment hanya untuk logic yang benar-benar kompleks. Pakai array shape type di PHPDoc kalau relevan.
- Ikuti PSR-12 secara umum.
- Setelah mengubah file PHP, **wajib** jalankan `vendor/bin/pint --dirty --format agent` sebelum selesai (bukan `--test`, langsung fix).

---

## 5. Arsitektur Laravel (Do It the Laravel Way)

- Buat file baru (migration, controller, model, dst) lewat `php artisan make:...`, bukan manual. Untuk class PHP generic, pakai `php artisan make:class`.
- **Controller harus tipis** — hanya terima request, panggil service, kembalikan response. Business logic **tidak boleh** ada di controller. Maksimal ±150 baris per controller.
- **Validasi selalu lewat Form Request** (`StoreCaseRequest`, `UpdateCaseRequest`, `StoreVerificationRequest`, dst). Jangan pakai `->validate()` langsung di controller.
- **Business logic** ditempatkan di `app/Services`: `CaseService`, `VerificationService`, `PhotoService`, `LocationService`.
- **Model**: gunakan Eloquent, definisikan semua relationship secara eksplisit (`CaseFile hasMany Verification`, `Verification belongsTo CaseFile`). Gunakan `$fillable`, **jangan** pakai `guarded = []`.
- Saat membuat model baru, sekalian buat factory dan seeder-nya; tanyakan ke user kalau butuh opsi lain (`php artisan make:model --help`).
- API (jika ada): default pakai Eloquent API Resources + versioning, kecuali route API yang sudah ada tidak melakukan itu — ikuti konvensi yang sudah berjalan.
- Link ke halaman lain: pakai named route + helper `route()`.
- Error handling: pakai `abort_if()`, `findOrFail()`. `try-catch` hanya kalau memang perlu. Pakai Laravel logging.

---

## 6. Database

- `bigint` auto-increment sebagai primary key, bukan UUID.
- Selalu pakai `timestamps()` dan `foreignId()->constrained()` untuk foreign key.
- Jangan pakai soft delete kecuali diminta eksplisit.
- Nama tabel: plural. Nama kolom: snake_case.

**Skema saat ini:**

```
users
  id, name, email, password, created_at, updated_at

cases
  id, reference_number, target_name, bank_name, account_number,
  amount, notes, token, status, expires_at, created_at, updated_at

verifications
  id, case_id, photo_path, latitude, longitude, accuracy, ip_address,
  browser, operating_system, device_type, language, timezone,
  screen_resolution, user_agent, photo_status, location_status, created_at
```

Sebelum membuat/mengubah migration, cek struktur tabel aktual lewat tool `database-schema`, jangan hanya mengandalkan tabel di atas (bisa saja sudah berubah).

---

## 7. Business Rules

- **Reference number**: format `TRC-YYYYMMDD-0001`.
- **Token verifikasi**: 32 karakter random, unik, expired 24 jam.
- **Status kasus**: `aktif` → `link_dibuka` → `terverifikasi` / `ditutup`.
- **Verifikasi satu klik**: tidak ada field input manual — pengunjung cukup klik **Konfirmasi Transfer**; browser meminta izin lokasi & kamera lalu mengirim hasilnya otomatis.
- Foto dan lokasi bersifat **opsional**; status verifikasi tetap tersimpan meskipun user menolak izin kamera/lokasi.

---

## 8. Konvensi Penamaan

| Jenis | Contoh |
|---|---|
| Model | `CaseFile`, `Verification` |
| Controller | `CaseController`, `VerificationController` |
| Form Request | `StoreCaseRequest`, `UpdateCaseRequest` |
| Route name | `cases.index`, `cases.store`, `verification.show`, `verification.store` |
| View | `resources/views/cases/index.blade.php`, `.../create.blade.php`, `.../show.blade.php` |

---

## 9. Frontend

**Blade**
- Pakai Blade Component (`x-input`, `x-button`, `x-card`, dst) kalau markup dipakai lebih dari sekali. Jangan menulis HTML yang sama berulang-ulang.
- Layout utama: `resources/views/layouts/app.blade.php`.

**Tailwind**
- Utility class dulu. Custom CSS hanya kalau benar-benar perlu.

**JavaScript**
- Vanilla JS atau Alpine.js saja. **Jangan** React, **jangan** Vue.
- Geolocation API untuk lokasi, WebRTC untuk kamera.

**Build**
- Kalau perubahan frontend tidak muncul di UI, minta user jalankan `npm run build`, `npm run dev`, atau `composer run dev` — jangan asumsikan sendiri harus yang mana.
- Error `Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest` → jalankan `npm run build` atau minta user jalankan `npm run dev` / `composer run dev`.

---

## 10. Security

Wajib ada di setiap fitur yang relevan:
- CSRF protection
- Mass assignment protection (`$fillable`, bukan `guarded = []`)
- Route middleware `auth` di halaman yang butuh login
- Signed URL untuk link verifikasi publik
- Rate limiting
- Validasi input lewat Form Request
- File disimpan di disk `private`/`public` sesuai kebutuhan (foto verifikasi → private, kecuali diminta lain)

Jangan pernah:
- Menonaktifkan CSRF
- Menulis raw SQL
- Menyimpan file upload tanpa validasi

---

## 11. Testing

Proyek ini pakai **Pest**.

- Buat test: `php artisan make:test --pest {Name}` untuk feature test (mayoritas test harus feature test), tambahkan `--unit` untuk unit test.
  - Argumen `{name}` tidak perlu prefix direktori suite — pakai `SomeFeatureTest`, bukan `Feature/SomeFeatureTest`.
- Jalankan test: `php artisan test --compact`, atau filter dengan `--filter=testName`.
- Saat membuat model untuk test, pakai factory — cek dulu apakah factory sudah punya state khusus yang bisa dipakai sebelum bikin manual.
- Faker: ikuti konvensi yang sudah dipakai di repo, `$this->faker->word()` atau `fake()->randomDigit()` — jangan campur gaya seenaknya.
- **Jangan hapus test** tanpa persetujuan user.
- "Test manual" di roadmap step tetap dilakukan sebagai verifikasi akhir per step (klik-klik di browser/Postman), tapi ini **melengkapi**, bukan **menggantikan**, Pest feature test — kalau ada test otomatis yang sudah cover, jangan tulis ulang script verifikasi manual/tinker untuk hal yang sama (lihat §2).

---

## 12. Alur Kerja Pengembangan (per task)

Untuk setiap task baru, ikuti urutan ini — jangan melompat:

1. Analisis kebutuhan.
2. Migration.
3. Model (+ factory, seeder).
4. Relationship.
5. Form Request.
6. Service (kalau ada business logic).
7. Controller.
8. Route.
9. Blade view.
10. Feature test (Pest) + test manual sebagai verifikasi akhir.
11. Refactor.

**Definition of done** untuk satu task:
- Migration berhasil dijalankan.
- Model selesai (dengan relationship, factory).
- Validation (Form Request) selesai.
- Business logic ada di Service, bukan di controller.
- Controller selesai (≤150 baris, tipis).
- Route + view selesai.
- Fitur bisa diuji manual, tidak ada error Laravel.
- Ada feature test Pest yang mengcover fitur.
- Kode lolos `vendor/bin/pint --dirty --format agent`.
- Tidak ada duplikasi kode.

---

## 13. Roadmap MVP

| Step | Scope | Selesai jika... |
|---|---|---|
| 1 | Instalasi Laravel + Breeze, auth, dashboard | Login berfungsi |
| 2 | Migration & model Case, CRUD, generate reference number | CRUD berjalan |
| 3 | Halaman verifikasi publik, token validation, status "link dibuka" | Link bisa diakses |
| 4 | Form verifikasi, simpan metadata device/IP/browser | Data tersimpan |
| 5 | Photo capture, storage, validasi, preview | Foto tersimpan |
| 6 | Geolocation, peta Leaflet, reverse geocoding | Lokasi tampil |
| 7 | Statistik dashboard, timeline aktivitas, chart | Statistik akurat |
| 8 | Export PDF, print layout, ringkasan kasus | PDF bisa diunduh |

---

## 14. AI Restrictions

AI **dilarang**, tanpa instruksi eksplisit dari user:
- Mengganti framework.
- Mengganti Blade menjadi React/Vue.
- Mengganti skema database (termasuk beralih ke UUID atau menambah soft delete).
- Menambah package/dependency baru.
- Mengubah struktur folder dasar aplikasi.
- Mengubah naming convention yang sudah ditetapkan di §8.

Kalau ada ketidakjelasan pada instruksi user, **tanya dulu** sebelum melakukan perubahan yang berpotensi melanggar aturan di atas.

---

## 15. Git Workflow

Satu fitur = satu commit terpisah, pesan commit deskriptif dan konvensional:

```
feat: add case migration
feat: implement case service
feat: add case CRUD
fix: validation on verification form
```

---

## 16. Referensi Cepat PRD

| Topik | PRD Section |
|-------|-------------|
| Ringkasan produk | §1 |
| Tujuan produk | §2 |
| Ruang lingkup | §3 |
| Role pengguna | §4 |
| Fitur utama | §5 |
| Alur sistem | §6 |
| Struktur database | §7 |
| API endpoint | §8 |
| Keamanan | §9 |
| Kebutuhan non-fungsional | §10 |
| Tampilan halaman | §11 |
| Roadmap MVP (bisnis) | §12 |
| Indikator keberhasilan | §13 |
| Catatan privasi | §14 |
| Changelog | §15 |

---

**Catatan Akhir**: 
- Jika ada perbedaan antara AGENTS.md dan PRD.md, **PRD adalah sumber kebenaran**.
- Selalu baca `docs/PRD.md` sebelum mengerjakan fitur baru.
- Update AGENTS.md ini jika ada perubahan di PRD yang mempengaruhi teknis.

===

<laravel-boost-guidelines>
=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to ensure the best experience when building Laravel applications.

## Foundational Context

This application is a Laravel application running on PHP 8.3. You are an expert with the Laravel ecosystem. Always use the APIs that match the installed major version of each package — do not assume a version.

Before relying on a package's API, confirm its installed version:
- PHP packages: run `composer show --direct` to list direct dependencies with versions, or `composer show <vendor/package>` for a single package.
- JS packages: check `package.json` for the installed versions.

## Skills Activation

This project has domain-specific skills available in `**/skills/**`. You MUST activate the relevant skill whenever you work in that domain—don't wait until you're stuck.

## Conventions

- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts

- Do not create verification scripts or tinker when tests cover that functionality and prove they work. Unit and feature tests are more important.

## Application Structure & Architecture

- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling

- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Documentation Files

- You must only create documentation files if explicitly requested by the user.

## Replies

- Be concise in your explanations - focus on what's important rather than explaining obvious details.

=== boost rules ===

# Laravel Boost

## Tools

- Laravel Boost is an MCP server with tools designed specifically for this application. Prefer Boost tools over manual alternatives like shell commands or file reads.
- Use `database-query` to run read-only queries against the database instead of writing raw SQL in tinker.
- Use `database-schema` to inspect table structure before writing migrations or models.
- Use `get-absolute-url` to resolve the correct scheme, domain, and port for project URLs. Always use this before sharing a URL with the user.
- Use `browser-logs` to read browser logs, errors, and exceptions. Only recent logs are useful, ignore old entries.

## Searching Documentation (IMPORTANT)

- Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.
- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries because package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Use words for auto-stemmed AND logic: `rate limit` matches both "rate" AND "limit".
2. Use `"quoted phrases"` for exact position matching: `"infinite scroll"` requires adjacent words in order.
3. Combine words and phrases for mixed queries: `middleware "rate limit"`.
4. Use multiple queries for OR logic: `queries=["authentication", "middleware"]`.

## Project Rules

- This project contains committed, area-grouped rules in `.ai/rules` when that directory exists (settled decisions, non-obvious traps, standing constraints). Framework and package guidelines that only apply to specific paths (testing, frontend, components) also live there, under `.ai/rules/boost` — this is not just recorded decisions, it is load-bearing guidance you have not seen inline. Before you enter plan mode or create/edit any file, you MUST first: open @.ai/rules/index.md (it maps file globs to rule files), read every rule file whose globs cover the path(s) in scope, and run `grep -rin 'keyword' .ai/rules` to catch what a path match alone misses. Do not write code until you have read and are following every matching rule. If `.ai/rules` does not exist, continue without it.
- Record durable rules with `record-rule` so the next agent or teammate inherits them instead of working them out again. Pass a `glob` (e.g. `app/Http/Controllers/**`), a short `title`, and a few-line `note`. Always use `record-rule`, never your native memory or notes tool — native memory is personal and session-scoped; only `.ai/rules` is shared with the team and persists in the repo.

## Artisan

- Run Artisan commands directly via the command line (e.g., `php artisan route:list`). Use `php artisan list` to discover available commands and `php artisan [command] --help` to check parameters.
- Inspect routes with `php artisan route:list`. Filter with: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`.
- Read configuration values using dot notation: `php artisan config:show app.name`, `php artisan config:show database.default`. Or read config files directly from the `config/` directory.

## Tinker

- Execute PHP in app context for debugging and testing code. Do not create models without user approval, prefer tests with factories instead. Prefer existing Artisan commands over custom tinker code.
- Always use single quotes to prevent shell expansion: `php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `php artisan tinker --execute 'User::where("active", true)->count();'`

=== php rules ===

# PHP

- Always use curly braces for control structures, even for single-line bodies.
- Use PHP 8 constructor property promotion: `public function __construct(public GitHub $github) { }`. Do not leave empty zero-parameter `__construct()` methods unless the constructor is private.
- Use explicit return type declarations and type hints for all method parameters: `function isAccessible(User $user, ?string $path = null): bool`
- Use TitleCase for Enum keys: `FavoritePerson`, `BestLake`, `Monthly`.
- Prefer PHPDoc blocks over inline comments. Only add inline comments for exceptionally complex logic.
- Use array shape type definitions in PHPDoc blocks.

=== deployments rules ===

# Deployment

- Laravel can be deployed using [Laravel Cloud](https://cloud.laravel.com/), which is the fastest way to deploy and scale production Laravel applications.

=== laravel/core rules ===

# Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using `php artisan list` and check their parameters with `php artisan [command] --help`.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Model Creation

- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `php artisan make:model --help` to check the available options.

## APIs & Eloquent Resources

- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

## URL Generation

- When generating links to other pages, prefer named routes and the `route()` function.

## Testing

- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

## Vite Error

- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== pint/core rules ===

# Laravel Pint Code Formatter

- If you have modified any PHP files, you must run `vendor/bin/pint --dirty --format agent` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test --format agent`, simply run `vendor/bin/pint --format agent` to fix any formatting issues.

=== pest/core rules ===

## Pest

- This project uses Pest for testing. Create tests: `php artisan make:test --pest {name}`.
- The `{name}` argument should not include the test suite directory. Use `php artisan make:test --pest SomeFeatureTest` instead of `php artisan make:test --pest Feature/SomeFeatureTest`.
- Run tests: `php artisan test --compact` or filter: `php artisan test --compact --filter=testName`.
- Do NOT delete tests without approval.

</laravel-boost-guidelines>
