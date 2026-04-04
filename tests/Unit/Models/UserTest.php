<?php

declare(strict_types=1);

use App\Models\Site;
use App\Models\User;
use Filament\Panel;

beforeEach(function (): void {
    $this->user = User::factory()->create();
});

it('has many sites', function (): void {
    Site::factory()->for($this->user)->create();
    Site::factory()->for($this->user)->create();

    $this->assertEquals(2, $this->user->sites->count());
});

it('can access panel when admin', function (): void {
    $this->user->is_admin = true;
    $this->user->save();

    $panel = new Panel('admin');
    $result = $this->user->canAccessPanel($panel);

    $this->assertTrue($result);
});

it('cannot access panel when not admin', function (): void {
    $this->user->is_admin = false;
    $this->user->save();

    $panel = new Panel('admin');
    $result = $this->user->canAccessPanel($panel);

    $this->assertFalse($result);
});

it('has correct casts', function (): void {
    $this->user->is_admin = true;
    $this->assertIsBool($this->user->is_admin);

    $this->assertIsString($this->user->name);
    $this->assertIsString($this->user->email);
});

it('has name attribute with ucfirst', function (): void {
    $user = User::factory()->create(['name' => 'john']);
    $user->refresh();

    $this->assertEquals('John', $user->name);
});
