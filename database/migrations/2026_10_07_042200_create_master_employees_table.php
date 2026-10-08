<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_employees', function (Blueprint $table) {
            $table->id();

            $table->string('assignment_id')->nullable();
            $table->string('level_6_id')->nullable();
            $table->string('level_2_name')->nullable();
            $table->string('level_3_name')->nullable();
            $table->string('level_4_name')->nullable();
            $table->string('level_6_name')->nullable();
            $table->text('nik_encrypted');
            $table->char('nik_lookup', 64);
            $table->string('name');
            $table->string('status')->nullable();
            $table->string('institution')->nullable();
            $table->timestamps();
            $table->index('nik_lookup');
            $table->index('status');
            $table->index('institution');
            $table->index('assignment_id');
            $table->index('level_6_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_employees');
    }
};
