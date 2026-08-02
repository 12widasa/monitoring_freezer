<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();

            $table->string('username', 50)
                ->unique();

            $table->string('email', 100)
                ->unique();

            $table->string('password');

            $table->rememberToken();

            $table->string('role', 20)
                ->default('customer');

            $table->string('name', 100);

            $table->string('phone', 30);

            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->index([
                'role',
                'is_active',
                'name',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
