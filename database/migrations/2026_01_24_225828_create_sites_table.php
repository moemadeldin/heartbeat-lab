<?php

declare(strict_types=1);

use App\Enums\SiteStatus;
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
            $table->string('status')->index()->default(SiteStatus::Checking->value);
            $table->timestamp('ssl_expires_at')->nullable();
            $table->string('ssl_issuer', 255)->nullable();
            $table->boolean('ssl_valid')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'url']);
            $table->unique(['user_id', 'name']);
        });
    }
};
