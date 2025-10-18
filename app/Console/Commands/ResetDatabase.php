<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:reset {--force : Force reset without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset database completely and run migrations fresh';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Resetting database...');
        $this->newLine();

        if (!$this->option('force')) {
            if (!$this->confirm('⚠️  This will DELETE ALL DATA and recreate the database. Continue?')) {
                $this->info('Operation cancelled.');
                return;
            }
        }

        try {
            // 1. Drop all tables
            $this->info('🗑️  Dropping all tables...');
            $this->dropAllTables();

            // 2. Run migrations fresh
            $this->info('🏗️  Running migrations fresh...');
            $this->call('migrate:fresh', ['--force' => true]);

            // 3. Run seeders
            $this->info('🌱 Running seeders...');
            $this->call('db:seed', ['--force' => true]);

            // 4. Clear cache
            $this->info('🧹 Clearing cache...');
            $this->call('config:clear');
            $this->call('cache:clear');
            $this->call('route:clear');
            $this->call('view:clear');

            $this->newLine();
            $this->info('✅ Database reset completed successfully!');
            $this->newLine();

            // 5. Show status
            $this->showDatabaseStatus();

        } catch (\Exception $e) {
            $this->error('❌ Error resetting database: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Drop all tables
     */
    private function dropAllTables(): void
    {
        try {
            // Get all table names
            $tables = DB::select("
                SELECT tablename 
                FROM pg_tables 
                WHERE schemaname = 'public'
            ");

            if (empty($tables)) {
                $this->line('No tables to drop.');
                return;
            }

            // Disable foreign key checks
            DB::statement('SET session_replication_role = replica;');

            // Drop all tables
            foreach ($tables as $table) {
                $tableName = $table->tablename;
                if ($tableName !== 'migrations') {
                    DB::statement("DROP TABLE IF EXISTS {$tableName} CASCADE");
                    $this->line("  Dropped: {$tableName}");
                }
            }

            // Re-enable foreign key checks
            DB::statement('SET session_replication_role = DEFAULT;');

            $this->line('All tables dropped successfully.');

        } catch (\Exception $e) {
            $this->warn('Error dropping tables: ' . $e->getMessage());
        }
    }

    /**
     * Show database status
     */
    private function showDatabaseStatus(): void
    {
        $this->info('📊 Database Status:');
        $this->newLine();

        try {
            // Check tables
            $tables = DB::select("
                SELECT tablename 
                FROM pg_tables 
                WHERE schemaname = 'public'
                ORDER BY tablename
            ");

            $this->line('Tables created:');
            foreach ($tables as $table) {
                $this->line("  ✅ {$table->tablename}");
            }

            $this->newLine();

            // Check migrations
            $migrations = DB::table('migrations')->count();
            $this->line("Migrations executed: {$migrations}");

            // Check users
            if (Schema::hasTable('users')) {
                $userCount = DB::table('users')->count();
                $this->line("Users created: {$userCount}");
            }

            // Check QR codes
            if (Schema::hasTable('qr_codes')) {
                $qrCount = DB::table('qr_codes')->count();
                $this->line("QR codes created: {$qrCount}");
            }

        } catch (\Exception $e) {
            $this->warn('Error checking database status: ' . $e->getMessage());
        }
    }
}