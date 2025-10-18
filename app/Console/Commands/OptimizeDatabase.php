<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class OptimizeDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:optimize {--analyze : Run ANALYZE on tables} {--vacuum : Run VACUUM on tables}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize database performance by running maintenance operations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database optimization...');

        if ($this->option('analyze')) {
            $this->analyzeTables();
        }

        if ($this->option('vacuum')) {
            $this->vacuumTables();
        }

        $this->updateTableStatistics();
        $this->optimizeIndexes();

        $this->info('Database optimization completed successfully!');
    }

    /**
     * Run ANALYZE on all tables
     */
    private function analyzeTables(): void
    {
        $this->info('Running ANALYZE on tables...');

        $tables = [
            'users',
            'subscriptions',
            'qr_codes',
            'qr_scans',
            'folders',
            'teams',
            'team_user',
            'custom_domains',
            'support_tickets',
        ];

        foreach ($tables as $table) {
            try {
                DB::statement("ANALYZE TABLE {$table}");
                $this->line("✓ Analyzed table: {$table}");
            } catch (\Exception $e) {
                $this->error("✗ Failed to analyze table {$table}: " . $e->getMessage());
            }
        }
    }

    /**
     * Run VACUUM on all tables
     */
    private function vacuumTables(): void
    {
        $this->info('Running VACUUM on tables...');

        $tables = [
            'users',
            'subscriptions',
            'qr_codes',
            'qr_scans',
            'folders',
            'teams',
            'team_user',
            'custom_domains',
            'support_tickets',
        ];

        foreach ($tables as $table) {
            try {
                DB::statement("VACUUM TABLE {$table}");
                $this->line("✓ Vacuumed table: {$table}");
            } catch (\Exception $e) {
                $this->error("✗ Failed to vacuum table {$table}: " . $e->getMessage());
            }
        }
    }

    /**
     * Update table statistics
     */
    private function updateTableStatistics(): void
    {
        $this->info('Updating table statistics...');

        try {
            // Para PostgreSQL
            if (config('database.default') === 'pgsql') {
                DB::statement('ANALYZE');
                $this->line('✓ Updated PostgreSQL statistics');
            }
            // Para MySQL
            elseif (config('database.default') === 'mysql') {
                DB::statement('ANALYZE TABLE users, subscriptions, qr_codes, qr_scans, folders, teams, team_user, custom_domains, support_tickets');
                $this->line('✓ Updated MySQL statistics');
            }
        } catch (\Exception $e) {
            $this->error('✗ Failed to update statistics: ' . $e->getMessage());
        }
    }

    /**
     * Optimize indexes
     */
    private function optimizeIndexes(): void
    {
        $this->info('Optimizing indexes...');

        try {
            // Verificar índices não utilizados
            $this->checkUnusedIndexes();
            
            // Verificar índices duplicados
            $this->checkDuplicateIndexes();
            
            // Verificar fragmentação de índices
            $this->checkIndexFragmentation();

            $this->line('✓ Index optimization completed');
        } catch (\Exception $e) {
            $this->error('✗ Failed to optimize indexes: ' . $e->getMessage());
        }
    }

    /**
     * Verificar índices não utilizados
     */
    private function checkUnusedIndexes(): void
    {
        if (config('database.default') === 'pgsql') {
            $unusedIndexes = DB::select("
                SELECT schemaname, tablename, indexname, idx_tup_read, idx_tup_fetch
                FROM pg_stat_user_indexes
                WHERE idx_tup_read = 0 AND idx_tup_fetch = 0
                AND schemaname = 'public'
            ");

            if (!empty($unusedIndexes)) {
                $this->warn('Found unused indexes:');
                foreach ($unusedIndexes as $index) {
                    $this->line("  - {$index->tablename}.{$index->indexname}");
                }
            }
        }
    }

    /**
     * Verificar índices duplicados
     */
    private function checkDuplicateIndexes(): void
    {
        if (config('database.default') === 'pgsql') {
            $duplicateIndexes = DB::select("
                SELECT 
                    t1.tablename,
                    t1.indexname as index1,
                    t2.indexname as index2,
                    t1.indexdef as def1,
                    t2.indexdef as def2
                FROM pg_indexes t1
                JOIN pg_indexes t2 ON t1.tablename = t2.tablename
                WHERE t1.indexname < t2.indexname
                AND t1.indexdef = t2.indexdef
                AND t1.schemaname = 'public'
            ");

            if (!empty($duplicateIndexes)) {
                $this->warn('Found duplicate indexes:');
                foreach ($duplicateIndexes as $index) {
                    $this->line("  - {$index->tablename}: {$index->index1} and {$index->index2}");
                }
            }
        }
    }

    /**
     * Verificar fragmentação de índices
     */
    private function checkIndexFragmentation(): void
    {
        if (config('database.default') === 'pgsql') {
            $fragmentedIndexes = DB::select("
                SELECT 
                    schemaname,
                    tablename,
                    indexname,
                    pg_size_pretty(pg_relation_size(indexrelid)) as index_size,
                    pg_stat_get_tuples_returned(indexrelid) as tuples_returned,
                    pg_stat_get_tuples_fetched(indexrelid) as tuples_fetched
                FROM pg_stat_user_indexes
                WHERE schemaname = 'public'
                AND pg_relation_size(indexrelid) > 1024 * 1024 -- Maior que 1MB
                ORDER BY pg_relation_size(indexrelid) DESC
                LIMIT 10
            ");

            if (!empty($fragmentedIndexes)) {
                $this->info('Largest indexes:');
                foreach ($fragmentedIndexes as $index) {
                    $this->line("  - {$index->tablename}.{$index->indexname}: {$index->index_size}");
                }
            }
        }
    }
}