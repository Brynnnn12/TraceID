<?php

use App\Enums\ActivityType;
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
