<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manual_registrations', function (Blueprint $table) {
            $table->foreignId('institution_id')
                ->nullable()
                ->after('nik_lookup')
                ->constrained('institutions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('manual_registrations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('institution_id');
        });
    }
};
