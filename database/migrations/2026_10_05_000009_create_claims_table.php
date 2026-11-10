<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('claims', function (Blueprint $table) {
            $table->id();
            $table->string('claim_code')->unique(); // CLM-2026-0001
            $table->foreignId('lost_item_id')->nullable()->constrained('lost_items')->nullOnDelete();
            $table->foreignId('found_item_id')->constrained('found_items')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Claimant
            $table->text('ownership_proof');
            $table->text('special_characteristics');
            $table->json('proof_photos')->nullable();
            $table->string('status')->default('DIAJUKAN'); // DIAJUKAN, DIPERIKSA, DISETUJUI, DITOLAK, SIAP_DIAMBIL, SELESAI
            $table->text('rejection_reason')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('claims');
    }
};
