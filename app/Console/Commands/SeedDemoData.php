<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\DatabaseSeeder;

class SeedDemoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:seed {--fresh : Drop all tables and re-run all migrations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the database with demo data for testing and development';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('fresh')) {
            $this->info('Dropping all tables and re-running migrations...');
            $this->call('migrate:fresh');
        }

        $this->info('Seeding demo data...');
        
        $seeder = new DatabaseSeeder();
        $seeder->run();

        $this->info('Demo data seeded successfully!');
        $this->newLine();
        
        $this->info('Demo accounts created:');
        $this->line('• Admin: admin@qrcodesaas.com / password123');
        $this->line('• Trial User: trial@example.com / password123');
        $this->line('• Premium User: premium@example.com / password123');
        $this->line('• Expired User: expired@example.com / password123');
        $this->line('• Cancelled User: cancelled@example.com / password123');
        
        $this->newLine();
        $this->info('You can now test the application with these accounts!');
    }
}
