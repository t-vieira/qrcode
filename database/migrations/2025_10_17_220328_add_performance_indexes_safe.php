<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Verificar se um índice existe
     */
    private function indexExists(string $table, string $indexName): bool
    {
        try {
            $indexes = DB::select("
                SELECT indexname 
                FROM pg_indexes 
                WHERE tablename = ? AND indexname = ?
            ", [$table, $indexName]);

            return !empty($indexes);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Criar índice se não existir
     */
    private function createIndexIfNotExists(string $table, string $column, string $indexName = null): void
    {
        $indexName = $indexName ?: $table . '_' . $column . '_index';
        
        if (!$this->indexExists($table, $indexName)) {
            DB::statement("CREATE INDEX IF NOT EXISTS {$indexName} ON {$table} ({$column})");
        }
    }

    /**
     * Criar índice composto se não existir
     */
    private function createCompositeIndexIfNotExists(string $table, array $columns, string $indexName): void
    {
        if (!$this->indexExists($table, $indexName)) {
            $columnsStr = implode(', ', $columns);
            DB::statement("CREATE INDEX IF NOT EXISTS {$indexName} ON {$table} ({$columnsStr})");
        }
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Índices para tabela users
        if (Schema::hasTable('users')) {
            $this->createIndexIfNotExists('users', 'email');
            
            if (Schema::hasColumn('users', 'subscription_status')) {
                $this->createIndexIfNotExists('users', 'subscription_status');
            }
            if (Schema::hasColumn('users', 'trial_ends_at')) {
                $this->createIndexIfNotExists('users', 'trial_ends_at');
            }
            if (Schema::hasColumn('users', 'deleted_at')) {
                $this->createIndexIfNotExists('users', 'deleted_at');
            }
            
            // Índice composto
            if (Schema::hasColumn('users', 'subscription_status') && Schema::hasColumn('users', 'trial_ends_at')) {
                $this->createCompositeIndexIfNotExists('users', ['subscription_status', 'trial_ends_at'], 'users_subscription_status_trial_ends_at_index');
            }
        }

        // Índices para tabela subscriptions
        if (Schema::hasTable('subscriptions')) {
            $this->createIndexIfNotExists('subscriptions', 'user_id');
            $this->createIndexIfNotExists('subscriptions', 'mp_subscription_id');
            $this->createIndexIfNotExists('subscriptions', 'status');
            $this->createIndexIfNotExists('subscriptions', 'current_period_end');
            $this->createCompositeIndexIfNotExists('subscriptions', ['user_id', 'status'], 'subscriptions_user_id_status_index');
        }

        // Índices para tabela qr_codes
        if (Schema::hasTable('qr_codes')) {
            $this->createIndexIfNotExists('qr_codes', 'user_id');
            $this->createIndexIfNotExists('qr_codes', 'short_code');
            $this->createIndexIfNotExists('qr_codes', 'type');
            $this->createIndexIfNotExists('qr_codes', 'status');
            $this->createIndexIfNotExists('qr_codes', 'is_dynamic');
            $this->createIndexIfNotExists('qr_codes', 'folder_id');
            $this->createIndexIfNotExists('qr_codes', 'team_id');
            $this->createIndexIfNotExists('qr_codes', 'created_at');
            $this->createIndexIfNotExists('qr_codes', 'updated_at');
            $this->createIndexIfNotExists('qr_codes', 'deleted_at');
            
            // Índices compostos
            $this->createCompositeIndexIfNotExists('qr_codes', ['user_id', 'status'], 'qr_codes_user_id_status_index');
            $this->createCompositeIndexIfNotExists('qr_codes', ['user_id', 'type'], 'qr_codes_user_id_type_index');
            $this->createCompositeIndexIfNotExists('qr_codes', ['user_id', 'folder_id'], 'qr_codes_user_id_folder_id_index');
        }

        // Índices para tabela qr_scans
        if (Schema::hasTable('qr_scans')) {
            $this->createIndexIfNotExists('qr_scans', 'qr_code_id');
            $this->createIndexIfNotExists('qr_scans', 'ip_address');
            $this->createIndexIfNotExists('qr_scans', 'is_unique');
            $this->createIndexIfNotExists('qr_scans', 'scanned_at');
            $this->createIndexIfNotExists('qr_scans', 'country');
            $this->createIndexIfNotExists('qr_scans', 'device_type');
            $this->createIndexIfNotExists('qr_scans', 'browser');
            $this->createIndexIfNotExists('qr_scans', 'os');
            
            // Índices compostos
            $this->createCompositeIndexIfNotExists('qr_scans', ['qr_code_id', 'scanned_at'], 'qr_scans_qr_code_id_scanned_at_index');
            $this->createCompositeIndexIfNotExists('qr_scans', ['qr_code_id', 'is_unique'], 'qr_scans_qr_code_id_is_unique_index');
            $this->createCompositeIndexIfNotExists('qr_scans', ['ip_address', 'scanned_at'], 'qr_scans_ip_address_scanned_at_index');
        }

        // Índices para tabela folders
        if (Schema::hasTable('folders')) {
            $this->createIndexIfNotExists('folders', 'user_id');
            $this->createIndexIfNotExists('folders', 'slug');
            $this->createIndexIfNotExists('folders', 'deleted_at');
            $this->createCompositeIndexIfNotExists('folders', ['user_id', 'slug'], 'folders_user_id_slug_index');
        }

        // Índices para tabela teams
        if (Schema::hasTable('teams')) {
            $this->createIndexIfNotExists('teams', 'owner_id');
            $this->createIndexIfNotExists('teams', 'slug');
            $this->createIndexIfNotExists('teams', 'status');
            $this->createIndexIfNotExists('teams', 'deleted_at');
        }

        // Índices para tabela team_user
        if (Schema::hasTable('team_user')) {
            $this->createIndexIfNotExists('team_user', 'team_id');
            $this->createIndexIfNotExists('team_user', 'user_id');
            $this->createIndexIfNotExists('team_user', 'role');
            $this->createCompositeIndexIfNotExists('team_user', ['team_id', 'user_id'], 'team_user_team_id_user_id_index');
        }

        // Índices para tabela custom_domains
        if (Schema::hasTable('custom_domains')) {
            $this->createIndexIfNotExists('custom_domains', 'user_id');
            $this->createIndexIfNotExists('custom_domains', 'domain');
            $this->createIndexIfNotExists('custom_domains', 'status');
            $this->createIndexIfNotExists('custom_domains', 'is_primary');
            $this->createCompositeIndexIfNotExists('custom_domains', ['user_id', 'status'], 'custom_domains_user_id_status_index');
        }

        // Índices para tabela support_tickets
        if (Schema::hasTable('support_tickets')) {
            $this->createIndexIfNotExists('support_tickets', 'user_id');
            $this->createIndexIfNotExists('support_tickets', 'status');
            $this->createIndexIfNotExists('support_tickets', 'priority');
            $this->createIndexIfNotExists('support_tickets', 'category');
            $this->createIndexIfNotExists('support_tickets', 'created_at');
            $this->createIndexIfNotExists('support_tickets', 'last_reply_at');
            $this->createCompositeIndexIfNotExists('support_tickets', ['user_id', 'status'], 'support_tickets_user_id_status_index');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remover índices de users
        if (Schema::hasTable('users')) {
            $this->dropIndexIfExists('users', 'users_email_index');
            $this->dropIndexIfExists('users', 'users_subscription_status_index');
            $this->dropIndexIfExists('users', 'users_trial_ends_at_index');
            $this->dropIndexIfExists('users', 'users_deleted_at_index');
            $this->dropIndexIfExists('users', 'users_subscription_status_trial_ends_at_index');
        }

        // Remover índices de subscriptions
        if (Schema::hasTable('subscriptions')) {
            $this->dropIndexIfExists('subscriptions', 'subscriptions_user_id_index');
            $this->dropIndexIfExists('subscriptions', 'subscriptions_mp_subscription_id_index');
            $this->dropIndexIfExists('subscriptions', 'subscriptions_status_index');
            $this->dropIndexIfExists('subscriptions', 'subscriptions_current_period_end_index');
            $this->dropIndexIfExists('subscriptions', 'subscriptions_user_id_status_index');
        }

        // Remover índices de qr_codes
        if (Schema::hasTable('qr_codes')) {
            $this->dropIndexIfExists('qr_codes', 'qr_codes_user_id_index');
            $this->dropIndexIfExists('qr_codes', 'qr_codes_short_code_index');
            $this->dropIndexIfExists('qr_codes', 'qr_codes_type_index');
            $this->dropIndexIfExists('qr_codes', 'qr_codes_status_index');
            $this->dropIndexIfExists('qr_codes', 'qr_codes_is_dynamic_index');
            $this->dropIndexIfExists('qr_codes', 'qr_codes_folder_id_index');
            $this->dropIndexIfExists('qr_codes', 'qr_codes_team_id_index');
            $this->dropIndexIfExists('qr_codes', 'qr_codes_created_at_index');
            $this->dropIndexIfExists('qr_codes', 'qr_codes_updated_at_index');
            $this->dropIndexIfExists('qr_codes', 'qr_codes_deleted_at_index');
            $this->dropIndexIfExists('qr_codes', 'qr_codes_user_id_status_index');
            $this->dropIndexIfExists('qr_codes', 'qr_codes_user_id_type_index');
            $this->dropIndexIfExists('qr_codes', 'qr_codes_user_id_folder_id_index');
        }

        // Remover índices de qr_scans
        if (Schema::hasTable('qr_scans')) {
            $this->dropIndexIfExists('qr_scans', 'qr_scans_qr_code_id_index');
            $this->dropIndexIfExists('qr_scans', 'qr_scans_ip_address_index');
            $this->dropIndexIfExists('qr_scans', 'qr_scans_is_unique_index');
            $this->dropIndexIfExists('qr_scans', 'qr_scans_scanned_at_index');
            $this->dropIndexIfExists('qr_scans', 'qr_scans_country_index');
            $this->dropIndexIfExists('qr_scans', 'qr_scans_device_type_index');
            $this->dropIndexIfExists('qr_scans', 'qr_scans_browser_index');
            $this->dropIndexIfExists('qr_scans', 'qr_scans_os_index');
            $this->dropIndexIfExists('qr_scans', 'qr_scans_qr_code_id_scanned_at_index');
            $this->dropIndexIfExists('qr_scans', 'qr_scans_qr_code_id_is_unique_index');
            $this->dropIndexIfExists('qr_scans', 'qr_scans_ip_address_scanned_at_index');
        }

        // Remover índices de folders
        if (Schema::hasTable('folders')) {
            $this->dropIndexIfExists('folders', 'folders_user_id_index');
            $this->dropIndexIfExists('folders', 'folders_slug_index');
            $this->dropIndexIfExists('folders', 'folders_deleted_at_index');
            $this->dropIndexIfExists('folders', 'folders_user_id_slug_index');
        }

        // Remover índices de teams
        if (Schema::hasTable('teams')) {
            $this->dropIndexIfExists('teams', 'teams_owner_id_index');
            $this->dropIndexIfExists('teams', 'teams_slug_index');
            $this->dropIndexIfExists('teams', 'teams_status_index');
            $this->dropIndexIfExists('teams', 'teams_deleted_at_index');
        }

        // Remover índices de team_user
        if (Schema::hasTable('team_user')) {
            $this->dropIndexIfExists('team_user', 'team_user_team_id_index');
            $this->dropIndexIfExists('team_user', 'team_user_user_id_index');
            $this->dropIndexIfExists('team_user', 'team_user_role_index');
            $this->dropIndexIfExists('team_user', 'team_user_team_id_user_id_index');
        }

        // Remover índices de custom_domains
        if (Schema::hasTable('custom_domains')) {
            $this->dropIndexIfExists('custom_domains', 'custom_domains_user_id_index');
            $this->dropIndexIfExists('custom_domains', 'custom_domains_domain_index');
            $this->dropIndexIfExists('custom_domains', 'custom_domains_status_index');
            $this->dropIndexIfExists('custom_domains', 'custom_domains_is_primary_index');
            $this->dropIndexIfExists('custom_domains', 'custom_domains_user_id_status_index');
        }

        // Remover índices de support_tickets
        if (Schema::hasTable('support_tickets')) {
            $this->dropIndexIfExists('support_tickets', 'support_tickets_user_id_index');
            $this->dropIndexIfExists('support_tickets', 'support_tickets_status_index');
            $this->dropIndexIfExists('support_tickets', 'support_tickets_priority_index');
            $this->dropIndexIfExists('support_tickets', 'support_tickets_category_index');
            $this->dropIndexIfExists('support_tickets', 'support_tickets_created_at_index');
            $this->dropIndexIfExists('support_tickets', 'support_tickets_last_reply_at_index');
            $this->dropIndexIfExists('support_tickets', 'support_tickets_user_id_status_index');
        }
    }

    /**
     * Remover índice se existir
     */
    private function dropIndexIfExists(string $table, string $indexName): void
    {
        if ($this->indexExists($table, $indexName)) {
            try {
                DB::statement("DROP INDEX IF EXISTS {$indexName}");
            } catch (\Exception $e) {
                // Ignorar erros ao remover índices
            }
        }
    }
};