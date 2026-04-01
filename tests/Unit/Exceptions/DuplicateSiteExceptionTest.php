<?php

declare(strict_types=1);

use App\Exceptions\DuplicateSiteException;

it('creates exception with default field', function (): void {
    $exception = new DuplicateSiteException();

    $this->assertEquals('url', $exception->field);
});

it('creates exception with custom field', function (): void {
    $exception = new DuplicateSiteException('name');

    $this->assertEquals('name', $exception->field);
});
