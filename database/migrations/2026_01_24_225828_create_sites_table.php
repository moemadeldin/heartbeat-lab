<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sites', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')
                ->index()
                ->constrained('users')
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('url');
            $table->boolean('is_online')->index()->nullable();
            $table->decimal('uptime', 5, 2)->default(0.00);
            $table->timestamp('last_checked_at')->index()->nullable();
            $table->integer('status_code')->nullable();
            $table->decimal('response_time', 8, 2)->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'url']);
            $table->unique(['user_id', 'name']);
        });
    }
};
