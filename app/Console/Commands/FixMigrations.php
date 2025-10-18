<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrations:fix {--force : Force fix without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix migration issues and ensure proper order';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Fixing migration issues...');
        $this->newLine();

        // Verificar se as tabelas existem
        $this->checkTables();

        // Verificar se as colunas existem
        $this->checkColumns();

        // Verificar se os índices existem
        $this->checkIndexes();

        // Sugerir correções
        $this->suggestFixes();

        $this->newLine();
        $this->info('✅ Migration check completed!');
    }

    /**
     * Verificar se as tabelas existem
     */
    private function checkTables(): void
    {
        $this->info('📋 Checking tables...');

        $requiredTables = [
            'users',
            'subscriptions',
            'teams',
            'team_user',
            'folders',
            'qr_codes',
            'qr_scans',
            'custom_domains',
            'support_tickets',
            'personal_access_tokens',
            'permissions',
            'roles',
            'model_has_permissions',
            'model_has_roles',
            'role_has_permissions',
        ];

        $missingTables = [];
        $existingTables = [];

        foreach ($requiredTables as $table) {
            if (Schema::hasTable($table)) {
                $existingTables[] = $table;
                $this->line("  ✅ {$table}");
            } else {
                $missingTables[] = $table;
                $this->line("  ❌ {$table}");
            }
        }

        if (!empty($missingTables)) {
            $this->warn("Missing tables: " . implode(', ', $missingTables));
        }

        $this->newLine();
    }

    /**
     * Verificar se as colunas existem
     */
    private function checkColumns(): void
    {
        $this->info('📊 Checking columns...');

        $requiredColumns = [
            'users' => ['subscription_status', 'trial_ends_at', 'subscription_id', 'deleted_at'],
            'subscriptions' => ['user_id', 'mp_subscription_id', 'status', 'plan_name', 'amount'],
            'qr_codes' => ['user_id', 'short_code', 'type', 'is_dynamic', 'content', 'design'],
            'qr_scans' => ['qr_code_id', 'ip_address', 'device_type', 'is_unique', 'scanned_at'],
        ];

        foreach ($requiredColumns as $table => $columns) {
            if (!Schema::hasTable($table)) {
                $this->line("  ⚠️  Table {$table} does not exist, skipping columns check");
                continue;
            }

            $this->line("  Table: {$table}");
            foreach ($columns as $column) {
                if (Schema::hasColumn($table, $column)) {
                    $this->line("    ✅ {$column}");
                } else {
                    $this->line("    ❌ {$column}");
                }
            }
        }

        $this->newLine();
    }

    /**
     * Verificar se os índices existem
     */
    private function checkIndexes(): void
    {
        $this->info('🔍 Checking indexes...');

        $requiredIndexes = [
            'users' => ['users_email_index', 'users_subscription_status_index', 'users_trial_ends_at_index'],
            'subscriptions' => ['subscriptions_user_id_index', 'subscriptions_mp_subscription_id_index'],
            'qr_codes' => ['qr_codes_user_id_index', 'qr_codes_short_code_index', 'qr_codes_type_index'],
            'qr_scans' => ['qr_scans_qr_code_id_index', 'qr_scans_ip_address_index', 'qr_scans_scanned_at_index'],
        ];

        foreach ($requiredIndexes as $table => $indexes) {
            if (!Schema::hasTable($table)) {
                $this->line("  ⚠️  Table {$table} does not exist, skipping indexes check");
                continue;
            }

            $this->line("  Table: {$table}");
            foreach ($indexes as $index) {
                if ($this->indexExists($table, $index)) {
                    $this->line("    ✅ {$index}");
                } else {
                    $this->line("    ❌ {$index}");
                }
            }
        }

        $this->newLine();
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
     * Sugerir correções
     */
    private function suggestFixes(): void
    {
        $this->info('💡 Suggested fixes:');
        $this->newLine();

        $this->line('1. If tables are missing, run:');
        $this->line('   php artisan migrate --force');
        $this->newLine();

        $this->line('2. If columns are missing, run:');
        $this->line('   php artisan migrate:refresh --force');
        $this->newLine();

        $this->line('3. If indexes are missing, run:');
        $this->line('   php artisan migrate --force');
        $this->newLine();

        $this->line('4. For a fresh start (WARNING: This will delete all data):');
        $this->line('   php artisan migrate:fresh --seed --force');
        $this->newLine();

        $this->line('5. Check migration status:');
        $this->line('   php artisan migrate:status');
        $this->newLine();

        $this->line('6. Check for pending migrations:');
        $this->line('   php artisan migrate:status | grep "Pending"');
    }

    /**
     * Verificar status das migrations
     */
    public function checkMigrationStatus(): void
    {
        $this->info('📋 Migration Status:');
        $this->newLine();

        try {
            $migrations = DB::table('migrations')->get();
            
            if ($migrations->isEmpty()) {
                $this->warn('No migrations found in database. Run: php artisan migrate --force');
                return;
            }

            $this->line('Executed migrations:');
            foreach ($migrations as $migration) {
                $this->line("  ✅ {$migration->migration}");
            }

        } catch (\Exception $e) {
            $this->error('Error checking migration status: ' . $e->getMessage());
        }
    }

    /**
     * Verificar se há migrations pendentes
     */
    public function checkPendingMigrations(): void
    {
        $this->info('⏳ Checking for pending migrations...');
        $this->newLine();

        try {
            $pendingMigrations = $this->getPendingMigrations();
            
            if (empty($pendingMigrations)) {
                $this->info('✅ No pending migrations found');
                return;
            }

            $this->warn('Pending migrations:');
            foreach ($pendingMigrations as $migration) {
                $this->line("  ⏳ {$migration}");
            }

            $this->newLine();
            $this->line('Run: php artisan migrate --force');

        } catch (\Exception $e) {
            $this->error('Error checking pending migrations: ' . $e->getMessage());
        }
    }

    /**
     * Obter migrations pendentes
     */
    private function getPendingMigrations(): array
    {
        $migrationFiles = glob(database_path('migrations/*.php'));
        $executedMigrations = DB::table('migrations')->pluck('migration')->toArray();
        
        $pendingMigrations = [];
        
        foreach ($migrationFiles as $file) {
            $migrationName = basename($file, '.php');
            if (!in_array($migrationName, $executedMigrations)) {
                $pendingMigrations[] = $migrationName;
            }
        }
        
        return $pendingMigrations;
    }
}