<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

final class CreateAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an admin user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $user['name'] = $this->ask('name');
        $user['email'] = $this->ask('email');
        $user['password'] = $this->secret('password');

        $validator = Validator::make($user, [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9]+$/'],
            'email' => ['required', 'string', 'email:rfc,dns', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:88'],
        ]);

        if ($validator->fails()) {
            $this->error('Validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->line('  - '.$error);
            }

            return Command::FAILURE;
        }

        $user = User::query()->create([
            'name' => $user['name'],
            'email' => $user['email'],
            'password' => $user['password'],
            'is_admin' => true,
        ]);

        $this->info('Admin created successfully!');
        $this->line('  Name: '.$user->name);
        $this->line('  Email: '.$user->email);
        $this->line('  Admin: Yes');

        return Command::SUCCESS;

    }
}
