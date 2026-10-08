<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up(): void
    {
        if (
            Schema::hasTable('master_employees') &&
            DB::table('master_employees')->exists()
        ) {
            throw new \RuntimeException(
                'master_employees tidak kosong. Migration dibatalkan untuk mencegah kehilangan data.'
            );
        }

        Schema::dropIfExists('master_employees');

        Schema::create('master_employees', function (Blueprint $table) {
            $table->id();

            $table->string('idsubsls')->nullable();
            $table->string('rt_rw')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('nama_kepala_keluarga')->nullable();
            $table->string('nama')->nullable();

            $table->text('nik_encrypted')->nullable();
            $table->char('nik_lookup', 64)->nullable()->index();

            $table->string('keberadaan_dtsen_label')->nullable();
            $table->string('profesi')->nullable();
            $table->text('profesi_lainnya')->nullable();
            $table->string('status_kerja_label')->nullable();
            $table->string('code_identity')->nullable();
            $table->string('assignment_status_alias')->nullable();
            $table->string('assignment_id')->nullable()->index();
            $table->string('nama_principal')->nullable();
            $table->text('alamat')->nullable();
            $table->string('no_kk', 32)->nullable();

            $table->string('respSE26_assignment_id')->nullable();
            $table->string('respSE26_no_kk', 32)->nullable();
            $table->string('respSE26_nama')->nullable();
            $table->string('respSE26_code_identity')->nullable();
            $table->string('respSE26_keberadaan_klrg')->nullable();

            $table->string('_file_sumber')->nullable();
            $table->unsignedBigInteger('_baris_sumber')->nullable();

            $table->string('name')->nullable();
            $table->string('status')->nullable()->index();

            $table->foreignId('institution_id')
                ->nullable()
                ->constrained('institutions')
                ->nullOnDelete();

            $table->string('instansi')->nullable()->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_employees');
    }
};
