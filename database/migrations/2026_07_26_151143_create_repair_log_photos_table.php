<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create(
            'repair_log_photos',
            function (Blueprint $table): void {
                $table->id();

                $table->foreignId('repair_log_id')
                    ->constrained('repair_logs')
                    ->cascadeOnDelete();

                $table->string('photo_path', 255);

                $table->unsignedTinyInteger('sort_order')
                    ->default(0);

                $table->dateTime('created_at');

                $table->index([
                    'repair_log_id',
                    'sort_order',
                    'id',
                ]);
            },
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_log_photos');
    }
};
