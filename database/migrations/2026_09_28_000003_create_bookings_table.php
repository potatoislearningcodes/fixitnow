<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Core booking record: a customer requesting a service from a technician.
     * `status` is updated manually by the technician (see proposal limitations —
     * no live GPS tracking, status tracking only).
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('technician_id')->nullable()->constrained('technicians')->nullOnDelete();
            $table->string('service_type'); // e.g. "Plumbing repair"
            $table->text('description')->nullable();
            $table->string('address');
            $table->dateTime('scheduled_at');
            $table->enum('status', ['pending', 'accepted', 'in_progress', 'completed', 'cancelled'])
                ->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
