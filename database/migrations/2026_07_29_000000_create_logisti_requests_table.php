<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('logisti_requests', function (Blueprint $table): void {
            $table->id();
            $table->string('endpoint')->index();
            $table->string('method')->index();
            $table->nullableMorphs('model');
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->json('error_codes')->nullable();
            $table->boolean('successful')->default(false)->index();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->text('exception_message')->nullable();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logisti_requests');
    }
};
