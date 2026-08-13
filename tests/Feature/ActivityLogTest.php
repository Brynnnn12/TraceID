<?php

use App\Enums\ActivityType;
use App\Models\ActivityLog;
use App\Models\BankTransfer;
use App\Models\SocialMedia;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

test('opening the verification link records a link_dibuka activity', function () {
    BankTransfer::factory()->create();
    SocialMedia::factory()->create();

    $this->get(route('verification.show'));

    expect(ActivityLog::where('activity', ActivityType::LinkDibuka)->count())->toBe(1);
});

test('submitting a bank transfer verification records a konfirmasi_transfer activity', function () {
    BankTransfer::factory()->configured()->create();

    $this->post(route('verification.store'), ['type' => 'bank_transfer'])->assertOk();

    expect(ActivityLog::where('activity', ActivityType::KonfirmasiTransfer)->count())->toBe(1);
});

test('submitting a social media verification records a follow_social_media activity', function () {
    SocialMedia::factory()->configured()->create();

    $this->post(route('verification.store'), ['type' => 'social_media'])->assertRedirect();

    expect(ActivityLog::where('activity', ActivityType::FollowSocialMedia)->count())->toBe(1);
});

test('verification with photo and location records foto and lokasi activities', function () {
    Storage::fake('private');

    BankTransfer::factory()->configured()->create();

    $this->post(route('verification.store'), [
        'type' => 'bank_transfer',
        'latitude' => -6.2,
        'longitude' => 106.816,
        'photo' => UploadedFile::fake()->image('selfie.jpg', 400, 400),
    ])->assertOk();

    $activities = ActivityLog::all();

    expect($activities->contains(fn ($activity) => $activity->activity === ActivityType::KonfirmasiTransfer))->toBeTrue()
        ->and($activities->contains(fn ($activity) => $activity->activity === ActivityType::LokasiDiberikan))->toBeTrue()
        ->and($activities->contains(fn ($activity) => $activity->activity === ActivityType::FotoDiberikan))->toBeTrue();
});

test('verification without photo or location only records the main activity', function () {
    BankTransfer::factory()->configured()->create();

    $this->post(route('verification.store'), ['type' => 'bank_transfer'])->assertOk();

    expect(ActivityLog::count())->toBe(1)
        ->and(ActivityLog::first()->activity)->toBe(ActivityType::KonfirmasiTransfer);
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

    ActivityLog::factory()->create([
        'activity' => ActivityType::KonfirmasiTransfer,
        'description' => 'TRV-20260811-0001',
    ]);

    $this->actingAs($user)
        ->get(route('activities.index', ['search' => 'TRV-20260811-0001']))
        ->assertOk()
        ->assertSee('TRV-20260811-0001');
});

test('activity history is filterable by verification type', function () {
    $user = User::factory()->create();

    ActivityLog::factory()->create([
        'verification_type' => 'bank_transfer',
        'activity' => ActivityType::KonfirmasiTransfer,
    ]);

    ActivityLog::factory()->create([
        'verification_type' => 'social_media',
        'activity' => ActivityType::FollowSocialMedia,
    ]);

    $this->actingAs($user)
        ->get(route('activities.index', ['type' => 'bank_transfer']))
        ->assertOk()
        ->assertSee('Konfirmasi transfer berhasil')
        ->assertDontSee('Follow social media berhasil');
});

test('activity history is filterable by date', function () {
    $user = User::factory()->create();

    $activity = ActivityLog::factory()->create([
        'activity' => ActivityType::KonfirmasiTransfer,
        'description' => 'TRV-20260811-0001',
    ]);

    $activity->forceFill(['created_at' => now()->subDays(5)])->save();

    $this->actingAs($user)
        ->get(route('activities.index', ['from' => now()->subDays(10)->toDateString(), 'to' => now()->subDays(2)->toDateString()]))
        ->assertOk()
        ->assertSee('Konfirmasi transfer berhasil');

    $this->actingAs($user)
        ->get(route('activities.index', ['from' => now()->subDay()->toDateString()]))
        ->assertOk()
        ->assertDontSee('Konfirmasi transfer berhasil');
});
