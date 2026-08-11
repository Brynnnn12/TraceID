<?php

use App\Enums\VerificationType;
use App\Models\User;
use App\Models\Verification;

test('guests are redirected to login when accessing the verification pages', function () {
    $this->get(route('verifications.index'))->assertRedirect('/login');

    $verification = Verification::factory()->create();

    $this->get(route('verifications.show', $verification))->assertRedirect('/login');
});

test('admin can list verifications with their reference numbers', function () {
    $verification = Verification::factory()->asBankTransfer()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('verifications.index'))
        ->assertOk()
        ->assertSee($verification->reference_number)
        ->assertSee('Bank Transfer');
});

test('verification list can be filtered by type', function () {
    Verification::factory()->asBankTransfer()->create();
    $socialMedia = Verification::factory()->asSocialMedia()->create();

    $response = $this->actingAs(User::factory()->create())
        ->get(route('verifications.index', ['type' => VerificationType::SocialMedia->value]))
        ->assertOk()
        ->assertSee($socialMedia->reference_number);

    expect($response->viewData('verifications'))
        ->toHaveCount(1)
        ->and($response->viewData('verifications')->first()->verification_type)->toBe(VerificationType::SocialMedia);
});

test('admin can view verification details with metadata', function () {
    $verification = Verification::factory()->asBankTransfer()->withLocation()->create([
        'ip_address' => '10.0.0.1',
        'browser' => 'Chrome',
        'operating_system' => 'Windows',
        'device_type' => 'desktop',
    ]);

    $this->actingAs(User::factory()->create())
        ->get(route('verifications.show', $verification))
        ->assertOk()
        ->assertSee($verification->reference_number)
        ->assertSee('10.0.0.1')
        ->assertSee('Chrome')
        ->assertSee('Windows')
        ->assertSee('desktop');
});

test('verification details include photo previews when photos exist', function () {
    $verification = Verification::factory()->asBankTransfer()->withPhoto()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('verifications.show', $verification))
        ->assertOk()
        ->assertSee('Foto');
});
