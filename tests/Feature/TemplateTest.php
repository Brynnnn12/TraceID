<?php

use App\Models\CaseFile;
use App\Models\User;
use App\Models\VerificationTemplate;
use App\Services\CaseService;
use Database\Seeders\VerificationTemplateSeeder;

test('the seeder creates six active verification templates', function () {
    $this->seed(VerificationTemplateSeeder::class);

    expect(VerificationTemplate::count())->toBe(6)
        ->and(VerificationTemplate::where('is_active', true)->count())->toBe(6)
        ->and(VerificationTemplate::where('slug', 'transfer')->exists())->toBeTrue()
        ->and(VerificationTemplate::where('slug', 'goods-receipt')->exists())->toBeTrue()
        ->and(VerificationTemplate::where('slug', 'appointment')->exists())->toBeTrue()
        ->and(VerificationTemplate::where('slug', 'document')->exists())->toBeTrue()
        ->and(VerificationTemplate::where('slug', 'identity')->exists())->toBeTrue()
        ->and(VerificationTemplate::where('slug', 'pickup')->exists())->toBeTrue();
});

test('a non-transfer template drives the public verification page', function () {
    $user = User::factory()->create();
    $template = VerificationTemplate::factory()->create([
        'name' => 'Konfirmasi Penerimaan Barang',
        'slug' => 'goods-receipt',
        'title' => 'Verifikasi Penerimaan Barang',
        'button_text' => 'Konfirmasi Penerimaan',
        'theme' => 'emerald',
    ]);

    $this->actingAs($user)->post(route('cases.store'), [
        'template_id' => $template->id,
        'fields' => [
            'resi_number' => 'JKT-000123',
            'recipient_name' => 'Budi Santoso',
            'address' => 'Jl. Merdeka No. 1, Jakarta',
        ],
    ])->assertRedirect();

    $case = CaseFile::query()->first();

    expect($case->template_id)->toBe($template->id);

    $this->get(route('verification.show', $case->token))
        ->assertOk()
        ->assertSee('Verifikasi Penerimaan Barang')
        ->assertSee('Nomor Resi')
        ->assertSee('JKT-000123')
        ->assertSee('Alamat')
        ->assertSee('Konfirmasi Penerimaan')
        ->assertSee('bg-emerald-600');
});

test('case fields not defined by the template are dropped', function () {
    $template = VerificationTemplate::factory()->create();

    $case = app(CaseService::class)->create([
        'template_id' => $template->id,
        'fields' => [
            'target_name' => 'Budi',
            'bank_name' => 'BCA',
            'account_number' => '123',
            'amount' => 1000,
            'random_key' => 'should be dropped',
        ],
    ]);

    expect($case->fields)->toBe([
        'target_name' => 'Budi',
        'bank_name' => 'BCA',
        'account_number' => '123',
        'amount' => '1000',
    ]);
});

test('an invalid template_id is rejected', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('cases.store'), [
            'template_id' => 99999,
            'fields' => ['target_name' => 'Budi'],
        ])
        ->assertSessionHasErrors(['template_id']);
});

test('the transfer template defines transfer fields with a currency amount', function () {
    $template = VerificationTemplate::factory()->create();

    $fieldKeys = collect($template->fields())->pluck('key')->all();

    expect($fieldKeys)->toBe(['target_name', 'bank_name', 'account_number', 'amount'])
        ->and(collect($template->fields())->firstWhere('key', 'amount')['format'])->toBe('currency');
});
