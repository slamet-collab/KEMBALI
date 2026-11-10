<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('found_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('title');
            $table->string('brand')->nullable();
            $table->string('primary_color');
            $table->text('description');
            $table->string('found_location');
            $table->date('found_date');
            $table->time('found_time')->nullable();
            $table->json('photos')->nullable();
            $table->string('status')->default('MENUNGGU_VERIFIKASI'); // DRAFT, MENUNGGU_VERIFIKASI, DIPUBLIKASIKAN, DITEMUKAN, DITAMPUNG, MENUNGGU_KLAIM, KLAIM_DIPROSES, SIAP_DIAMBIL, DIKEMBALIKAN, DITOLAK
            $table->boolean('is_public')->default(true);
            $table->string('holding_option')->default('saya_simpan'); // saya_simpan, satpam, pos_central
            $table->string('holding_location_name')->nullable();
            $table->text('verification_note')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('found_items');
    }
};
