<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_shifts', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('lokasi');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->unsignedInteger('radius_meters')->nullable()->after('longitude');
        });

        Schema::table('activity_shift_attendances', function (Blueprint $table) {
            $table->decimal('absen_latitude', 10, 7)->nullable()->after('photo_path');
            $table->decimal('absen_longitude', 10, 7)->nullable()->after('absen_latitude');
            $table->unsignedInteger('distance_meters')->nullable()->after('absen_longitude');
        });
    }

    public function down(): void
    {
        Schema::table('activity_shift_attendances', function (Blueprint $table) {
            $table->dropColumn(['absen_latitude', 'absen_longitude', 'distance_meters']);
        });

        Schema::table('activity_shifts', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'radius_meters']);
        });
    }
};
