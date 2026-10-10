
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('master_employees', 'nip')) {
            Schema::table('master_employees', function (Blueprint $table) {
                $table->string('nip', 18)->nullable()->index();
            });
        }

        if (!Schema::hasColumn('manual_registrations', 'nip')) {
            Schema::table('manual_registrations', function (Blueprint $table) {
                $table->string('nip', 18)->nullable();
            });
        }
    }

    public function down(): void
    {
    }
};
