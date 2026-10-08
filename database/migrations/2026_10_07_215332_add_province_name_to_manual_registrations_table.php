<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manual_registrations', function (Blueprint $table) {
           $table->string('province_name', 100)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('manual_registrations', function (Blueprint $table) {
            $table->dropColumn('province_name');
        });
    }
};
