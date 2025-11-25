<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Para PostgreSQL, precisamos adicionar o valor 'whatsapp' ao tipo ENUM existente
        DB::statement("ALTER TYPE qr_codes_type_enum ADD VALUE IF NOT EXISTS 'whatsapp'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // PostgreSQL não permite remover valores de um ENUM diretamente
        // A reversão requer recriar o tipo, o que é complexo e arriscado em produção
        // Por isso, deixamos vazio - o valor 'whatsapp' permanecerá no enum
        // Se necessário remover, seria preciso:
        // 1. Criar novo tipo sem 'whatsapp'
        // 2. Alterar coluna para usar novo tipo
        // 3. Remover tipo antigo
        // Isso não é recomendado em produção
    }
};
