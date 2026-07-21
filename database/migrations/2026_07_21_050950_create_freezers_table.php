<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('freezers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->string('brand', 50);
            $table->string('model', 50);
            $table->string('serial_number', 100)->unique();
            $table->integer('capacity_liter')->nullable();
            $table->string('estimated_age', 30)->nullable();
            $table->string('photo_path', 255)->nullable();
            $table->string('status_verifikasi', 30)->default('pending_arrival');
            $table->text('rejection_reason')->nullable();
            $table->text('complaint_note')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('freezers');
    }
};
