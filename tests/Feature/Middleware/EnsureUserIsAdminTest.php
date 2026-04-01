<?php

declare(strict_types=1);

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

beforeEach(function (): void {
    $this->user = User::factory()->create();
    $this->middleware = new EnsureUserIsAdmin();
});

it('allows admin users', function (): void {
    $this->user->update(['is_admin' => true]);

    $request = Request::create('/admin');
    $this->actingAs($this->user);

    $response = $this->middleware->handle($request, fn ($request): Response => new Response('OK'));

    $this->assertEquals('OK', $response->getContent());
});

it('blocks non-admin users', function (): void {
    $this->user->update(['is_admin' => false]);

    $request = Request::create('/admin');

    $this->middleware->handle($request, fn ($request): Response => new Response('OK'));
})->throws(HttpException::class, 'Access denied. Admin access required.');
