<?php

namespace App\Services;

use App\Enums\VerificationType;
use App\Models\BankTransfer;
use App\Models\SocialMedia;
use App\Models\Verification;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPDF;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

class ReportService
{
    public function generate(?string $from = null, ?string $to = null, ?string $type = null): DomPDF
    {
        $verifications = Verification::query()
            ->when(filled($type), function (Builder $query) use ($type) {
                $query->where('verification_type', $type);
            })
            ->when(filled($from), function (Builder $query) use ($from) {
                $query->whereDate('created_at', '>=', $from);
            })
            ->when(filled($to), function (Builder $query) use ($to) {
                $query->whereDate('created_at', '<=', $to);
            })
            ->latest('created_at')
            ->get();

        return Pdf::loadView('reports.pdf', [
            'bankTransfer' => BankTransfer::first(),
            'socialMedia' => SocialMedia::first(),
            'verifications' => $verifications->map(function (Verification $verification) {
                return [
                    'verification' => $verification,
                    'photos' => collect($verification->photo_paths ?? [])
                        ->filter(fn (string $path) => Storage::disk('private')->exists($path))
                        ->values()
                        ->map(fn (string $path) => [
                            'data' => base64_encode((string) Storage::disk('private')->get($path)),
                            'mime' => Storage::disk('private')->mimeType($path) ?? 'image/jpeg',
                            'time' => $verification->created_at->format('d M Y H:i'),
                        ]),
                ];
            }),
            'generatedAt' => now(),
            'from' => $from,
            'to' => $to,
            'type' => $type !== null ? VerificationType::tryFrom($type)?->label() : null,
        ]);
    }
}
