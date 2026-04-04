<?php

declare(strict_types=1);

use App\Filament\Admin\Resources\UserResource;
use App\Models\User;

beforeEach(function (): void {
    $this->user = User::factory()->create();
});

it('has correct model', function (): void {
    $resource = new UserResource();

    $this->assertEquals(User::class, $resource->getModel());
});

it('has correct navigation label', function (): void {
    $resource = new UserResource();

    $this->assertEquals('Users', $resource->getNavigationLabel());
});

it('has correct pages', function (): void {
    $resource = new UserResource();
    $pages = $resource->getPages();

    $this->assertArrayHasKey('index', $pages);
    $this->assertArrayHasKey('create', $pages);
    $this->assertArrayHasKey('edit', $pages);
});
