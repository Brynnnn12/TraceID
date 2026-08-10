<?php

use App\Enums\CaseStatus;
use App\Enums\LocationStatus;
use App\Enums\PhotoStatus;
use App\Models\CaseFile;
use App\Models\User;
use App\Models\Verification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

test('visitor can submit the one-click verification form', function () {
    $case = CaseFile::factory()->create(['status' => CaseStatus::LinkDibuka]);

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.1'])
        ->post(route('verification.store', $case->token), [
            'timezone' => 'Asia/Jakarta',
            'screen_resolution' => '1920x1080',
            'latitude' => -6.200000,
            'longitude' => 106.816666,
            'accuracy' => 12.5,
        ])
        ->assertOk()
        ->assertSee('Verifikasi Berhasil');

    expect($case->fresh()->status)->toBe(CaseStatus::Terverifikasi);

    $verification = $case->fresh()->verifications()->first();

    expect($verification)->not->toBeNull()
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
    $case = CaseFile::factory()->create(['status' => CaseStatus::LinkDibuka]);

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.7'])
        ->post(route('verification.store', $case->token))
        ->assertOk()
        ->assertSee('Verifikasi Berhasil');

    $verification = $case->fresh()->verifications()->first();

    expect($case->fresh()->status)->toBe(CaseStatus::Terverifikasi)
        ->and($verification)->not->toBeNull()
        ->and($verification->location_status)->toBeNull()
        ->and($verification->photo_status)->toBeNull();
});

test('device metadata is captured from the request', function () {
    $case = CaseFile::factory()->create();

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.2'])
        ->withHeader('User-Agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36')
        ->withHeader('Accept-Language', 'id-ID,id;q=0.9,en;q=0.8')
        ->post(route('verification.store', $case->token));

    $verification = $case->fresh()->verifications()->first();

    expect($verification->browser)->toBe('Chrome')
        ->and($verification->operating_system)->toBe('Windows 10/11')
        ->and($verification->device_type)->toBe('desktop')
        ->and($verification->language)->toBe('id_ID')
        ->and($verification->ip_address)->toBe('10.0.0.2');
});

test('coordinates outside valid ranges are rejected', function () {
    $case = CaseFile::factory()->create();

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.3'])
        ->post(route('verification.store', $case->token), [
            'latitude' => 95,
        ])
        ->assertSessionHasErrors(['latitude']);
});

test('submitting an invalid token shows an error page', function () {
    $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.4'])
        ->post('/verify/nonexistent-token')
        ->assertOk()
        ->assertSee('Link verifikasi tidak valid.');
});

test('a case cannot be verified twice', function () {
    $case = CaseFile::factory()->create(['status' => CaseStatus::LinkDibuka]);

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.5'])
        ->post(route('verification.store', $case->token))
        ->assertOk();

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.5'])
        ->post(route('verification.store', $case->token))
        ->assertOk()
        ->assertSee('Sudah Diverifikasi');

    expect($case->fresh()->verifications()->count())->toBe(1);
});

test('verification store is rate limited', function () {
    $case = CaseFile::factory()->create();

    foreach (range(1, 5) as $ignored) {
        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.6'])
            ->post(route('verification.store', $case->token))
            ->assertSuccessful();
    }

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.6'])
        ->post(route('verification.store', $case->token))
        ->assertStatus(429);
});

test('visitor can submit a verification with up to three photos', function () {
    Storage::fake('private');

    $case = CaseFile::factory()->create();

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.1.1'])
        ->post(route('verification.store', $case->token), [
            'photo' => [
                UploadedFile::fake()->image('foto-1.jpg', 400, 400),
                UploadedFile::fake()->image('foto-2.jpg', 400, 400),
                UploadedFile::fake()->image('foto-3.jpg', 400, 400),
            ],
        ])
        ->assertOk();

    $verification = $case->fresh()->verifications()->first();

    expect($verification->photo_status)->toBe(PhotoStatus::Diberikan)
        ->and($verification->photo_paths)->toHaveCount(3);

    foreach ($verification->photo_paths as $path) {
        Storage::disk('private')->assertExists($path);
    }
});

test('only the first three photos are kept', function () {
    Storage::fake('private');

    $case = CaseFile::factory()->create();

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.1.5'])
        ->post(route('verification.store', $case->token), [
            'photo' => [
                UploadedFile::fake()->image('foto-1.jpg', 400, 400),
                UploadedFile::fake()->image('foto-2.jpg', 400, 400),
                UploadedFile::fake()->image('foto-3.jpg', 400, 400),
                UploadedFile::fake()->image('foto-4.jpg', 400, 400),
            ],
        ])
        ->assertOk();

    $verification = $case->fresh()->verifications()->first();

    expect($verification->photo_status)->toBe(PhotoStatus::Diberikan)
        ->and($verification->photo_paths)->toHaveCount(3);
});

test('an invalid photo does not block the verification', function () {
    $case = CaseFile::factory()->create();

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.1.2'])
        ->post(route('verification.store', $case->token), [
            'photo' => [UploadedFile::fake()->create('photo.txt', 100)],
        ])
        ->assertOk();

    $verification = $case->fresh()->verifications()->first();

    expect($verification->photo_status)->toBe(PhotoStatus::Gagal)
        ->and($verification->photo_paths)->toBeNull();
});

test('a denied camera permission is recorded as photo_status ditolak', function () {
    $case = CaseFile::factory()->create();

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.1.3'])
        ->post(route('verification.store', $case->token), [
            'photo_status' => 'ditolak',
        ])
        ->assertOk();

    $verification = $case->fresh()->verifications()->first();

    expect($verification->photo_status)->toBe(PhotoStatus::Ditolak);
});

test('a denied location permission is recorded as location_status ditolak', function () {
    $case = CaseFile::factory()->create();

    $this->withServerVariables(['REMOTE_ADDR' => '10.0.1.4'])
        ->post(route('verification.store', $case->token), [
            'location_status' => 'ditolak',
        ])
        ->assertOk();

    $verification = $case->fresh()->verifications()->first();

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
