<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Verifikasi</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111827;
        }
        h1 {
            font-size: 18px;
            margin: 0 0 4px;
        }
        .meta {
            color: #6b7280;
            font-size: 10px;
            margin-bottom: 16px;
        }
        h2 {
            font-size: 13px;
            margin: 20px 0 8px;
            border-bottom: 1px solid #d1d5db;
            padding-bottom: 4px;
        }
        h3 {
            font-size: 11px;
            margin: 0 0 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 5px 7px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background: #f3f4f6;
            font-weight: bold;
        }
        .summary-table td {
            border: none;
            border-bottom: 1px solid #e5e7eb;
            padding: 3px 0;
        }
        .empty {
            color: #6b7280;
            font-style: italic;
        }
        .verification {
            page-break-inside: avoid;
            border: 1px solid #d1d5db;
            padding: 8px;
            margin-bottom: 10px;
        }
        .meta-line {
            font-size: 9px;
            color: #4b5563;
            margin: 2px 0;
        }
        .photos {
            margin-top: 6px;
        }
        .photo {
            position: relative;
            display: inline-block;
            border: 1px solid #d1d5db;
            margin: 0 6px 6px 0;
            padding: 3px;
        }
        .photo img {
            display: block;
        }
        .stamp {
            position: absolute;
            bottom: 3px;
            left: 3px;
            background: #1f2937;
            color: #ffffff;
            font-size: 8px;
            padding: 2px 4px;
        }
    </style>
</head>
<body>
    <h1>Laporan Verifikasi Bukti Digital</h1>
    <div class="meta">
        Dibuat pada {{ $generatedAt->format('d M Y H:i') }}<br>
        Periode: {{ $type ?? 'Semua jenis' }}
        @if (filled($from) || filled($to))
            ({{ $from ?? 'awal' }} s.d. {{ $to ?? 'sekarang' }})
        @else
            (seluruh data)
        @endif
    </div>

    <h2>Konfigurasi Bank Transfer</h2>
    <table class="summary-table">
        <tr>
            <td style="width:140px;">Nama Bank</td>
            <td>: {{ $bankTransfer?->bank_name ?? 'Belum diisi' }}</td>
        </tr>
        <tr>
            <td>Nomor Rekening</td>
            <td>: {{ $bankTransfer?->account_number ?? 'Belum diisi' }}</td>
        </tr>
        <tr>
            <td>Jumlah Transfer</td>
            <td>: {{ $bankTransfer?->amount ? 'Rp '.number_format((float) $bankTransfer->amount, 0, ',', '.') : 'Belum diisi' }}</td>
        </tr>
        <tr>
            <td>Status</td>
            <td>: {{ $bankTransfer?->status?->label() ?? '-' }}</td>
        </tr>
    </table>

    <h2>Konfigurasi Social Media</h2>
    <table class="summary-table">
        <tr>
            <td style="width:140px;">Platform</td>
            <td>: {{ $socialMedia?->platform ?? 'Belum diisi' }}</td>
        </tr>
        <tr>
            <td>Username</td>
            <td>: {{ $socialMedia?->username ?? 'Belum diisi' }}</td>
        </tr>
        <tr>
            <td>URL Profil</td>
            <td>: {{ $socialMedia?->profile_url ?? 'Belum diisi' }}</td>
        </tr>
        <tr>
            <td>Status</td>
            <td>: {{ $socialMedia?->status?->label() ?? '-' }}</td>
        </tr>
    </table>

    <h2>Daftar Verifikasi ({{ $verifications->count() }})</h2>
    @if ($verifications->isEmpty())
        <p class="empty">Belum ada verifikasi pada periode ini.</p>
    @else
        @foreach ($verifications as $item)
            @php $v = $item['verification']; @endphp
            <div class="verification">
                <h3>{{ $v->reference_number }} &middot; {{ $v->verification_type->label() }} &middot; {{ $v->created_at->format('d M Y H:i') }}</h3>
                <p class="meta-line">
                    IP: {{ $v->ip_address ?? '—' }} &middot; Browser: {{ $v->browser ?? '—' }}
                    &middot; OS: {{ $v->operating_system ?? '—' }} &middot; Perangkat: {{ $v->device_type ?? '—' }}
                    &middot; Bahasa: {{ $v->language ?? '—' }} &middot; Timezone: {{ $v->timezone ?? '—' }}
                    &middot; Resolusi: {{ $v->screen_resolution ?? '—' }}
                </p>
                @if ($v->hasCoordinates())
                    <p class="meta-line">
                        Lokasi: {{ $v->latitude }}, {{ $v->longitude }} (±{{ $v->accuracy ?? '?' }} m)
                        &middot; <a href="https://www.google.com/maps/search/?api=1&query={{ $v->latitude }},{{ $v->longitude }}">Buka di Google Maps</a>
                    </p>
                @else
                    <p class="meta-line">Lokasi: tidak ada</p>
                @endif
                @if ($item['photos']->isNotEmpty())
                    <div class="photos">
                        @foreach ($item['photos'] as $photo)
                            <div class="photo">
                                <img src="data:{{ $photo['mime'] }};base64,{{ $photo['data'] }}" width="180">
                                <span class="stamp">{{ $photo['time'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="meta-line">Foto: tidak ada</p>
                @endif
            </div>
        @endforeach
    @endif
</body>
</html>
