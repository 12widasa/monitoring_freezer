<?php

use App\Enums\VerificationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_intakes', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('freezer_id')
                ->constrained('freezers')
                ->restrictOnDelete();

            $table->string('intake_code', 20)
                ->unique();

            $table->text('complaint_note')
                ->nullable();

            $table->text('condition_note')
                ->nullable();

            $table->string('status_verifikasi', 30)
                ->default(
                    VerificationStatus::PENDING_ARRIVAL->value,
                );

            $table->text('rejection_reason')
                ->nullable();

            $table->foreignId('received_by')
                ->constrained('users')
                ->restrictOnDelete();

            $table->dateTime('received_at');

            $table->foreignId('verified_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->dateTime('verified_at')
                ->nullable();

            $table->dateTime('completed_at')
                ->nullable();

            $table->timestamps();

            $table->index([
                'freezer_id',
                'received_at',
                'id',
            ]);

            $table->index([
                'status_verifikasi',
                'received_at',
                'id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_intakes');
    }
};
