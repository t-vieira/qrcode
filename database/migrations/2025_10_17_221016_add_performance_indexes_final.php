<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Verificar se uma coluna existe
     */
    private function columnExists(string $table, string $column): bool
    {
        try {
            return Schema::hasColumn($table, $column);
        } catch (\Exception $e) {
            return false;
        }
    }

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
     * Criar índice se não existir e coluna existir
     */
    private function createIndexIfExists(string $table, string $column, string $indexName = null): void
    {
        $indexName = $indexName ?: $table . '_' . $column . '_index';
        
        if ($this->columnExists($table, $column) && !$this->indexExists($table, $indexName)) {
            try {
                DB::statement("CREATE INDEX IF NOT EXISTS {$indexName} ON {$table} ({$column})");
                // Created index: {$indexName}
            } catch (\Exception $e) {
                // Failed to create index {$indexName}: {$e->getMessage()}
            }
        }
    }

    /**
     * Criar índice composto se não existir e colunas existirem
     */
    private function createCompositeIndexIfExists(string $table, array $columns, string $indexName): void
    {
        // Verificar se todas as colunas existem
        $allColumnsExist = true;
        foreach ($columns as $column) {
            if (!$this->columnExists($table, $column)) {
                $allColumnsExist = false;
                break;
            }
        }

        if ($allColumnsExist && !$this->indexExists($table, $indexName)) {
            try {
                $columnsStr = implode(', ', $columns);
                DB::statement("CREATE INDEX IF NOT EXISTS {$indexName} ON {$table} ({$columnsStr})");
                // Created composite index: {$indexName}
            } catch (\Exception $e) {
                // Failed to create composite index {$indexName}: {$e->getMessage()}
            }
        }
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Creating performance indexes...

        // Índices para tabela users
        if (Schema::hasTable('users')) {
            $this->createIndexIfExists('users', 'email');
            $this->createIndexIfExists('users', 'subscription_status');
            $this->createIndexIfExists('users', 'trial_ends_at');
            $this->createIndexIfExists('users', 'deleted_at');
            
            // Índice composto
            $this->createCompositeIndexIfExists('users', ['subscription_status', 'trial_ends_at'], 'users_subscription_status_trial_ends_at_index');
        }

        // Índices para tabela subscriptions
        if (Schema::hasTable('subscriptions')) {
            $this->createIndexIfExists('subscriptions', 'user_id');
            $this->createIndexIfExists('subscriptions', 'mp_subscription_id');
            $this->createIndexIfExists('subscriptions', 'status');
            $this->createIndexIfExists('subscriptions', 'current_period_end');
            $this->createCompositeIndexIfExists('subscriptions', ['user_id', 'status'], 'subscriptions_user_id_status_index');
        }

        // Índices para tabela qr_codes
        if (Schema::hasTable('qr_codes')) {
            $this->createIndexIfExists('qr_codes', 'user_id');
            $this->createIndexIfExists('qr_codes', 'short_code');
            $this->createIndexIfExists('qr_codes', 'type');
            $this->createIndexIfExists('qr_codes', 'status');
            $this->createIndexIfExists('qr_codes', 'is_dynamic');
            $this->createIndexIfExists('qr_codes', 'folder_id');
            $this->createIndexIfExists('qr_codes', 'team_id');
            $this->createIndexIfExists('qr_codes', 'created_at');
            $this->createIndexIfExists('qr_codes', 'updated_at');
            $this->createIndexIfExists('qr_codes', 'deleted_at');
            
            // Índices compostos
            $this->createCompositeIndexIfExists('qr_codes', ['user_id', 'status'], 'qr_codes_user_id_status_index');
            $this->createCompositeIndexIfExists('qr_codes', ['user_id', 'type'], 'qr_codes_user_id_type_index');
            $this->createCompositeIndexIfExists('qr_codes', ['user_id', 'folder_id'], 'qr_codes_user_id_folder_id_index');
        }

        // Índices para tabela qr_scans
        if (Schema::hasTable('qr_scans')) {
            $this->createIndexIfExists('qr_scans', 'qr_code_id');
            $this->createIndexIfExists('qr_scans', 'ip_address');
            $this->createIndexIfExists('qr_scans', 'is_unique');
            $this->createIndexIfExists('qr_scans', 'scanned_at');
            $this->createIndexIfExists('qr_scans', 'country');
            $this->createIndexIfExists('qr_scans', 'device_type');
            $this->createIndexIfExists('qr_scans', 'browser');
            $this->createIndexIfExists('qr_scans', 'os');
            
            // Índices compostos
            $this->createCompositeIndexIfExists('qr_scans', ['qr_code_id', 'scanned_at'], 'qr_scans_qr_code_id_scanned_at_index');
            $this->createCompositeIndexIfExists('qr_scans', ['qr_code_id', 'is_unique'], 'qr_scans_qr_code_id_is_unique_index');
            $this->createCompositeIndexIfExists('qr_scans', ['ip_address', 'scanned_at'], 'qr_scans_ip_address_scanned_at_index');
        }

        // Índices para tabela folders
        if (Schema::hasTable('folders')) {
            $this->createIndexIfExists('folders', 'user_id');
            $this->createIndexIfExists('folders', 'slug');
            $this->createIndexIfExists('folders', 'deleted_at');
            $this->createCompositeIndexIfExists('folders', ['user_id', 'slug'], 'folders_user_id_slug_index');
        }

        // Índices para tabela teams
        if (Schema::hasTable('teams')) {
            $this->createIndexIfExists('teams', 'owner_id');
            $this->createIndexIfExists('teams', 'slug');
            $this->createIndexIfExists('teams', 'deleted_at');
            // Remover status da tabela teams pois não existe
        }

        // Índices para tabela team_user
        if (Schema::hasTable('team_user')) {
            $this->createIndexIfExists('team_user', 'team_id');
            $this->createIndexIfExists('team_user', 'user_id');
            $this->createIndexIfExists('team_user', 'role');
            $this->createCompositeIndexIfExists('team_user', ['team_id', 'user_id'], 'team_user_team_id_user_id_index');
        }

        // Índices para tabela custom_domains
        if (Schema::hasTable('custom_domains')) {
            $this->createIndexIfExists('custom_domains', 'user_id');
            $this->createIndexIfExists('custom_domains', 'domain');
            $this->createIndexIfExists('custom_domains', 'status');
            $this->createIndexIfExists('custom_domains', 'is_primary');
            $this->createCompositeIndexIfExists('custom_domains', ['user_id', 'status'], 'custom_domains_user_id_status_index');
        }

        // Índices para tabela support_tickets
        if (Schema::hasTable('support_tickets')) {
            $this->createIndexIfExists('support_tickets', 'user_id');
            $this->createIndexIfExists('support_tickets', 'status');
            $this->createIndexIfExists('support_tickets', 'priority');
            $this->createIndexIfExists('support_tickets', 'category');
            $this->createIndexIfExists('support_tickets', 'created_at');
            $this->createIndexIfExists('support_tickets', 'last_reply_at');
            $this->createCompositeIndexIfExists('support_tickets', ['user_id', 'status'], 'support_tickets_user_id_status_index');
        }

        // Performance indexes creation completed!
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Removing performance indexes...

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

        // Performance indexes removal completed!
    }

    /**
     * Remover índice se existir
     */
    private function dropIndexIfExists(string $table, string $indexName): void
    {
        if ($this->indexExists($table, $indexName)) {
            try {
                DB::statement("DROP INDEX IF EXISTS {$indexName}");
                // Dropped index: {$indexName}
            } catch (\Exception $e) {
                // Failed to drop index {$indexName}: {$e->getMessage()}
            }
        }
    }
};