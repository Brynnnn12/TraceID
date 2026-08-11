<?php

namespace App\Http\Controllers;

use App\Enums\VerificationType;
use App\Http\Requests\StoreVerificationRequest;
use App\Models\BankTransfer;
use App\Models\SocialMedia;
use App\Models\Verification;
use App\Services\VerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VerificationController extends Controller
{
    public function __construct(private readonly VerificationService $verificationService) {}

    public function show(Request $request)
    {
        if (! $this->verificationService->isLinkActive()) {
            return view('verification.error', ['message' => 'Link ini sudah tidak aktif.']);
        }

        $this->verificationService->recordLinkOpened($request);

        return view('verification.show', [
            'bankTransfer' => BankTransfer::first(),
            'socialMedia' => SocialMedia::first(),
        ]);
    }

    public function store(StoreVerificationRequest $request)
    {
        $type = VerificationType::from($request->validated('type'));

        if (! $this->verificationService->isSectionActive($type)) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Link ini sudah tidak aktif.'], 410);
            }

            return view('verification.error', ['message' => 'Link ini sudah tidak aktif.']);
        }

        $verification = $this->verificationService->recordVerification($type, $request->validated(), $request);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Verifikasi berhasil']);
        }

        return view('verification.success', compact('verification'));
    }

    public function photo(Request $request, Verification $verification)
    {
        $paths = $verification->photo_paths ?? [];
        $photoIndex = (int) $request->query('photo', 0);

        abort_if($paths === [] || ! array_key_exists($photoIndex, $paths), 404);

        return Storage::disk('private')->response($paths[$photoIndex]);
    }
}
