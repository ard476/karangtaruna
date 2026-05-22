<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_shifts', function (Blueprint $table) {
            $table->string('qr_token', 64)->nullable()->unique()->after('catatan');
        });

        DB::table('activity_shifts')
            ->whereNull('qr_token')
            ->orderBy('id')
            ->each(function (object $shift): void {
                DB::table('activity_shifts')
                    ->where('id', $shift->id)
                    ->update(['qr_token' => Str::random(40)]);
            });
    }

    public function down(): void
    {
        Schema::table('activity_shifts', function (Blueprint $table) {
            $table->dropUnique(['qr_token']);
            $table->dropColumn('qr_token');
        });
    }
};
