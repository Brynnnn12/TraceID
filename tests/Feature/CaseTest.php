<?php

use App\Enums\CaseStatus;
use App\Models\CaseFile;
use App\Models\User;
use App\Models\Verification;
use App\Models\VerificationTemplate;
use App\Services\CaseService;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->transferTemplate = VerificationTemplate::factory()->create();
});

test('guests are redirected to login when accessing cases', function () {
    $this->get('/cases')->assertRedirect('/login');
    $this->get('/cases/create')->assertRedirect('/login');
});

test('admin can view the list of cases', function () {
    CaseFile::factory()->count(3)->create();

    $this->actingAs($this->user)
        ->get(route('cases.index'))
        ->assertOk()
        ->assertViewHas('cases');
});

test('admin can create a case with reference number and token', function () {
    $response = $this->actingAs($this->user)->post(route('cases.store'), [
        'template_id' => $this->transferTemplate->id,
        'fields' => [
            'target_name' => 'Budi Santoso',
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'amount' => 500000,
        ],
        'notes' => 'Transfer DP',
    ]);

    $response->assertRedirect();

    $case = CaseFile::query()->first();

    expect($case)->not->toBeNull()
        ->and($case->status)->toBe(CaseStatus::Aktif)
        ->and($case->fields)->toBe([
            'target_name' => 'Budi Santoso',
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'amount' => '500000',
        ])
        ->and($case->reference_number)->toMatch('/^TRC-\d{8}-\d{4}$/')
        ->and(strlen($case->token))->toBe(32)
        ->and($case->expires_at)->not->toBeNull();
});

test('case store requires authentication', function () {
    $this->post(route('cases.store'), [
        'template_id' => $this->transferTemplate->id,
        'fields' => [
            'target_name' => 'Budi Santoso',
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'amount' => 500000,
        ],
    ])->assertRedirect('/login');
});

test('case store requires a template selection', function () {
    $this->actingAs($this->user)
        ->post(route('cases.store'), [])
        ->assertSessionHasErrors(['template_id']);
});

test('case store requires the template fields', function () {
    $this->actingAs($this->user)
        ->post(route('cases.store'), ['template_id' => $this->transferTemplate->id])
        ->assertSessionHasErrors([
            'fields.target_name',
            'fields.bank_name',
            'fields.account_number',
            'fields.amount',
        ]);
});

test('case store validates template field types', function () {
    $this->actingAs($this->user)
        ->post(route('cases.store'), [
            'template_id' => $this->transferTemplate->id,
            'fields' => [
                'target_name' => 'Budi',
                'bank_name' => 'BCA',
                'account_number' => '123',
                'amount' => 'abc',
            ],
        ])
        ->assertSessionHasErrors(['fields.amount']);
});

test('reference numbers are unique per day sequence', function () {
    $service = app(CaseService::class);

    $first = $service->create([
        'template_id' => $this->transferTemplate->id,
        'fields' => [
            'target_name' => 'A',
            'bank_name' => 'BCA',
            'account_number' => '1',
            'amount' => 1000,
        ],
    ]);

    $second = $service->create([
        'template_id' => $this->transferTemplate->id,
        'fields' => [
            'target_name' => 'B',
            'bank_name' => 'Mandiri',
            'account_number' => '2',
            'amount' => 2000,
        ],
    ]);

    expect($first->reference_number)->toBe('TRC-'.now()->format('Ymd').'-0001')
        ->and($second->reference_number)->toBe('TRC-'.now()->format('Ymd').'-0002')
        ->and($first->token)->not->toBe($second->token);
});

test('admin can view a single case', function () {
    $case = CaseFile::factory()->create();

    $this->actingAs($this->user)
        ->get(route('cases.show', $case))
        ->assertOk()
        ->assertSee($case->reference_number);
});

test('admin can update a case', function () {
    $case = CaseFile::factory()->create();

    $response = $this->actingAs($this->user)->put(route('cases.update', $case), [
        'template_id' => $this->transferTemplate->id,
        'fields' => [
            'target_name' => 'Siti Aminah',
            'bank_name' => 'BNI',
            'account_number' => '9876543210',
            'amount' => 750000,
        ],
        'notes' => 'Perbarui',
    ]);

    $response->assertRedirect(route('cases.show', $case));

    $fresh = $case->fresh();

    expect($fresh->fields['target_name'])->toBe('Siti Aminah')
        ->and($fresh->fields['amount'])->toBe('750000')
        ->and($fresh->reference_number)->toBe($case->reference_number);
});

