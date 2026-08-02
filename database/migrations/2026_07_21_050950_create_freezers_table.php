<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('freezers', function (Blueprint $table): void {
            $table->id();

            $table->string('freezer_code', 20)
                ->nullable()
                ->unique();

            $table->foreignId('customer_id')
                ->constrained('customers')
                ->restrictOnDelete();

            $table->string('brand', 50);

            $table->string('model', 50);

            $table->string('serial_number', 100)
                ->nullable()
                ->unique();

            $table->unsignedInteger('capacity_liter')
                ->nullable();

            $table->string('estimated_age', 30)
                ->nullable();

            $table->string('photo_path', 255)
                ->nullable();

            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->timestamps();

            $table->index([
                'customer_id',
                'created_at',
            ]);

            $table->index([
                'brand',
                'model',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('freezers');
    }
};
