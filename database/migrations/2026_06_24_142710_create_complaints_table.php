<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('complaints', function (Blueprint $table) {
        $table->id();
        $table->string('complaint_number')->unique(); // nomor tiket: SPM-2025-XXXX
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('category_id')->constrained()->onDelete('cascade');
        $table->string('title');
        $table->text('description');
        $table->string('location_name'); // nama lokasi tekstual
        $table->decimal('latitude', 10, 8);  // koordinat GPS
        $table->decimal('longitude', 11, 8); // koordinat GPS
        $table->string('photo')->nullable();  // foto bukti
        $table->enum('status', ['pending', 'verified', 'in_progress', 'resolved', 'rejected'])
              ->default('pending');
        $table->text('rejection_reason')->nullable();
        $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
        $table->timestamp('verified_at')->nullable();
        $table->timestamp('resolved_at')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
