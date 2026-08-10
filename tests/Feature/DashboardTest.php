<?php

use App\Enums\ActivityType;
use App\Enums\CaseStatus;
use App\Enums\LocationStatus;
use App\Enums\PhotoStatus;
use App\Models\ActivityLog;
use App\Models\CaseFile;
use App\Models\User;
use App\Models\Verification;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('guests are redirected to login when accessing the dashboard', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

test('dashboard shows the statistics widgets', function () {
    $case = CaseFile::factory()->create();
    CaseFile::factory()->count(2)->create();

    Verification::factory()->create(['case_id' => $case->id, 'location_status' => LocationStatus::Diberikan]);
    Verification::factory()->create(['case_id' => $case->id, 'location_status' => LocationStatus::Diberikan]);
    Verification::factory()->create(['case_id' => $case->id, 'photo_status' => PhotoStatus::Diberikan]);
    Verification::factory()->create(['case_id' => $case->id, 'created_at' => now()->subDay()]);

    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertViewHas('statistics', function (array $statistics) {
            return $statistics['total_cases'] === 3
                && $statistics['total_verifications'] === 4
                && $statistics['verifications_today'] === 3
                && $statistics['locations_recorded'] === 2
                && $statistics['photos_recorded'] === 1;
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

test('dashboard provides case data grouped by status', function () {
    CaseFile::factory()->create(['status' => CaseStatus::Aktif]);
    CaseFile::factory()->create(['status' => CaseStatus::Aktif]);
    CaseFile::factory()->create(['status' => CaseStatus::Terverifikasi]);

    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertViewHas('statusChart', function (array $chart) {
            return $chart['labels'] === ['Aktif', 'Link dibuka', 'Terverifikasi', 'Ditutup']
                && $chart['data'] === [2, 0, 1, 0];
        });
});

test('dashboard lists the most recent activities', function () {
    $case = CaseFile::factory()->create();

    ActivityLog::factory()->create([
        'case_id' => $case->id,
        'activity' => ActivityType::VerifikasiSelesai,
    ]);

    $this->actingAs($this->user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Verifikasi selesai')
        ->assertSee($case->reference_number);
});
