<?php

use App\Enums\LocationStatus;
use App\Enums\PhotoStatus;
use App\Enums\VerificationType;
use App\Models\BankTransfer;
use App\Models\SocialMedia;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

test('visitor can submit a bank transfer verification', function () {
    BankTransfer::factory()->configured()->create();

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.1'])
        ->post(route('verification.store'), [
            'type' => 'bank_transfer',
            'timezone' => 'Asia/Jakarta',
            'screen_resolution' => '1920x1080',
            'latitude' => -6.200000,
            'longitude' => 106.816666,
            'accuracy' => 12.5,
        ])
        ->assertOk()
        ->assertSee('Verifikasi Berhasil');

    $verification = Verification::first();

    expect($verification)->not->toBeNull()
        ->and($verification->verification_type)->toBe(VerificationType::BankTransfer)
        ->and($verification->reference_number)->toMatch('/^TRV-\d{8}-\d{4}$/')
        ->and($verification->timezone)->toBe('Asia/Jakarta')
        ->and($verification->screen_resolution)->toBe('1920x1080')
        ->and($verification->latitude)->toBe('-6.2000000')
        ->and($verification->longitude)->toBe('106.8166660')
        ->and($verification->accuracy)->toBe('12.50')
        ->and($verification->location_status)->toBe(LocationStatus::Diberikan)
        ->and($verification->ip_address)->toBe('10.0.0.1')
        ->and($verification->user_agent)->not->toBeNull();
});

test('visitor can verify with no permission data at all', function () {
    BankTransfer::factory()->configured()->create();

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.7'])
        ->post(route('verification.store'), ['type' => 'bank_transfer'])
        ->assertOk()
        ->assertSee('Verifikasi Berhasil');

    $verification = Verification::first();

    expect($verification)->not->toBeNull()
        ->and($verification->location_status)->toBeNull()
        ->and($verification->photo_status)->toBeNull();
});

test('device metadata is captured from the request', function () {
    BankTransfer::factory()->configured()->create();

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.2'])
        ->withHeader('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36')
        ->withHeader('Accept-Language', 'id-ID,id;q=0.9,en;q=0.8')
        ->post(route('verification.store'), ['type' => 'bank_transfer']);

    $verification = Verification::first();

    expect($verification->browser)->toBe('Chrome')
        ->and($verification->operating_system)->toBe('Windows 10/11')
        ->and($verification->device_type)->toBe('desktop')
        ->and($verification->language)->toBeString()
        ->and(str_starts_with((string) $verification->language, 'id'))->toBeTrue()
        ->and($verification->ip_address)->toBe('10.0.0.2');
});

test('coordinates outside valid ranges are rejected', function () {
    BankTransfer::factory()->configured()->create();

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.3'])
        ->post(route('verification.store'), [
            'type' => 'bank_transfer',
            'latitude' => 95,
        ])
        ->assertSessionHasErrors(['latitude']);
});

test('an invalid verification type is rejected', function () {
    BankTransfer::factory()->configured()->create();

    $this->post(route('verification.store'), ['type' => 'kripto'])
        ->assertSessionHasErrors(['type']);
});

test('verification can be submitted multiple times by different visitors', function () {
    BankTransfer::factory()->configured()->create();

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.11'])
        ->post(route('verification.store'), ['type' => 'bank_transfer'])
        ->assertOk();

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.12'])
        ->post(route('verification.store'), ['type' => 'bank_transfer'])
        ->assertOk();

    expect(Verification::count())->toBe(2);
});

test('a closed section cannot be verified', function () {
    BankTransfer::factory()->ditutup()->create();

    $this->post(route('verification.store'), ['type' => 'bank_transfer'])
        ->assertOk()
        ->assertSee('Link ini sudah tidak aktif.');

    expect(Verification::count())->toBe(0);
});

test('a visitor is redirected to the social media profile after a successful follow', function () {
    SocialMedia::factory()->configured()->create();

    $profileUrl = SocialMedia::first()->profile_url;

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.2.1'])
        ->post(route('verification.store'), ['type' => 'social_media'])
        ->assertRedirect($profileUrl);

    $verification = Verification::first();

    expect($verification)->not->toBeNull()
        ->and($verification->verification_type)->toBe(VerificationType::SocialMedia);
});

test('a social media verification without a profile url shows the success page', function () {
    SocialMedia::factory()->configured()->create(['profile_url' => null]);

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.2.2'])
        ->post(route('verification.store'), ['type' => 'social_media'])
        ->assertOk()
        ->assertSee('Verifikasi Berhasil');
});

