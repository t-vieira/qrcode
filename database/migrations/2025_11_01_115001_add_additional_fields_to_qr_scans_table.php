<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $connection = Schema::getConnection();
        $driver = $connection->getDriverName();
        
        // Obter lista de colunas existentes
        $columns = Schema::getColumnListing('qr_scans');
        
        // Função helper para verificar se índice existe
        $indexExists = function($indexName) use ($connection, $driver) {
            try {
                if ($driver === 'pgsql') {
                    $result = $connection->select(
                        "SELECT indexname FROM pg_indexes WHERE tablename = 'qr_scans' AND indexname = ?",
                        [$indexName]
                    );
                    return !empty($result);
                } elseif ($driver === 'mysql') {
                    $result = $connection->select(
                        "SELECT COUNT(*) as count FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'qr_scans' AND index_name = ?",
                        [$indexName]
                    );
                    return isset($result[0]->count) && $result[0]->count > 0;
                } else {
                    // SQLite
                    $result = $connection->select(
                        "SELECT name FROM sqlite_master WHERE type='index' AND tbl_name='qr_scans' AND name = ?",
                        [$indexName]
                    );
                    return !empty($result);
                }
            } catch (\Exception $e) {
                return false;
            }
        };
        
        Schema::table('qr_scans', function (Blueprint $table) use ($columns) {
            // Dados de geolocalização expandidos
            if (!in_array('region', $columns)) {
                $table->string('region')->nullable();
            }
            if (!in_array('region_code', $columns)) {
                $table->string('region_code', 10)->nullable();
            }
            if (!in_array('postal_code', $columns)) {
                $table->string('postal_code', 20)->nullable();
            }
            if (!in_array('timezone', $columns)) {
                $table->string('timezone', 50)->nullable();
            }
            
            // Dados de rede/ISP
            if (!in_array('isp', $columns)) {
                $table->string('isp')->nullable();
            }
            if (!in_array('organization', $columns)) {
                $table->string('organization')->nullable();
            }
            if (!in_array('as_number', $columns)) {
                $table->string('as_number', 50)->nullable();
            }
            if (!in_array('is_mobile_connection', $columns)) {
                $table->boolean('is_mobile_connection')->default(false);
            }
            if (!in_array('is_proxy', $columns)) {
                $table->boolean('is_proxy')->default(false);
            }
            if (!in_array('is_hosting', $columns)) {
                $table->boolean('is_hosting')->default(false);
            }
            
            // Dados do dispositivo/navegador expandidos
            if (!in_array('browser_version', $columns)) {
                $table->string('browser_version', 50)->nullable();
            }
            if (!in_array('os_version', $columns)) {
                $table->string('os_version', 50)->nullable();
            }
            if (!in_array('device_model', $columns)) {
                $table->string('device_model', 100)->nullable();
            }
            if (!in_array('is_robot', $columns)) {
                $table->boolean('is_robot')->default(false);
            }
            if (!in_array('language', $columns)) {
                $table->string('language', 10)->nullable();
            }
            
            // Dados de conexão
            if (!in_array('referer', $columns)) {
                $table->string('referer')->nullable();
            }
            if (!in_array('protocol', $columns)) {
                $table->string('protocol', 10)->nullable();
            }
        });
        
        // Re-verificar colunas após criação (para casos onde foram criadas agora)
        $columnsAfter = Schema::getColumnListing('qr_scans');
        
        // Adicionar índices separadamente usando DB::statement para evitar problemas de transação no PostgreSQL
        if (in_array('region', $columnsAfter) && !$indexExists('qr_scans_region_index')) {
            try {
                if ($driver === 'pgsql') {
                    DB::statement('CREATE INDEX IF NOT EXISTS qr_scans_region_index ON qr_scans (region)');
                } else {
                    Schema::table('qr_scans', function (Blueprint $table) {
                        $table->index('region');
                    });
                }
            } catch (\Exception $e) {
                // Ignora se o índice já existir
                if (strpos(strtolower($e->getMessage()), 'already exists') === false && 
                    strpos(strtolower($e->getMessage()), 'duplicate') === false) {
                    throw $e;
                }
            }
        }
        
        if (in_array('isp', $columnsAfter) && !$indexExists('qr_scans_isp_index')) {
            try {
                if ($driver === 'pgsql') {
                    DB::statement('CREATE INDEX IF NOT EXISTS qr_scans_isp_index ON qr_scans (isp)');
                } else {
                    Schema::table('qr_scans', function (Blueprint $table) {
                        $table->index('isp');
                    });
                }
            } catch (\Exception $e) {
                if (strpos(strtolower($e->getMessage()), 'already exists') === false && 
                    strpos(strtolower($e->getMessage()), 'duplicate') === false) {
                    throw $e;
                }
            }
        }
        
        if (in_array('is_robot', $columnsAfter) && !$indexExists('qr_scans_is_robot_index')) {
            try {
                if ($driver === 'pgsql') {
                    DB::statement('CREATE INDEX IF NOT EXISTS qr_scans_is_robot_index ON qr_scans (is_robot)');
                } else {
                    Schema::table('qr_scans', function (Blueprint $table) {
                        $table->index('is_robot');
                    });
                }
            } catch (\Exception $e) {
                if (strpos(strtolower($e->getMessage()), 'already exists') === false && 
                    strpos(strtolower($e->getMessage()), 'duplicate') === false) {
                    throw $e;
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qr_scans', function (Blueprint $table) {
            $table->dropIndex(['region']);
            $table->dropIndex(['isp']);
            $table->dropIndex(['is_robot']);
            
            $table->dropColumn([
                'region',
                'region_code',
                'postal_code',
                'timezone',
                'isp',
                'organization',
                'as_number',
                'is_mobile_connection',
                'is_proxy',
                'is_hosting',
                'browser_version',
                'os_version',
                'device_model',
                'is_robot',
                'language',
                'referer',
                'protocol',
            ]);
        });
    }
};
