<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Hash;

it('creates an admin user successfully', function (): void {

    $userData = [
        'name' => 'adminn',
        'email' => 'adminn@gmail.com',
        'password' => 'password123',
    ];
    $this->artisan('admin:create')
        ->expectsQuestion('name', $userData['name'])
        ->expectsQuestion('email', $userData['email'])
        ->expectsQuestion('password', $userData['password'])
        ->assertExitCode(0);

    $this->assertDatabaseHas('users', [
        'name' => $userData['name'],
        'email' => $userData['email'],
        'is_admin' => true,
    ]);

    $user = User::query()
        ->where('email', $userData['email'])
        ->first();
    expect(Hash::check($userData['password'], $user->password))->toBeTrue();
});

it('fails when email is invalid', function (): void {
    $this->artisan('admin:create')
        ->expectsQuestion('name', 'sdklfjlkdsajf')
        ->expectsQuestion('email', 'sdakfljdsf')
        ->expectsQuestion('password', 'securepassword')
        ->expectsOutputToContain('The email field must be a valid email address.')
        ->assertExitCode(1);
});

it('fails when password is too short', function (): void {
    $this->artisan('admin:create')
        ->expectsQuestion('name', 'sakdjflksjd')
        ->expectsQuestion('email', 'sadkjflksajf@gmail.com')
        ->expectsQuestion('password', '123')
        ->expectsOutputToContain('The password field must be at least 8 characters.')
        ->assertExitCode(1);
});
