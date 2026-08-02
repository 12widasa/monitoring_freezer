<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('components', function (Blueprint $table): void {
            $table->id();

            $table->string('name', 100);

            $table->string('part_number', 100)
                ->nullable()
                ->unique();

            $table->string('unit', 30)
                ->default('unit');

            $table->timestamps();

            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('components');
    }
};
