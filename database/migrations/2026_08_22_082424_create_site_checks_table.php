<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_checks', function (Blueprint $table): void {
            $table->id();
            $table->foreignUuid('site_id')->index()->constrained()->cascadeOnDelete();
            $table->boolean('is_online');
            $table->integer('status_code')->nullable();
            $table->decimal('response_time', 8, 2)->nullable();
            $table->timestamp('checked_at');
            $table->timestamps();
        });
    }
};
