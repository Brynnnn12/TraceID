<?php

namespace App\Http\Controllers;

use App\Enums\CaseStatus;
use App\Exceptions\VerificationLinkException;
use App\Http\Requests\StoreVerificationRequest;
use App\Models\Verification;
use App\Services\VerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VerificationController extends Controller
{
    public function __construct(private readonly VerificationService $verificationService) {}

    public function show(string $token)
    {
        try {
            $case = $this->verificationService->resolveToken($token);
        } catch (VerificationLinkException $e) {
            return view('verification.error', ['message' => $e->getMessage()]);
        }

        if ($case->status === CaseStatus::Terverifikasi) {
            return view('verification.already-verified', ['case' => $case]);
        }

        return view('verification.show', ['case' => $case]);
    }

    public function store(StoreVerificationRequest $request, string $token)
    {
        try {
            $case = $this->verificationService->resolveToken($token);
        } catch (VerificationLinkException $e) {
            return view('verification.error', ['message' => $e->getMessage()]);
        }

        if ($case->status === CaseStatus::Terverifikasi) {
            return view('verification.already-verified', ['case' => $case]);
        }

        $verification = $this->verificationService->recordVerification($case, $request->validated(), $request);

        return view('verification.success', ['case' => $case, 'verification' => $verification]);
    }

    public function photo(Request $request, Verification $verification)
    {
        $paths = $verification->photo_paths ?? [];
        $photoIndex = (int) $request->query('photo', 0);

        abort_if($paths === [] || ! array_key_exists($photoIndex, $paths), 404);

        return Storage::disk('private')->response($paths[$photoIndex]);
    }
}
