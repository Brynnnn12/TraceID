<?php

use App\Enums\ActivityType;
use App\Models\ActivityLog;
use App\Models\BankTransfer;
use App\Models\User;

test('guests are redirected to login when accessing the config page', function () {
    $this->get(route('bank-transfer.edit'))->assertRedirect('/login');
});

test('admin can view the bank transfer config page', function () {
    BankTransfer::factory()->configured()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('bank-transfer.edit'))
        ->assertOk()
        ->assertSee('Konfigurasi Bank Transfer')
        ->assertSee('Salin Link');
});

test('admin can update the bank transfer configuration', function () {
    BankTransfer::factory()->create();

    $this->actingAs(User::factory()->create())
        ->put(route('bank-transfer.update'), [
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'amount' => 100000,
            'notes' => 'Transfer tunai',
            'status' => 'aktif',
        ])
        ->assertRedirect(route('bank-transfer.edit'))
        ->assertSessionHas('status');

    $bankTransfer = BankTransfer::first();

    expect($bankTransfer->bank_name)->toBe('BCA')
        ->and($bankTransfer->account_number)->toBe('1234567890')
        ->and($bankTransfer->amount)->toBe('100000.00')
        ->and($bankTransfer->status->value)->toBe('aktif');
});

test('updating the config records an activity log', function () {
    BankTransfer::factory()->create();

    $this->actingAs(User::factory()->create())
        ->put(route('bank-transfer.update'), [
            'bank_name' => 'BCA',
            'status' => 'aktif',
        ]);

    expect(ActivityLog::where('activity', ActivityType::KonfigurasiBankTransferDiperbarui)->count())->toBe(1);
});

test('an invalid status is rejected', function () {
    BankTransfer::factory()->create();

    $this->actingAs(User::factory()->create())
        ->put(route('bank-transfer.update'), ['status' => 'buka'])
        ->assertSessionHasErrors(['status']);
});
