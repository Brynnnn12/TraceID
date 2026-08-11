<?php

namespace App\Http\Controllers;

use App\Enums\VerificationType;
use App\Http\Requests\DownloadReportRequest;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(private readonly ReportService $reportService) {}

    public function index(Request $request)
    {
        return view('reports.index', [
            'filters' => $request->only(['from', 'to', 'type']),
            'types' => VerificationType::cases(),
        ]);
    }

    public function download(DownloadReportRequest $request)
    {
        $validated = $request->validated();

        $pdf = $this->reportService->generate(
            $validated['from'] ?? null,
            $validated['to'] ?? null,
            $validated['type'] ?? null,
        );

        return $pdf->download('laporan-verifikasi-'.now()->format('Ymd-His').'.pdf');
    }
}
