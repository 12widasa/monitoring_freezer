<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'repair_assignments',
            function (Blueprint $table): void {
                $table->id();

                $table->foreignId('repair_id')
                    ->constrained('repairs')
                    ->cascadeOnDelete();

                $table->foreignId('technician_id')
                    ->constrained('users')
                    ->restrictOnDelete();

                $table->foreignId('assigned_by')
                    ->constrained('users')
                    ->restrictOnDelete();

                $table->dateTime('assigned_at');

                $table->dateTime('ended_at')
                    ->nullable();

                $table->foreignId('ended_by')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();

                $table->text('end_reason')
                    ->nullable();

                $table->timestamps();

                $table->index([
                    'repair_id',
                    'ended_at',
                    'assigned_at',
                    'id',
                ]);

                $table->index([
                    'technician_id',
                    'ended_at',
                ]);
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_assignments');
    }
};
