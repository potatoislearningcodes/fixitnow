<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Renames gateway_reference -> reference_number to match the
     * "reference number is recorded" language from the system guide, and
     * adds the payment method. Safe to rename: nothing in the app reads
     * gateway_reference yet, since payment recording didn't exist before
     * this week.
     */
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('gateway_reference', 'reference_number');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->enum('payment_method', ['cash', 'gcash', 'bank_transfer'])
                ->nullable()
                ->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('payment_method');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('reference_number', 'gateway_reference');
        });
    }
};
