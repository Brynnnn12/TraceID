<?php

use App\Enums\ActivityType;
use App\Models\ActivityLog;
use App\Models\BankTransfer;
use App\Models\SocialMedia;

test('the root path redirects to the public verification page', function () {
    $this->get('/')->assertRedirect(route('verification.show'));
});

test('visitor can open the public verification page with active configurations', function () {
    BankTransfer::factory()->configured()->create();
    SocialMedia::factory()->configured()->create();

    $this->get(route('verification.show'))
        ->assertOk()
        ->assertSee('Bank Transfer')
        ->assertSee('Verifikasi Bank Transfer')
        ->assertSee('Social Media')
        ->assertSee('Verifikasi Social Media');
});

test('visitor can open the bank transfer verification page', function () {
    BankTransfer::factory()->configured()->create();

    $this->get(route('verification.bank-transfer'))
        ->assertOk()
        ->assertSee('Bank Transfer')
        ->assertSee('Konfirmasi');
});

test('visitor can open the social media verification page', function () {
    SocialMedia::factory()->configured()->create();

    $this->get(route('verification.social-media'))
        ->assertOk()
        ->assertSee('Social Media')
        ->assertSee('Follow');
});

test('an active but incomplete configuration shows the unavailable message', function () {
    BankTransfer::factory()->create();
    SocialMedia::factory()->create();

    $this->get(route('verification.show'))
        ->assertOk()
        ->assertSee('Informasi belum tersedia. Hubungi pengirim.');
});

test('a closed configuration section is hidden from the public page', function () {
    BankTransfer::factory()->configured()->ditutup()->create();
    SocialMedia::factory()->configured()->create();

    $this->get(route('verification.show'))
        ->assertRedirect(route('verification.social-media'));

    $this->get(route('verification.social-media'))
        ->assertOk()
        ->assertDontSee('Konfirmasi')
        ->assertSee('Follow');
});

test('opening the link records a link_dibuka activity once per visitor', function () {
    BankTransfer::factory()->create();
    SocialMedia::factory()->create();

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.10'])->get(route('verification.show'));
    $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.10'])->get(route('verification.show'));

    expect(ActivityLog::where('activity', ActivityType::LinkDibuka)->count())->toBe(1);
});

test('the link is inactive when all configurations are closed', function () {
    BankTransfer::factory()->ditutup()->create();
    SocialMedia::factory()->ditutup()->create();

    $this->get(route('verification.show'))
        ->assertOk()
        ->assertSee('Link ini sudah tidak aktif.');
});
