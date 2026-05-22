<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_shift_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_shift_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('tidak_hadir');
            $table->string('photo_path')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamp('absen_pada')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['activity_shift_id', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_shift_attendances');
    }
};
