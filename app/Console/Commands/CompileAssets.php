<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CompileAssets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assets:compile {--production : Compile for production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compile frontend assets using Laravel Mix';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🎨 Compiling frontend assets...');
        $this->newLine();

        $isProduction = $this->option('production');

        try {
            // Verificar se Node.js está instalado
            if (!$this->isNodeInstalled()) {
                $this->error('❌ Node.js não está instalado. Instale Node.js para compilar os assets.');
                return 1;
            }

            // Verificar se npm está disponível
            if (!$this->isNpmInstalled()) {
                $this->error('❌ npm não está disponível. Instale npm para compilar os assets.');
                return 1;
            }

            // Instalar dependências se necessário
            if (!$this->areDependenciesInstalled()) {
                $this->info('📦 Installing dependencies...');
                $this->runCommand('npm install');
            }

            // Compilar assets
            if ($isProduction) {
                $this->info('🏭 Compiling for production...');
                $this->runCommand('npm run production');
            } else {
                $this->info('🔧 Compiling for development...');
                $this->runCommand('npm run dev');
            }

            // Verificar se os arquivos foram gerados
            $this->verifyCompiledAssets();

            $this->newLine();
            $this->info('✅ Assets compiled successfully!');
            
            if ($isProduction) {
                $this->info('🚀 Production assets are ready for deployment.');
            }

        } catch (\Exception $e) {
            $this->error('❌ Error compiling assets: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }

    /**
     * Verificar se Node.js está instalado
     */
    private function isNodeInstalled(): bool
    {
        $output = [];
        $returnCode = 0;
        exec('node --version 2>&1', $output, $returnCode);
        return $returnCode === 0;
    }

    /**
     * Verificar se npm está instalado
     */
    private function isNpmInstalled(): bool
    {
        $output = [];
        $returnCode = 0;
        exec('npm --version 2>&1', $output, $returnCode);
        return $returnCode === 0;
    }

    /**
     * Verificar se as dependências estão instaladas
     */
    private function areDependenciesInstalled(): bool
    {
        return File::exists(base_path('node_modules'));
    }

    /**
     * Executar comando
     */
    private function runCommand(string $command): void
    {
        $this->line("Running: {$command}");
        
        $output = [];
        $returnCode = 0;
        exec($command . ' 2>&1', $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new \Exception('Command failed: ' . implode("\n", $output));
        }
        
        $this->line(implode("\n", $output));
    }

    /**
     * Verificar se os assets foram compilados
     */
    private function verifyCompiledAssets(): void
    {
        $this->info('🔍 Verifying compiled assets...');

        $assets = [
            'public/css/app.css',
            'public/js/app.js',
        ];

        foreach ($assets as $asset) {
            if (File::exists(base_path($asset))) {
                $size = File::size(base_path($asset));
                $this->line("  ✅ {$asset} (" . $this->formatBytes($size) . ")");
            } else {
                $this->warn("  ⚠️  {$asset} not found");
            }
        }

        // Verificar se o manifest foi gerado
        if (File::exists(base_path('public/mix-manifest.json'))) {
            $this->line("  ✅ mix-manifest.json generated");
        } else {
            $this->warn("  ⚠️  mix-manifest.json not found");
        }
    }

    /**
     * Formatar bytes para leitura humana
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}