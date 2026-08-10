---
paths:
  - 'resources/views/cases/**.blade.php'
---

# Cases

## Leaflet map rendered via leafletMap Alpine component
Peta lokasi verifikasi dirender lewat Alpine component `leafletMap(lat, lng)` yang didaftarkan di resources/js/leaflet-map.js (di-import dari app.js). Teks popup (alamat reverse-geocode Nominatim, link Google Maps) dibuat client-side di JS, jadi feature test harus assert pada output `x-data="leafletMap(...)"`, bukan teks popup yang tidak muncul di HTML. Kontainer peta harus punya tinggi (mis. h-56) dan class z-0 agar z-index Leaflet tidak menabrak layout.
