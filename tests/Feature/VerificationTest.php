<?php

use App\Enums\CaseStatus;
use App\Models\CaseFile;

test('visitor can open a valid verification link', function () {
    $case = CaseFile::factory()->create();

    $this->get(route('verification.show', $case->token))
        ->assertOk()
        ->assertSee($case->target_name)
        ->assertSee($case->reference_number);
});

test('opening a valid link marks the case as link_dibuka', function () {
    $case = CaseFile::factory()->create(['status' => CaseStatus::Aktif]);

    $this->get(route('verification.show', $case->token));

    expect($case->fresh()->status)->toBe(CaseStatus::LinkDibuka);
});

test('revisiting an open link keeps the link_dibuka status', function () {
    $case = CaseFile::factory()->create(['status' => CaseStatus::LinkDibuka]);

    $this->get(route('verification.show', $case->token))
        ->assertOk();

    expect($case->fresh()->status)->toBe(CaseStatus::LinkDibuka);
});

test('invalid token shows an error page', function () {
    $this->get('/verify/nonexistent-token')
        ->assertOk()
        ->assertSee('Link verifikasi tidak valid.');
});

test('expired token shows an expired message', function () {
    $case = CaseFile::factory()->create(['expires_at' => now()->subMinute()]);

    $this->get(route('verification.show', $case->token))
        ->assertOk()
        ->assertSee('Link verifikasi sudah kedaluwarsa. Hubungi pengirim untuk link baru.');
});

test('closed case link shows an inactive message', function () {
    $case = CaseFile::factory()->create(['status' => CaseStatus::Ditutup]);

    $this->get(route('verification.show', $case->token))
        ->assertOk()
        ->assertSee('Link ini sudah tidak aktif.');
});

test('already verified link shows a read-only summary', function () {
    $case = CaseFile::factory()->create(['status' => CaseStatus::Terverifikasi]);

    $this->get(route('verification.show', $case->token))
        ->assertOk()
        ->assertSee('Transaksi Sudah Diverifikasi')
        ->assertSee($case->reference_number)
        ->assertDontSee('Nama Pengirim');
});
