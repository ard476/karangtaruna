<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_shift_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('activity_shift_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->string('catatan', 255)->nullable();
            $table->timestamps();

            $table->unique(['activity_shift_id', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_shift_assignments');
    }
};
