<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->date('last_maintenance_at')->nullable()->after('purchased_at');
            $table->date('last_calibration_at')->nullable()->after('last_maintenance_at');
            $table->string('photo_path')->nullable()->after('last_calibration_at');
        });
    }

    public function down(): void
    {
        Schema::table('devices', function (Blueprint $table) {
            $table->dropColumn(['last_maintenance_at', 'last_calibration_at', 'photo_path']);
        });
    }
};
