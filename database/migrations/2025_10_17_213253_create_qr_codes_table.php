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
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('folder_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('short_code')->unique();
            $table->enum('type', [
                'url', 'vcard', 'business', 'coupon', 'text', 'mp3', 'pdf', 'image', 
                'video', 'app', 'menu', 'email', 'phone', 'sms', 'social', 'wifi', 
                'event', 'location', 'feedback', 'crypto'
            ]);
            $table->boolean('is_dynamic')->default(false);
            $table->json('content');
            $table->json('design')->nullable();
            $table->string('custom_domain')->nullable();
            $table->integer('resolution')->default(300);
            $table->enum('format', ['png', 'jpg', 'svg', 'eps'])->default('png');
            $table->string('file_path')->nullable();
            $table->enum('status', ['active', 'archived', 'deleted'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['user_id', 'status']);
            $table->index(['team_id', 'status']);
            $table->index(['folder_id', 'status']);
            $table->index('short_code');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_codes');
    }
};
