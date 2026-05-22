<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_shift_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->string('phone', 30);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->unsignedInteger('distance_meters')->nullable();
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index(['phone', 'expires_at']);
            $table->unique(['activity_shift_id', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_attendance_sessions');
    }
};
