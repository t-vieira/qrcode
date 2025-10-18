<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanDuplicateIndexes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clean-indexes {--force : Force cleanup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean duplicate indexes and fix migration issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Cleaning duplicate indexes...');
        $this->newLine();

        if (!$this->option('force')) {
            if (!$this->confirm('This will remove duplicate indexes. Continue?')) {
                $this->info('Operation cancelled.');
                return;
            }
        }

        // Listar todos os índices
        $this->listAllIndexes();

        // Remover índices duplicados
        $this->removeDuplicateIndexes();

        // Verificar se ainda há problemas
        $this->checkRemainingIssues();

        $this->newLine();
        $this->info('✅ Index cleanup completed!');
    }

    /**
     * Listar todos os índices
     */
    private function listAllIndexes(): void
    {
        $this->info('📋 Current indexes:');
        $this->newLine();

        try {
            $indexes = DB::select("
                SELECT 
                    schemaname,
                    tablename,
                    indexname,
                    indexdef
                FROM pg_indexes 
                WHERE schemaname = 'public'
                ORDER BY tablename, indexname
            ");

            $currentTable = '';
            foreach ($indexes as $index) {
                if ($currentTable !== $index->tablename) {
                    $this->line("Table: {$index->tablename}");
                    $currentTable = $index->tablename;
                }
                $this->line("  - {$index->indexname}");
            }

        } catch (\Exception $e) {
            $this->error('Error listing indexes: ' . $e->getMessage());
        }

        $this->newLine();
    }

    /**
     * Remover índices duplicados
     */
    private function removeDuplicateIndexes(): void
    {
        $this->info('🗑️  Removing duplicate indexes...');

        // Índices problemáticos conhecidos
        $problematicIndexes = [
            'subscriptions_mp_subscription_id_index',
            'subscriptions_user_id_index',
            'subscriptions_status_index',
            'subscriptions_current_period_end_index',
            'subscriptions_user_id_status_index',
        ];

        foreach ($problematicIndexes as $indexName) {
            $this->removeIndexIfExists($indexName);
        }

        $this->line('Duplicate indexes removed.');
    }

    /**
     * Remover índice se existir
     */
    private function removeIndexIfExists(string $indexName): void
    {
        try {
            $exists = DB::select("
                SELECT indexname 
                FROM pg_indexes 
                WHERE indexname = ?
            ", [$indexName]);

            if (!empty($exists)) {
                DB::statement("DROP INDEX IF EXISTS {$indexName}");
                $this->line("  ✅ Removed: {$indexName}");
            } else {
                $this->line("  ⚠️  Not found: {$indexName}");
            }

        } catch (\Exception $e) {
            $this->line("  ❌ Error removing {$indexName}: " . $e->getMessage());
        }
    }

    /**
     * Verificar se ainda há problemas
     */
    private function checkRemainingIssues(): void
    {
        $this->info('🔍 Checking for remaining issues...');

        try {
            // Verificar se há índices duplicados
            $duplicates = DB::select("
                SELECT 
                    tablename,
                    COUNT(*) as count
                FROM pg_indexes 
                WHERE schemaname = 'public'
                GROUP BY tablename
                HAVING COUNT(*) > 10
                ORDER BY count DESC
            ");

            if (!empty($duplicates)) {
                $this->warn('Tables with many indexes:');
                foreach ($duplicates as $duplicate) {
                    $this->line("  - {$duplicate->tablename}: {$duplicate->count} indexes");
                }
            } else {
                $this->info('✅ No excessive indexes found.');
            }

        } catch (\Exception $e) {
            $this->error('Error checking issues: ' . $e->getMessage());
        }
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
                $this->warn('No migrations found in database.');
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
     * Sugerir próximos passos
     */
    public function suggestNextSteps(): void
    {
        $this->info('💡 Next steps:');
        $this->newLine();

        $this->line('1. Run migrations again:');
        $this->line('   php artisan migrate --force');
        $this->newLine();

        $this->line('2. If still having issues, reset migrations:');
        $this->line('   php artisan migrate:reset');
        $this->line('   php artisan migrate --force');
        $this->newLine();

        $this->line('3. For a fresh start (WARNING: This will delete all data):');
        $this->line('   php artisan migrate:fresh --seed --force');
        $this->newLine();

        $this->line('4. Check migration status:');
        $this->line('   php artisan migrate:status');
        $this->newLine();

        $this->line('5. Run diagnostics:');
        $this->line('   php artisan migrations:fix');
    }
}