<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('dusun');
            $table->string('rw_number', 5);
            $table->string('rw_name')->nullable();
            $table->string('desa');
            $table->string('kecamatan');
            $table->string('kabupaten');
            $table->text('alamat_lengkap')->nullable();
            $table->unsignedSmallInteger('tahun_berdiri')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
