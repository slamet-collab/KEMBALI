<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('handover_records', function (Blueprint $table) {
            $table->id();
            $table->string('handover_code')->unique(); // HO-2026-0001
            $table->foreignId('claim_id')->constrained('claims')->onDelete('cascade');
            $table->foreignId('found_item_id')->constrained('found_items')->onDelete('cascade');
            $table->foreignId('claimed_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('handed_over_by')->constrained('users')->onDelete('cascade'); // Staff
            $table->dateTime('handover_date')->useCurrent();
            $table->string('recipient_identity_no');
            $table->text('notes')->nullable();
            $table->json('photos')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('handover_records');
    }
};
