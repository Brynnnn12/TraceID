<?php

use App\Models\BankTransfer;
use App\Models\SocialMedia;
use App\Models\User;
use App\Models\Verification;

test('guests are redirected to login when accessing the reports page', function () {
    $this->get(route('reports.index'))->assertRedirect('/login');
});

test('admin can view the reports page', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('reports.index'))
        ->assertOk()
        ->assertSee('Laporan Verifikasi')
        ->assertSee('Download PDF');
});

test('admin can download the report as pdf', function () {
    BankTransfer::factory()->configured()->create();
    SocialMedia::factory()->configured()->create();
    Verification::factory()->asBankTransfer()->count(2)->create();
    Verification::factory()->asSocialMedia()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('reports.download'))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});

test('report download can be filtered by type and period', function () {
    Verification::factory()->asBankTransfer()->create(['created_at' => now()->subDays(5)]);
    Verification::factory()->asSocialMedia()->create(['created_at' => now()->subDays(30)]);

    $this->actingAs(User::factory()->create())
        ->get(route('reports.download', [
            'type' => 'bank_transfer',
            'from' => now()->subDays(10)->toDateString(),
            'to' => now()->toDateString(),
        ]))
        ->assertOk()
        ->assertHeader('content-type', 'application/pdf');
});

test('an invalid report type is rejected', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('reports.download', ['type' => 'kripto']))
        ->assertSessionHasErrors(['type']);
});
