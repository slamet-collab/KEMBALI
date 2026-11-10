<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('item_storages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('found_item_id')->constrained('found_items')->onDelete('cascade');
            $table->string('storage_code')->unique(); // KB-2026-0001
            $table->string('building'); // Gedung B
            $table->string('room'); // Ruang Lost & Found
            $table->string('cupboard')->nullable(); // Lemari A
            $table->string('shelf')->nullable(); // Rak 03
            $table->string('qr_code_path')->nullable();
            $table->foreignId('stored_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('received_at')->useCurrent();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_storages');
    }
};
