<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lost_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('title');
            $table->string('brand')->nullable();
            $table->string('primary_color');
            $table->text('description');
            $table->string('last_seen_location');
            $table->date('last_seen_date');
            $table->time('last_seen_time')->nullable();
            $table->json('photos')->nullable();
            $table->string('status')->default('MENUNGGU_VERIFIKASI'); // DRAFT, MENUNGGU_VERIFIKASI, DIPUBLIKASIKAN, DITEMUKAN, DITOLAK
            $table->text('verification_note')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lost_items');
    }
};
