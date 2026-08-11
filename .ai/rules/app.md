---
paths:
  - 'app/**'
---

# App

## Cases/templates removed; singleton config model
Konsep case & verification template sudah dihapus total (PRD changelog). Tidak ada case_id, token, expires_at. Verifikasi publik satu link /verify. BankTransfer & SocialMedia adalah singleton (1 baris, data kosong via seeder, diisi admin). Jangan reintroduksi kolom case_id/token atau model CaseFile/VerificationTemplate.
