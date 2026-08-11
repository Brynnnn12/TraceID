<?php

use App\Enums\ActivityType;
use App\Enums\CaseStatus;
use App\Models\CaseFile;
use App\Models\User;
use App\Models\VerificationTemplate;
use App\Services\CaseService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('creating a case records a link_dibuat activity', function () {
    $template = VerificationTemplate::factory()->create();

    $case = app(CaseService::class)->create([
        'template_id' => $template->id,
        'fields' => [
            'target_name' => 'Budi Santoso',
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'amount' => 100000,
        ],
    ]);

    expect($case->activities()->where('activity', ActivityType::LinkDibuat)->count())->toBe(1);
});

test('opening a verification link records a link_dibuka activity', function () {
    $case = CaseFile::factory()->create();

    $this->get(route('verification.show', $case->token));

    expect($case->fresh()->activities()->where('activity', ActivityType::LinkDibuka)->count())->toBe(1);
});

test('revisiting an open link does not duplicate the link_dibuka activity', function () {
    $case = CaseFile::factory()->create();

    $this->get(route('verification.show', $case->token));
    $this->get(route('verification.show', $case->token));

    expect($case->fresh()->activities()->where('activity', ActivityType::LinkDibuka)->count())->toBe(1);
});

test('verifying a case records verifikasi_selesai, lokasi, and foto activities', function () {
    Storage::fake('private');

    $case = CaseFile::factory()->create();

    $this->post(route('verification.store', $case->token), [
        'latitude' => -6.2,
        'longitude' => 106.816,
        'photo' => UploadedFile::fake()->image('selfie.jpg', 400, 400),
    ])->assertOk();

    $activities = $case->fresh()->activities;

    expect($activities->contains(fn ($activity) => $activity->activity === ActivityType::VerifikasiSelesai))->toBeTrue()
        ->and($activities->contains(fn ($activity) => $activity->activity === ActivityType::LokasiDiberikan))->toBeTrue()
        ->and($activities->contains(fn ($activity) => $activity->activity === ActivityType::FotoDiberikan))->toBeTrue();
});

test('verification without photo or location only records verifikasi_selesai', function () {
    $case = CaseFile::factory()->create(['status' => CaseStatus::LinkDibuka]);

    $this->post(route('verification.store', $case->token))
        ->assertOk();

    $activities = $case->fresh()->activities;

    expect($activities->count())->toBe(1)
        ->and($activities->first()->activity)->toBe(ActivityType::VerifikasiSelesai);
});

test('admin can view the activity history page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('activities.index'))
        ->assertOk()
        ->assertSee('Riwayat Aktivitas');
});

test('activity history is searchable by reference number', function () {
    $user = User::factory()->create();
    $case = CaseFile::factory()->create();
    $case->activities()->create(['activity' => ActivityType::LinkDibuat]);

    $this->actingAs($user)
        ->get(route('activities.index', ['search' => $case->reference_number]))
        ->assertOk()
        ->assertSee($case->reference_number);
});

test('activity history is filterable by date', function () {
    $user = User::factory()->create();
    $case = CaseFile::factory()->create();
    $activity = $case->activities()->create([
        'activity' => ActivityType::LinkDibuat,
    ]);
    $activity->forceFill(['created_at' => now()->subDays(5)])->save();

    $this->actingAs($user)
        ->get(route('activities.index', ['from' => now()->subDays(10)->toDateString(), 'to' => now()->subDays(2)->toDateString()]))
        ->assertOk()
        ->assertSee($case->reference_number);

    $this->actingAs($user)
        ->get(route('activities.index', ['from' => now()->subDay()->toDateString()]))
        ->assertOk()
        ->assertDontSee($case->reference_number);
});
