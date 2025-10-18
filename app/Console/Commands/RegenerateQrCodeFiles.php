<?php

namespace App\Console\Commands;

use App\Models\QrCode;
use App\Services\QrCodeGeneratorService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RegenerateQrCodeFiles extends Command
{
    protected $signature = 'qrcodes:regenerate-files {--force : Force regeneration even if file exists}';
    protected $description = 'Regenerate QR Code files for existing QR Codes that don\'t have files';

    protected QrCodeGeneratorService $qrGenerator;

    public function __construct(QrCodeGeneratorService $qrGenerator)
    {
        parent::__construct();
        $this->qrGenerator = $qrGenerator;
    }

    public function handle()
    {
        $this->info('🔄 Regenerando arquivos de QR Codes...');

        $query = QrCode::query();
        
        if (!$this->option('force')) {
            $query->whereNull('file_path');
        }

        $qrCodes = $query->get();

        if ($qrCodes->isEmpty()) {
            $this->info('✅ Nenhum QR Code encontrado para regenerar.');
            return Command::SUCCESS;
        }

        $this->info("📊 Encontrados {$qrCodes->count()} QR Code(s) para processar.");

        $bar = $this->output->createProgressBar($qrCodes->count());
        $bar->start();

        $successCount = 0;
        $errorCount = 0;

        foreach ($qrCodes as $qrCode) {
            try {
                // Deletar arquivo antigo se existir
                if ($qrCode->file_path && Storage::disk('public')->exists($qrCode->file_path)) {
                    $this->qrGenerator->deleteQrCodeFile($qrCode->file_path);
                }

                // Converter content para string se for array
                $content = is_array($qrCode->content) ? json_encode($qrCode->content) : $qrCode->content;
                
                // Gerar novo arquivo
                $filename = $this->qrGenerator->generateUniqueFilename();
                $filePath = $this->qrGenerator->generateAndSave($content, $filename, 'svg');

                // Atualizar QR Code
                $qrCode->update(['file_path' => $filePath]);

                $successCount++;
            } catch (\Exception $e) {
                $this->error("\n❌ Erro ao processar QR Code ID {$qrCode->id}: " . $e->getMessage());
                $errorCount++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->info("✅ Processamento concluído!");
        $this->info("📈 Sucessos: {$successCount}");
        
        if ($errorCount > 0) {
            $this->warn("⚠️ Erros: {$errorCount}");
        }

        return Command::SUCCESS;
    }
}