test('admin can delete a case', function () {
    $case = CaseFile::factory()->create();

    $response = $this->actingAs($this->user)->delete(route('cases.destroy', $case));

    $response->assertRedirect(route('cases.index'));

    expect(CaseFile::find($case->id))->toBeNull();
});

test('admin can regenerate a verification link', function () {
    $case = CaseFile::factory()->create(['status' => CaseStatus::LinkDibuka]);

    $oldToken = $case->token;

    $response = $this->actingAs($this->user)
        ->post(route('cases.regenerate-link', $case));

    $response->assertRedirect(route('cases.show', $case));

    $fresh = $case->fresh();

    expect($fresh->token)->not->toBe($oldToken)
        ->and(strlen($fresh->token))->toBe(32)
        ->and($fresh->status)->toBe(CaseStatus::Aktif)
        ->and($fresh->expires_at->isFuture())->toBeTrue();
});

test('a verified case cannot regenerate its link', function () {
    $case = CaseFile::factory()->create(['status' => CaseStatus::Terverifikasi]);

    $this->actingAs($this->user)
        ->post(route('cases.regenerate-link', $case))
        ->assertForbidden();
});

test('admin can close a case link', function () {
    $case = CaseFile::factory()->create(['status' => CaseStatus::LinkDibuka]);

    $response = $this->actingAs($this->user)->post(route('cases.close', $case));

    $response->assertRedirect(route('cases.show', $case));

    expect($case->fresh()->status)->toBe(CaseStatus::Ditutup);
});

test('cases can be filtered by status', function () {
    CaseFile::factory()->create(['status' => CaseStatus::Aktif]);
    CaseFile::factory()->create(['status' => CaseStatus::Terverifikasi]);

    $this->actingAs($this->user)
        ->get(route('cases.index', ['status' => CaseStatus::Terverifikasi->value]))
        ->assertOk()
        ->assertViewHas('cases', function ($cases) {
            return $cases->total() === 1
                && $cases->first()->status === CaseStatus::Terverifikasi;
        });
});

test('cases can be filtered by template', function () {
    $otherTemplate = VerificationTemplate::factory()->create([
        'name' => 'Konfirmasi Penerimaan Barang',
        'slug' => 'goods-receipt',
    ]);

    CaseFile::factory()->create(['template_id' => $this->transferTemplate->id]);
    CaseFile::factory()->create(['template_id' => $otherTemplate->id]);

    $this->actingAs($this->user)
        ->get(route('cases.index', ['template' => $otherTemplate->id]))
        ->assertOk()
        ->assertViewHas('cases', function ($cases) use ($otherTemplate) {
            return $cases->total() === 1
                && $cases->first()->template_id === $otherTemplate->id;
        });
});

test('case can report expired status based on expires_at', function () {
    $expired = CaseFile::factory()->create(['expires_at' => now()->subHour()]);
    $active = CaseFile::factory()->create(['expires_at' => now()->addHours(2)]);

    expect($expired->isExpired())->toBeTrue()
        ->and($active->isExpired())->toBeFalse();
});

test('case is not expired once verified', function () {
    $case = CaseFile::factory()->create([
        'status' => CaseStatus::Terverifikasi,
        'expires_at' => now()->subHour(),
    ]);

    expect($case->isExpired())->toBeFalse();
});

test('case detail renders a leaflet map for verifications with coordinates', function () {
    $case = CaseFile::factory()->create();

    Verification::factory()->create([
        'case_id' => $case->id,
        'latitude' => -6.2,
        'longitude' => 106.816666,
    ]);

    $this->actingAs($this->user)
        ->get(route('cases.show', $case))
        ->assertOk()
        ->assertSee('leafletMap(-6.2000000, 106.8166660)');
});

test('case detail does not render a map without coordinates', function () {
    $case = CaseFile::factory()->create();

    Verification::factory()->create([
        'case_id' => $case->id,
        'latitude' => null,
        'longitude' => null,
    ]);

    $this->actingAs($this->user)
        ->get(route('cases.show', $case))
        ->assertOk()
        ->assertDontSee('leafletMap(');
});
