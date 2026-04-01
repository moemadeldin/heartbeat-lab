<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Site>
 */
final class SiteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        return [
            'user_id' => User::factory(),
            'name' => fake()->name(),
            'url' => fake()->url(),
            'last_checked_at' => now()->subMinutes(random_int(1, 60)),
        ];
    }
}
