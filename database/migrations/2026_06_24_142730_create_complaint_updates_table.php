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
         Schema::create('complaint_updates', function (Blueprint $table) {
        $table->id();
        $table->foreignId('complaint_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // siapa yg update
        $table->enum('status', ['pending', 'verified', 'in_progress', 'resolved', 'rejected']);
        $table->text('note')->nullable(); // catatan update
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaint_updates');
    }
};
