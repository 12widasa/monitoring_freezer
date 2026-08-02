<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'repair_components',
            function (Blueprint $table): void {
                $table->id();

                $table->foreignId('repair_id')
                    ->constrained('repairs')
                    ->cascadeOnDelete();

                $table->foreignId('component_id')
                    ->constrained('components')
                    ->restrictOnDelete();

                $table->unsignedInteger('quantity')
                    ->default(1);

                $table->string('status', 30)
                    ->default('requested');

                $table->text('note')
                    ->nullable();

                $table->foreignId('added_by')
                    ->constrained('users')
                    ->restrictOnDelete();

                $table->foreignId('installed_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->dateTime('installed_at')
                    ->nullable();

                $table->timestamps();

                $table->index([
                    'repair_id',
                    'status',
                ]);

                $table->index([
                    'repair_id',
                    'component_id',
                ]);
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_components');
    }
};
