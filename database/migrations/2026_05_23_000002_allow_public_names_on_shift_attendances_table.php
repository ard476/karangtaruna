<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_shift_attendances', function (Blueprint $table) {
            $table->string('public_name')->nullable()->after('member_id');
        });

        DB::statement('ALTER TABLE activity_shift_attendances ALTER COLUMN member_id DROP NOT NULL');
    }

    public function down(): void
    {
        Schema::table('activity_shift_attendances', function (Blueprint $table) {
            $table->dropColumn('public_name');
        });

        DB::statement('ALTER TABLE activity_shift_attendances ALTER COLUMN member_id SET NOT NULL');
    }
};
