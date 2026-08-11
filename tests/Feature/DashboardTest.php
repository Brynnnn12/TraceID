<?php

use App\Enums\ActivityType;
use App\Models\ActivityLog;
use App\Models\BankTransfer;
use App\Models\User;
use App\Models\Verification;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('guests are redirected to login when accessing the dashboard', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

test('dashboard shows the statistics widgets', function () {
    Verification::factory()->asBankTransfer()->count(2)->create();
    Verification::factory()->asSocialMedia()->create();
    Verification::factory()->asBankTransfer()->withLocation()->create();
    Verification::factory()->asBankTransfer()->withPhoto()->at(now()->subDay())->create();
    BankTransfer::factory()->configured()->create();

    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertViewHas('statistics', function (array $statistics) {
            return $statistics['total_verifications'] === 5
                && $statistics['bank_transfer_verifications'] === 4
                && $statistics['social_media_verifications'] === 1
                && $statistics['verifications_today'] === 4
                && $statistics['locations_recorded'] === 1
                && $statistics['photos_recorded'] === 1
                && $statistics['active_configurations'] === 1;
        });
});

test('dashboard provides chart data for the last 7 days', function () {
    Verification::factory()->count(2)->create(['created_at' => now()]);
    Verification::factory()->create(['created_at' => now()->subDays(3)]);

    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertViewHas('verificationsChart', function (array $chart) {
            return count($chart['labels']) === 7
                && count($chart['data']) === 7
                && array_sum($chart['data']) === 3
                && $chart['data'][6] === 2
                && $chart['data'][3] === 1;
        });
});

test('dashboard provides verification data grouped by type', function () {
    Verification::factory()->asBankTransfer()->count(2)->create();
    Verification::factory()->asSocialMedia()->create();

    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertViewHas('typeChart', function (array $chart) {
            return $chart['labels'] === ['Bank Transfer', 'Social Media']
                && $chart['data'] === [2, 1];
        });
});

test('dashboard lists the most recent activities', function () {
    ActivityLog::factory()->create([
        'verification_type' => 'bank_transfer',
        'activity' => ActivityType::KonfirmasiTransfer,
        'description' => 'TRV-20260811-0001',
    ]);

    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Konfirmasi transfer berhasil');
});
