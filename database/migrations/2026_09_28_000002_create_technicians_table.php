<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * One row per technician, linked 1-to-1 to a `users` row with role = technician.
     * `verification_status` drives the admin approval workflow from the proposal.
     */
    public function up(): void
    {
        Schema::create('technicians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('specialty')->nullable(); // e.g. "Plumbing", "Electrical"
            $table->text('bio')->nullable();
            $table->string('document_path')->nullable(); // uploaded ID / certificate for verification
            $table->enum('verification_status', ['pending', 'approved', 'rejected'])
                ->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('technicians');
    }
};
