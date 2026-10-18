<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Additive only — existing bookings from earlier testing keep working;
     * these new columns are nullable so nothing breaks on old rows.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('building_type', ['residential', 'institutional_business', 'hospitality'])
                ->nullable()
                ->after('customer_id');
            $table->enum('service_group', ['repair', 'maintenance'])
                ->nullable()
                ->after('service_type');
            $table->text('rooms')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['building_type', 'service_group', 'rooms']);
        });
    }
};
