<?php

use App\Enums\ActivityType;
use App\Models\ActivityLog;
use App\Models\SocialMedia;
use App\Models\User;

test('guests are redirected to login when accessing the config page', function () {
    $this->get(route('social-media.edit'))->assertRedirect('/login');
});

test('admin can view the social media config page', function () {
    SocialMedia::factory()->configured()->create();

    $this->actingAs(User::factory()->create())
        ->get(route('social-media.edit'))
        ->assertOk()
        ->assertSee('Konfigurasi Social Media')
        ->assertSee('Salin Link');
});

test('admin can update the social media configuration', function () {
    SocialMedia::factory()->create();

    $this->actingAs(User::factory()->create())
        ->put(route('social-media.update'), [
            'platform' => 'Instagram',
            'username' => 'budi_santoso',
            'profile_url' => 'https://instagram.com/budi_santoso',
            'caption' => 'Follow akun di atas',
            'status' => 'aktif',
        ])
        ->assertRedirect(route('social-media.edit'))
        ->assertSessionHas('status');

    $socialMedia = SocialMedia::first();

    expect($socialMedia->platform)->toBe('Instagram')
        ->and($socialMedia->username)->toBe('budi_santoso')
        ->and($socialMedia->profile_url)->toBe('https://instagram.com/budi_santoso')
        ->and($socialMedia->status->value)->toBe('aktif');
});

test('updating the config records an activity log', function () {
    SocialMedia::factory()->create();

    $this->actingAs(User::factory()->create())
        ->put(route('social-media.update'), [
            'platform' => 'Instagram',
            'status' => 'aktif',
        ]);

    expect(ActivityLog::where('activity', ActivityType::KonfigurasiSocialMediaDiperbarui)->count())->toBe(1);
});

test('an invalid status is rejected', function () {
    SocialMedia::factory()->create();

    $this->actingAs(User::factory()->create())
        ->put(route('social-media.update'), ['status' => 'buka'])
        ->assertSessionHasErrors(['status']);
});
