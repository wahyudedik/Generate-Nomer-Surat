<?php

use App\Models\Letter;
use App\Models\LetterFormat;
use App\Models\LetterFormatSegment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

function makeOutFormat(): LetterFormat
{
    $format = LetterFormat::create([
        'type' => 'out',
        'period_mode' => 'year',
        'counter_scope' => 'global',
        'name' => 'Format Default',
        'description' => 'Test format',
    ]);

    LetterFormatSegment::create([
        'format_id' => $format->id,
        'order' => 1,
        'kind' => 'sequence',
        'padding' => 3,
    ]);

    LetterFormatSegment::create([
        'format_id' => $format->id,
        'order' => 2,
        'kind' => 'text',
        'value' => '/ABC/',
    ]);

    LetterFormatSegment::create([
        'format_id' => $format->id,
        'order' => 3,
        'kind' => 'year',
    ]);

    return $format->fresh('segments');
}

test('can generate outgoing letter number', function () {
    Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->assignRole('staff');
    $format = makeOutFormat();

    $response = $this->actingAs($user)->post(route('letters.out.generate'), [
        'format_id' => $format->id,
    ]);

    $letter = Letter::first();

    $response->assertRedirect(route('letters.out.edit', $letter));
    expect($letter->status)->toBe(Letter::STATUS_DRAFT);
    expect($letter->number)->not->toBeNull();
});

test('cannot generate new number when draft exists for same type', function () {
    Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->assignRole('staff');
    $format = makeOutFormat();

    Letter::create([
        'type' => 'out',
        'format_id' => $format->id,
        'number' => '001/ABC/2026',
        'sequence' => 1,
        'status' => Letter::STATUS_DRAFT,
        'created_by' => $user->id,
    ]);

    $response = $this->actingAs($user)->post(route('letters.out.generate'), [
        'format_id' => $format->id,
    ]);

    $response->assertSessionHasErrors('letter');
});

test('outgoing letter requires scan upload to complete', function () {
    Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->assignRole('staff');
    $format = makeOutFormat();

    $letter = Letter::create([
        'type' => 'out',
        'format_id' => $format->id,
        'number' => '001/ABC/2026',
        'sequence' => 1,
        'status' => Letter::STATUS_DRAFT,
        'created_by' => $user->id,
    ]);

    $response = $this->actingAs($user)->put(route('letters.out.update', $letter), [
        'title' => 'Surat Uji',
        'description' => 'Deskripsi',
        'issued_at' => now()->format('Y-m-d'),
    ]);

    $response->assertSessionHasErrors('scan');
});

test('incoming letter can be stored with scan', function () {
    Storage::fake('public');
    Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
    $user = User::factory()->create();
    $user->assignRole('staff');

    $response = $this->actingAs($user)->post(route('letters.in.store'), [
        'title' => 'Surat Masuk',
        'description' => 'Deskripsi',
        'issued_at' => now()->format('Y-m-d'),
        'scan' => UploadedFile::fake()->create('scan.pdf', 200, 'application/pdf'),
    ]);

    $response->assertRedirect(route('letters.in.index'));
    expect(Letter::first()->type)->toBe('in');
});
