<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('qr_scans', function (Blueprint $table) {
            $columns = Schema::getColumnListing('qr_scans');
            
            // Dados de geolocalização expandidos
            if (!in_array('region', $columns)) {
                $table->string('region')->nullable()->after('city');
            }
            if (!in_array('region_code', $columns)) {
                $table->string('region_code', 10)->nullable()->after('region');
            }
            if (!in_array('postal_code', $columns)) {
                $table->string('postal_code', 20)->nullable()->after('region_code');
            }
            if (!in_array('timezone', $columns)) {
                $table->string('timezone', 50)->nullable()->after('postal_code');
            }
            
            // Dados de rede/ISP
            if (!in_array('isp', $columns)) {
                $table->string('isp')->nullable()->after('timezone');
            }
            if (!in_array('organization', $columns)) {
                $table->string('organization')->nullable()->after('isp');
            }
            if (!in_array('as_number', $columns)) {
                $table->string('as_number', 50)->nullable()->after('organization');
            }
            if (!in_array('is_mobile_connection', $columns)) {
                $table->boolean('is_mobile_connection')->default(false)->after('as_number');
            }
            if (!in_array('is_proxy', $columns)) {
                $table->boolean('is_proxy')->default(false)->after('is_mobile_connection');
            }
            if (!in_array('is_hosting', $columns)) {
                $table->boolean('is_hosting')->default(false)->after('is_proxy');
            }
            
            // Dados do dispositivo/navegador expandidos
            if (!in_array('browser_version', $columns)) {
                $table->string('browser_version', 50)->nullable()->after('browser');
            }
            if (!in_array('os_version', $columns)) {
                $table->string('os_version', 50)->nullable()->after('os');
            }
            if (!in_array('device_model', $columns)) {
                $table->string('device_model', 100)->nullable()->after('device_type');
            }
            if (!in_array('is_robot', $columns)) {
                $table->boolean('is_robot')->default(false)->after('device_model');
            }
            if (!in_array('language', $columns)) {
                $table->string('language', 10)->nullable()->after('is_robot');
            }
            
            // Dados de conexão
            if (!in_array('referer', $columns)) {
                $table->string('referer')->nullable()->after('language');
            }
            if (!in_array('protocol', $columns)) {
                $table->string('protocol', 10)->nullable()->after('referer');
            }
        });
        
        // Adicionar índices separadamente para evitar duplicatas
        Schema::table('qr_scans', function (Blueprint $table) {
            $indexes = [];
            try {
                $indexes = \DB::select("SELECT name FROM sqlite_master WHERE type='index' AND tbl_name='qr_scans'");
                $indexNames = array_column($indexes, 'name');
            } catch (\Exception $e) {
                // Ignora erro se não conseguir verificar índices
            }
            
            if (!in_array('qr_scans_region_index', $indexNames ?? [])) {
                $table->index('region');
            }
            if (!in_array('qr_scans_isp_index', $indexNames ?? [])) {
                $table->index('isp');
            }
            if (!in_array('qr_scans_is_robot_index', $indexNames ?? [])) {
                $table->index('is_robot');
            }
        });
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
