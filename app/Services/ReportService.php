<?php

namespace App\Services;

use App\Enums\VerificationType;
use App\Models\BankTransfer;
use App\Models\SocialMedia;
use App\Models\Verification;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF;
use Illuminate\Database\Eloquent\Builder;

class ReportService
{
    public function generate(?string $from = null, ?string $to = null, ?string $type = null): PDF
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
            'verifications' => $verifications,
            'generatedAt' => now(),
            'type' => $type !== null ? VerificationType::tryFrom($type)?->label() : null,
        ]);
    }
}
