---
paths:
  - 'app/Http/Requests/*.php'
---

# Requests

## Photo validation must NOT live in StoreVerificationRequest
Per PRD §5.6, jika validasi foto gagal, verifikasi TETAP tersimpan dengan photo_status=gagal. Karena itu rule foto (image/mimes/max:5120) divalidasi manual di PhotoService, bukan di FormRequest — FormRequest yang gagal akan membatalkan seluruh submit. Jangan pindahkan validasi foto ke StoreVerificationRequest.
