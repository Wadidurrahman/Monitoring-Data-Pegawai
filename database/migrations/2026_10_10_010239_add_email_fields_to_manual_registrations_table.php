<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manual_registrations', function (Blueprint $table) {
            $table->string('email', 254)->nullable()->after('nip');
            $table->timestamp('email_consent_at')->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('manual_registrations', function (Blueprint $table) {
            $table->dropColumn(['email', 'email_consent_at']);
        });
    }
};
