<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('due_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('due_period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->decimal('jumlah_bayar', 15, 2)->nullable();
            $table->date('dibayar_pada')->nullable();
            $table->string('status', 20)->default('belum_bayar');
            $table->string('metode', 50)->nullable();
            $table->text('catatan')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['due_period_id', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('due_payments');
    }
};
