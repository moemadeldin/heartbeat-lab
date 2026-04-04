<?php

declare(strict_types=1);

use App\Filament\Admin\Resources\SiteResource;
use App\Models\Site;
use App\Models\User;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->site = Site::factory()->for($this->user)->create();
});

it('has correct model', function (): void {
    $resource = new SiteResource();

    $this->assertEquals(Site::class, $resource->getModel());
});

it('has correct navigation label', function (): void {
    $resource = new SiteResource();

    $this->assertEquals('Sites', $resource->getNavigationLabel());
});

it('has correct pages', function (): void {
    $resource = new SiteResource();
    $pages = $resource->getPages();

    $this->assertArrayHasKey('index', $pages);
    $this->assertArrayHasKey('create', $pages);
    $this->assertArrayHasKey('edit', $pages);
});
