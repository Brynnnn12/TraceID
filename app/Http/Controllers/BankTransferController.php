<?php

namespace App\Http\Controllers;

use App\Enums\ActivityType;
use App\Http\Requests\UpdateBankTransferRequest;
use App\Models\BankTransfer;
use App\Services\ActivityService;

class BankTransferController extends Controller
{
    public function __construct(private readonly ActivityService $activityService) {}

    public function edit()
    {
        return view('bank-transfers.edit', [
            'bankTransfer' => BankTransfer::first(),
        ]);
    }

    public function update(UpdateBankTransferRequest $request)
    {
        $bankTransfer = BankTransfer::query()->firstOrCreate([]);
        $bankTransfer->update($request->validated());

        $this->activityService->record(null, ActivityType::KonfigurasiBankTransferDiperbarui);

        return redirect()
            ->route('bank-transfer.edit')
            ->with('status', 'Konfigurasi bank transfer berhasil diperbarui.');
    }
}
