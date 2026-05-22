<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('number', 5);
            $table->string('name')->nullable();
            $table->timestamps();

            $table->unique(['organization_id', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rts');
    }
};
