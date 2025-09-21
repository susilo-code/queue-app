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
        Schema::create('queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('queue_type_id')->constrained()->onDelete('cascade');
            $table->string('queue_number');
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->enum('status', ['waiting', 'called', 'served', 'cancelled'])->default('waiting');
            $table->timestamp('called_at')->nullable();
            $table->timestamp('served_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('queue');
    }
};
