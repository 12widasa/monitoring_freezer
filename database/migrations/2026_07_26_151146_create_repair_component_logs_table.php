<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'repair_component_logs',
            function (Blueprint $table): void {
                $table->id();

                $table->foreignId('repair_component_id')
                    ->constrained('repair_components')
                    ->cascadeOnDelete();

                $table->string('status', 30);

                $table->unsignedInteger('quantity');

                $table->text('note')
                    ->nullable();

                $table->foreignId('updated_by')
                    ->constrained('users')
                    ->restrictOnDelete();

                $table->dateTime('created_at');

                $table->index([
                    'repair_component_id',
                    'created_at',
                    'id',
                ]);
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_component_logs');
    }
};
