<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lost_item_id')->constrained('lost_items')->onDelete('cascade');
            $table->foreignId('found_item_id')->constrained('found_items')->onDelete('cascade');
            $table->integer('match_score')->default(0); // 0 - 100 %
            $table->string('confidence_level')->default('SEDANG'); // SANGAT_TINGGI, TINGGI, SEDANG, RENDAH
            $table->json('match_reasons')->nullable();
            $table->string('status')->default('PENDING'); // PENDING, CONFIRMED, REJECTED
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('matches');
    }
};
