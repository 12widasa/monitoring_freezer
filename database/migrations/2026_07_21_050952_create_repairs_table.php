<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repairs', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('freezer_id')
                ->constrained('freezers')
                ->restrictOnDelete();

            $table->foreignId('service_intake_id')
                ->unique()
                ->constrained('service_intakes')
                ->restrictOnDelete();

            $table->foreignId('technician_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('admin_id')
                ->constrained('users')
                ->restrictOnDelete();

            $table->string('status', 30)
                ->default('queued');

            $table->text('initial_analysis')
                ->nullable();

            $table->timestamps();

            $table->index([
                'technician_id',
                'status',
            ]);

            $table->index([
                'status',
                'created_at',
            ]);

            $table->index([
                'status',
                'updated_at',
            ]);

            $table->index([
                'freezer_id',
                'created_at',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repairs');
    }
};
