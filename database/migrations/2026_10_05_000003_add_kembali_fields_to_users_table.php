<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('identity_no')->nullable()->after('email')->comment('NIM / NIP / ID Kampus');
            $table->string('phone')->nullable()->after('identity_no');
            $table->foreignId('role_id')->nullable()->after('phone')->constrained('roles')->nullOnDelete();
            $table->string('status')->default('active')->after('role_id');
            $table->string('avatar')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn(['identity_no', 'phone', 'role_id', 'status', 'avatar']);
        });
    }
};
