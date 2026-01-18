<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
});

test('admin can view users index', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->get(route('users.index'));

    $response->assertOk();
});

test('non admin cannot access user management', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('users.index'));

    $response->assertForbidden();
});

test('admin can create user with avatar', function () {
    Storage::fake('public');

    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $response = $this->actingAs($admin)->post(route('users.store'), [
        'name' => 'Staff User',
        'email' => 'staff@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'staff',
        'is_active' => true,
        'avatar' => UploadedFile::fake()->image('avatar.jpg'),
    ]);

    $response->assertRedirect(route('users.index'));

    $user = User::where('email', 'staff@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->avatar_path)->not->toBeNull();

    Storage::disk('public')->assertExists($user->avatar_path);
});

test('admin can update user status', function () {
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $user = User::factory()->create();
    $user->assignRole('staff');

    $response = $this->actingAs($admin)->put(route('users.update', $user), [
        'name' => $user->name,
        'email' => $user->email,
        'role' => 'staff',
        'is_active' => false,
    ]);

    $response->assertRedirect(route('users.index'));
    expect($user->refresh()->is_active)->toBeFalse();
});
