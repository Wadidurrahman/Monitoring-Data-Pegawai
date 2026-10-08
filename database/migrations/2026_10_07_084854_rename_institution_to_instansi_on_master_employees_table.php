<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('master_employees', 'institution') && !Schema::hasColumn('master_employees', 'instansi')) {
            Schema::table('master_employees', function (Blueprint $table) {
                $table->renameColumn('institution', 'instansi');
            });
        }

        if (!Schema::hasColumn('master_employees', 'instansi')) {
            Schema::table('master_employees', function (Blueprint $table) {
                $table->string('instansi')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('master_employees', 'instansi') && !Schema::hasColumn('master_employees', 'institution')) {
            Schema::table('master_employees', function (Blueprint $table) {
                $table->renameColumn('instansi', 'institution');
            });
        }
    }
};