test('verification store is rate limited', function () {
    BankTransfer::factory()->configured()->create();

    foreach (range(1, 10) as $ignored) {
        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.6'])
            ->post(route('verification.store'), ['type' => 'bank_transfer'])
            ->assertSuccessful();
    }

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.6'])
        ->post(route('verification.store'), ['type' => 'bank_transfer'])
        ->assertStatus(429);
});

test('visitor can submit a verification with up to three photos', function () {
    Storage::fake('private');

    BankTransfer::factory()->configured()->create();

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.1.1'])
        ->post(route('verification.store'), [
            'type' => 'bank_transfer',
            'photo' => [
                UploadedFile::fake()->image('foto-1.jpg', 400, 400),
                UploadedFile::fake()->image('foto-2.jpg', 400, 400),
                UploadedFile::fake()->image('foto-3.jpg', 400, 400),
            ],
        ])
        ->assertOk();

    $verification = Verification::first();

    expect($verification->photo_status)->toBe(PhotoStatus::Diberikan)
        ->and($verification->photo_paths)->toHaveCount(3);

    foreach ($verification->photo_paths as $path) {
        Storage::disk('private')->assertExists($path);
    }
});

test('only the first three photos are kept', function () {
    Storage::fake('private');

    BankTransfer::factory()->configured()->create();

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.1.5'])
        ->post(route('verification.store'), [
            'type' => 'bank_transfer',
            'photo' => [
                UploadedFile::fake()->image('foto-1.jpg', 400, 400),
                UploadedFile::fake()->image('foto-2.jpg', 400, 400),
                UploadedFile::fake()->image('foto-3.jpg', 400, 400),
                UploadedFile::fake()->image('foto-4.jpg', 400, 400),
            ],
        ])
        ->assertOk();

    $verification = Verification::first();

    expect($verification->photo_status)->toBe(PhotoStatus::Diberikan)
        ->and($verification->photo_paths)->toHaveCount(3);
});

test('an invalid photo does not block the verification', function () {
    BankTransfer::factory()->configured()->create();

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.1.2'])
        ->post(route('verification.store'), [
            'type' => 'bank_transfer',
            'photo' => [UploadedFile::fake()->create('photo.txt', 100)],
        ])
        ->assertOk();

    $verification = Verification::first();

    expect($verification->photo_status)->toBe(PhotoStatus::Gagal)
        ->and($verification->photo_paths)->toBeNull();
});

test('a denied camera permission is recorded as photo_status ditolak', function () {
    BankTransfer::factory()->configured()->create();

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.1.3'])
        ->post(route('verification.store'), [
            'type' => 'bank_transfer',
            'photo_status' => 'ditolak',
        ])
        ->assertOk();

    $verification = Verification::first();

    expect($verification->photo_status)->toBe(PhotoStatus::Ditolak);
});

test('a denied location permission is recorded as location_status ditolak', function () {
    BankTransfer::factory()->configured()->create();

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.1.4'])
        ->post(route('verification.store'), [
            'type' => 'bank_transfer',
            'location_status' => 'ditolak',
        ])
        ->assertOk();

    $verification = Verification::first();

    expect($verification->location_status)->toBe(LocationStatus::Ditolak);
});

test('admin can view the stored verification photo via signed url', function () {
    Storage::fake('private');

    $photo = UploadedFile::fake()->image('foto-1.jpg', 400, 400);
    $path = Storage::disk('private')->putFile('verifications', $photo);

    $verification = Verification::factory()->create(['photo_paths' => [$path]]);

    $this->actingAs(User::factory()->create())
        ->get(URL::signedRoute('verification.photo', ['verification' => $verification->id, 'photo' => 0]))
        ->assertOk();
});

test('verification photo without a stored path is not accessible', function () {
    $verification = Verification::factory()->create();

    $this->actingAs(User::factory()->create())
        ->get(URL::signedRoute('verification.photo', ['verification' => $verification->id]))
        ->assertNotFound();
});

test('an out of range photo index is not accessible', function () {
    Storage::fake('private');

    $photo = UploadedFile::fake()->image('foto-1.jpg', 400, 400);
    $path = Storage::disk('private')->putFile('verifications', $photo);

    $verification = Verification::factory()->create(['photo_paths' => [$path]]);

    $this->actingAs(User::factory()->create())
        ->get(URL::signedRoute('verification.photo', ['verification' => $verification->id, 'photo' => 3]))
        ->assertNotFound();
});
