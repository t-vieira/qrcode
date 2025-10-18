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
        Schema::create('qr_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('qr_code_id')->constrained()->onDelete('cascade');
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->enum('device_type', ['mobile', 'tablet', 'desktop'])->nullable();
            $table->string('os')->nullable();
            $table->string('browser')->nullable();
            $table->string('country', 2)->nullable();
            $table->string('city')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('is_unique')->default(false);
            $table->timestamp('scanned_at');
            $table->timestamps();
            
            $table->index(['qr_code_id', 'scanned_at']);
            $table->index(['qr_code_id', 'is_unique']);
            $table->index('ip_address');
            $table->index('scanned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_scans');
    }
};
