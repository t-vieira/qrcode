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
        Schema::table('users', function (Blueprint $table) {
            // Índices para autenticação e consultas frequentes
            $table->index('email');
            $table->index('subscription_status');
            $table->index('trial_ends_at');
            $table->index(['subscription_status', 'trial_ends_at']);
            $table->index('deleted_at');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            // Índices para consultas de assinatura
            $table->index('user_id');
            $table->index('mp_subscription_id');
            $table->index('status');
            $table->index(['user_id', 'status']);
            $table->index('current_period_end');
        });

        Schema::table('qr_codes', function (Blueprint $table) {
            // Índices para consultas de QR Codes
            $table->index('user_id');
            $table->index('short_code');
            $table->index('type');
            $table->index('status');
            $table->index('is_dynamic');
            $table->index('folder_id');
            $table->index('team_id');
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'folder_id']);
            $table->index('created_at');
            $table->index('updated_at');
            $table->index('deleted_at');
        });

        Schema::table('qr_scans', function (Blueprint $table) {
            // Índices para consultas de scans e estatísticas
            $table->index('qr_code_id');
            $table->index('ip_address');
            $table->index('is_unique');
            $table->index('scanned_at');
            $table->index(['qr_code_id', 'scanned_at']);
            $table->index(['qr_code_id', 'is_unique']);
            $table->index(['ip_address', 'scanned_at']);
            $table->index('country');
            $table->index('device_type');
            $table->index('browser');
            $table->index('os');
        });

        Schema::table('folders', function (Blueprint $table) {
            // Índices para consultas de pastas
            $table->index('user_id');
            $table->index('slug');
            $table->index(['user_id', 'slug']);
            $table->index('deleted_at');
        });

        Schema::table('teams', function (Blueprint $table) {
            // Índices para consultas de equipes
            $table->index('owner_id');
            $table->index('slug');
            $table->index('status');
            $table->index('deleted_at');
        });

        Schema::table('team_user', function (Blueprint $table) {
            // Índices para consultas de membros de equipe
            $table->index('team_id');
            $table->index('user_id');
            $table->index(['team_id', 'user_id']);
            $table->index('role');
        });

        Schema::table('custom_domains', function (Blueprint $table) {
            // Índices para consultas de domínios customizados
            $table->index('user_id');
            $table->index('domain');
            $table->index('status');
            $table->index('is_primary');
            $table->index(['user_id', 'status']);
        });

        Schema::table('support_tickets', function (Blueprint $table) {
            // Índices para consultas de tickets de suporte
            $table->index('user_id');
            $table->index('status');
            $table->index('priority');
            $table->index('category');
            $table->index(['user_id', 'status']);
            $table->index('created_at');
            $table->index('last_reply_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email']);
            $table->dropIndex(['subscription_status']);
            $table->dropIndex(['trial_ends_at']);
            $table->dropIndex(['subscription_status', 'trial_ends_at']);
            $table->dropIndex(['deleted_at']);
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['mp_subscription_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['current_period_end']);
        });

        Schema::table('qr_codes', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['short_code']);
            $table->dropIndex(['type']);
            $table->dropIndex(['status']);
            $table->dropIndex(['is_dynamic']);
            $table->dropIndex(['folder_id']);
            $table->dropIndex(['team_id']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['user_id', 'type']);
            $table->dropIndex(['user_id', 'folder_id']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['updated_at']);
            $table->dropIndex(['deleted_at']);
        });

        Schema::table('qr_scans', function (Blueprint $table) {
            $table->dropIndex(['qr_code_id']);
            $table->dropIndex(['ip_address']);
            $table->dropIndex(['is_unique']);
            $table->dropIndex(['scanned_at']);
            $table->dropIndex(['qr_code_id', 'scanned_at']);
            $table->dropIndex(['qr_code_id', 'is_unique']);
            $table->dropIndex(['ip_address', 'scanned_at']);
            $table->dropIndex(['country']);
            $table->dropIndex(['device_type']);
            $table->dropIndex(['browser']);
            $table->dropIndex(['os']);
        });

        Schema::table('folders', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['slug']);
            $table->dropIndex(['user_id', 'slug']);
            $table->dropIndex(['deleted_at']);
        });

        Schema::table('teams', function (Blueprint $table) {
            $table->dropIndex(['owner_id']);
            $table->dropIndex(['slug']);
            $table->dropIndex(['status']);
            $table->dropIndex(['deleted_at']);
        });

        Schema::table('team_user', function (Blueprint $table) {
            $table->dropIndex(['team_id']);
            $table->dropIndex(['user_id']);
            $table->dropIndex(['team_id', 'user_id']);
            $table->dropIndex(['role']);
        });

        Schema::table('custom_domains', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['domain']);
            $table->dropIndex(['status']);
            $table->dropIndex(['is_primary']);
            $table->dropIndex(['user_id', 'status']);
        });

        Schema::table('support_tickets', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['priority']);
            $table->dropIndex(['category']);
            $table->dropIndex(['user_id', 'status']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['last_reply_at']);
        });
    }
};