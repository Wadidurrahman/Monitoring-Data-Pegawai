<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manual_registrations', function (Blueprint $table) {
            $table->dropColumn('family_card_number');
        });
    }

    public function down(): void
    {
        Schema::table('manual_registrations', function (Blueprint $table) {
            $table->string('family_card_number', 16)->nullable();
        });
    }
};
