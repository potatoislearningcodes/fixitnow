<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * FixItNow has three user roles sharing the base `users` table:
     * customer, technician, admin. The `technicians` table (see the
     * next migration) holds technician-specific fields.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['customer', 'technician', 'admin'])
                ->default('customer')
                ->after('email');
            $table->string('phone')->nullable()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'phone']);
        });
    }
};
