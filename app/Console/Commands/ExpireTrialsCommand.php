<?php

namespace App\Console\Commands;

use App\Jobs\ExpireTrialSubscriptions;
use Illuminate\Console\Command;

class ExpireTrialsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:expire-trials';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire trial subscriptions that have reached their end date';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando expiração de trials...');
        
        // Executar o job
        ExpireTrialSubscriptions::dispatch();
        
        $this->info('Job de expiração de trials enviado para a fila.');
        
        return 0;
    }
}
