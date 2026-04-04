<?php

declare(strict_types=1);

use App\Models\Site;
use App\Models\User;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->site = Site::factory()->for($this->user)->create();
});

it('belongs to user', function (): void {
    $this->assertEquals($this->user->id, $this->site->user->id);
});

it('has correct casts', function (): void {
    $this->site->is_online = true;
    $this->assertIsBool($this->site->is_online);

    $this->site->status_code = 200;
    $this->assertIsInt($this->site->status_code);
});

it('has uuid id', function (): void {
    $this->assertNotEmpty($this->site->id);
});

it('filters by user scope', function (): void {
    $sites = Site::userSites($this->user)->get();

    $this->assertEquals(1, $sites->count());
});

it('checks url duplicate scope', function (): void {
    Site::factory()->for($this->user)->create([
        'url' => 'https://duplicate.com',
    ]);

    $duplicates = Site::whereURLDuplicate($this->user, 'https://duplicate.com')->get();

    $this->assertEquals(1, $duplicates->count());
});

it('checks name duplicate scope', function (): void {
    Site::factory()->for($this->user)->create([
        'name' => 'Duplicate Name',
    ]);

    $duplicates = Site::whereNameDuplicate($this->user, 'Duplicate Name')->get();

    $this->assertEquals(1, $duplicates->count());
});

it('applies sites with no duplicates scope', function (): void {
    Site::factory()->for($this->user)->create();
    Site::factory()->for($this->user)->create();

    $sites = Site::sitesWithNoDuplicates($this->user)->get();

    $this->assertNotEmpty($sites);
});
