<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_logs', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('repair_id')
                ->constrained('repairs')
                ->cascadeOnDelete();

            $table->string('status', 30);

            $table->text('description');

            $table->foreignId('updated_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->dateTime('created_at');

            $table->index([
                'repair_id',
                'created_at',
                'id',
            ]);

            $table->index([
                'updated_by',
                'created_at',
                'id',
            ]);

            $table->index([
                'created_at',
                'id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_logs');
    }
};
