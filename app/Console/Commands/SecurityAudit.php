<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SecurityAuditService;
use App\Services\RateLimitingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SecurityAudit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'security:audit {--fix : Attempt to fix security issues} {--export : Export report to file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perform comprehensive security audit of the application';

    protected SecurityAuditService $securityAudit;
    protected RateLimitingService $rateLimiting;

    public function __construct(SecurityAuditService $securityAudit, RateLimitingService $rateLimiting)
    {
        parent::__construct();
        $this->securityAudit = $securityAudit;
        $this->rateLimiting = $rateLimiting;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔒 Starting security audit...');
        $this->newLine();

        $issues = [];
        $recommendations = [];

        // Check file permissions
        $this->checkFilePermissions($issues, $recommendations);

        // Check environment configuration
        $this->checkEnvironmentConfig($issues, $recommendations);

        // Check database security
        $this->checkDatabaseSecurity($issues, $recommendations);

        // Check application security
        $this->checkApplicationSecurity($issues, $recommendations);

        // Check rate limiting
        $this->checkRateLimiting($issues, $recommendations);

        // Check logs and monitoring
        $this->checkLogsAndMonitoring($issues, $recommendations);

        // Display results
        $this->displayResults($issues, $recommendations);

        // Export report if requested
        if ($this->option('export')) {
            $this->exportReport($issues, $recommendations);
        }

        // Fix issues if requested
        if ($this->option('fix')) {
            $this->fixIssues($issues);
        }

        $this->newLine();
        $this->info('✅ Security audit completed!');
    }

    /**
     * Check file permissions
     */
    private function checkFilePermissions(array &$issues, array &$recommendations): void
    {
        $this->info('📁 Checking file permissions...');

        $criticalPaths = [
            'storage' => 775,
            'bootstrap/cache' => 775,
            '.env' => 600,
            'config' => 755,
        ];

        foreach ($criticalPaths as $path => $expectedPermission) {
            $fullPath = base_path($path);
            
            if (File::exists($fullPath)) {
                $actualPermission = substr(sprintf('%o', fileperms($fullPath)), -3);
                
                if ($actualPermission != $expectedPermission) {
                    $issues[] = [
                        'type' => 'file_permission',
                        'severity' => 'high',
                        'path' => $path,
                        'expected' => $expectedPermission,
                        'actual' => $actualPermission,
                        'message' => "Incorrect permissions on {$path}",
                    ];
                }
            }
        }

        $recommendations[] = 'Ensure all sensitive files have proper permissions (600 for .env, 775 for storage)';
    }

    /**
     * Check environment configuration
     */
    private function checkEnvironmentConfig(array &$issues, array &$recommendations): void
    {
        $this->info('⚙️ Checking environment configuration...');

        $requiredEnvVars = [
            'APP_KEY',
            'DB_PASSWORD',
            'MERCADOPAGO_ACCESS_TOKEN',
            'WHATSAPP_ACCESS_TOKEN',
            'RECAPTCHA_SECRET_KEY',
        ];

        foreach ($requiredEnvVars as $var) {
            $value = env($var);
            
            if (empty($value) || $value === 'YOUR_' . $var) {
                $issues[] = [
                    'type' => 'environment',
                    'severity' => 'critical',
                    'variable' => $var,
                    'message' => "Environment variable {$var} is not properly configured",
                ];
            }
        }

        // Check debug mode
        if (env('APP_DEBUG', false)) {
            $issues[] = [
                'type' => 'environment',
                'severity' => 'high',
                'variable' => 'APP_DEBUG',
                'message' => 'Debug mode is enabled in production',
            ];
        }

        $recommendations[] = 'Review all environment variables and ensure sensitive data is properly configured';
    }

    /**
     * Check database security
     */
    private function checkDatabaseSecurity(array &$issues, array &$recommendations): void
    {
        $this->info('🗄️ Checking database security...');

        try {
            // Check for weak passwords
            $weakPasswords = DB::table('users')
                ->whereRaw('LENGTH(password) < 8')
                ->count();

            if ($weakPasswords > 0) {
                $issues[] = [
                    'type' => 'database',
                    'severity' => 'medium',
                    'count' => $weakPasswords,
                    'message' => "{$weakPasswords} users with weak passwords found",
                ];
            }

            // Check for users without email verification
            $unverifiedUsers = DB::table('users')
                ->whereNull('email_verified_at')
                ->where('created_at', '<', now()->subDays(7))
                ->count();

            if ($unverifiedUsers > 0) {
                $issues[] = [
                    'type' => 'database',
                    'severity' => 'medium',
                    'count' => $unverifiedUsers,
                    'message' => "{$unverifiedUsers} users with unverified emails for more than 7 days",
                ];
            }

        } catch (\Exception $e) {
            $issues[] = [
                'type' => 'database',
                'severity' => 'high',
                'message' => 'Database connection failed: ' . $e->getMessage(),
            ];
        }

        $recommendations[] = 'Implement password strength requirements and email verification enforcement';
    }

    /**
     * Check application security
     */
    private function checkApplicationSecurity(array &$issues, array &$recommendations): void
    {
        $this->info('🔐 Checking application security...');

        // Check for exposed sensitive files
        $sensitiveFiles = [
            '.env',
            'composer.json',
            'package.json',
            'artisan',
        ];

        foreach ($sensitiveFiles as $file) {
            $path = public_path($file);
            if (File::exists($path)) {
                $issues[] = [
                    'type' => 'application',
                    'severity' => 'critical',
                    'file' => $file,
                    'message' => "Sensitive file {$file} is accessible in public directory",
                ];
            }
        }

        // Check for missing security headers
        $securityHeaders = [
            'X-Frame-Options',
            'X-Content-Type-Options',
            'X-XSS-Protection',
            'Strict-Transport-Security',
        ];

        $recommendations[] = 'Ensure all security headers are properly configured in Nginx/Apache';
        $recommendations[] = 'Implement Content Security Policy (CSP) headers';
    }

    /**
     * Check rate limiting
     */
    private function checkRateLimiting(array &$issues, array &$recommendations): void
    {
        $this->info('🚦 Checking rate limiting...');

        $stats = $this->rateLimiting->getRateLimitStats();

        if ($stats['blocked_ips'] > 100) {
            $issues[] = [
                'type' => 'rate_limiting',
                'severity' => 'medium',
                'count' => $stats['blocked_ips'],
                'message' => "High number of blocked IPs: {$stats['blocked_ips']}",
            ];
        }

        $recommendations[] = 'Monitor rate limiting statistics regularly';
        $recommendations[] = 'Consider implementing DDoS protection for high-traffic scenarios';
    }

    /**
     * Check logs and monitoring
     */
    private function checkLogsAndMonitoring(array &$issues, array &$recommendations): void
    {
        $this->info('📊 Checking logs and monitoring...');

        $logPath = storage_path('logs');
        $logSize = 0;

        if (File::exists($logPath)) {
            $files = File::allFiles($logPath);
            foreach ($files as $file) {
                $logSize += $file->getSize();
            }
        }

        // Check if logs are too large (> 100MB)
        if ($logSize > 100 * 1024 * 1024) {
            $issues[] = [
                'type' => 'monitoring',
                'severity' => 'low',
                'size' => $this->formatBytes($logSize),
                'message' => 'Log files are very large: ' . $this->formatBytes($logSize),
            ];
        }

        $recommendations[] = 'Implement log rotation to prevent disk space issues';
        $recommendations[] = 'Set up monitoring and alerting for security events';
    }

    /**
     * Display audit results
     */
    private function displayResults(array $issues, array $recommendations): void
    {
        $this->newLine();
        $this->info('📋 Security Audit Results');
        $this->newLine();

        // Display issues
        if (!empty($issues)) {
            $this->error('🚨 Security Issues Found:');
            $this->newLine();

            $criticalIssues = array_filter($issues, fn($issue) => $issue['severity'] === 'critical');
            $highIssues = array_filter($issues, fn($issue) => $issue['severity'] === 'high');
            $mediumIssues = array_filter($issues, fn($issue) => $issue['severity'] === 'medium');
            $lowIssues = array_filter($issues, fn($issue) => $issue['severity'] === 'low');

            if (!empty($criticalIssues)) {
                $this->error('🔴 Critical Issues:');
                foreach ($criticalIssues as $issue) {
                    $this->line("  • {$issue['message']}");
                }
                $this->newLine();
            }

            if (!empty($highIssues)) {
                $this->error('🟠 High Priority Issues:');
                foreach ($highIssues as $issue) {
                    $this->line("  • {$issue['message']}");
                }
                $this->newLine();
            }

            if (!empty($mediumIssues)) {
                $this->warn('🟡 Medium Priority Issues:');
                foreach ($mediumIssues as $issue) {
                    $this->line("  • {$issue['message']}");
                }
                $this->newLine();
            }

            if (!empty($lowIssues)) {
                $this->info('🔵 Low Priority Issues:');
                foreach ($lowIssues as $issue) {
                    $this->line("  • {$issue['message']}");
                }
                $this->newLine();
            }
        } else {
            $this->info('✅ No security issues found!');
            $this->newLine();
        }

        // Display recommendations
        if (!empty($recommendations)) {
            $this->info('💡 Security Recommendations:');
            foreach ($recommendations as $recommendation) {
                $this->line("  • {$recommendation}");
            }
            $this->newLine();
        }

        // Summary
        $this->info('📊 Summary:');
        $this->line("  Total Issues: " . count($issues));
        $this->line("  Critical: " . count(array_filter($issues, fn($i) => $i['severity'] === 'critical')));
        $this->line("  High: " . count(array_filter($issues, fn($i) => $i['severity'] === 'high')));
        $this->line("  Medium: " . count(array_filter($issues, fn($i) => $i['severity'] === 'medium')));
        $this->line("  Low: " . count(array_filter($issues, fn($i) => $i['severity'] === 'low')));
    }

    /**
     * Export audit report
     */
    private function exportReport(array $issues, array $recommendations): void
    {
        $filename = 'security_audit_' . now()->format('Y-m-d_H-i-s') . '.json';
        $filepath = storage_path('logs/' . $filename);

        $report = [
            'timestamp' => now()->toISOString(),
            'issues' => $issues,
            'recommendations' => $recommendations,
            'summary' => [
                'total_issues' => count($issues),
                'critical' => count(array_filter($issues, fn($i) => $i['severity'] === 'critical')),
                'high' => count(array_filter($issues, fn($i) => $i['severity'] === 'high')),
                'medium' => count(array_filter($issues, fn($i) => $i['severity'] === 'medium')),
                'low' => count(array_filter($issues, fn($i) => $i['severity'] === 'low')),
            ],
        ];

        file_put_contents($filepath, json_encode($report, JSON_PRETTY_PRINT));
        $this->info("📄 Security audit report exported to: {$filepath}");
    }

    /**
     * Fix security issues
     */
    private function fixIssues(array $issues): void
    {
        $this->info('🔧 Attempting to fix security issues...');

        foreach ($issues as $issue) {
            switch ($issue['type']) {
                case 'file_permission':
                    $this->fixFilePermission($issue);
                    break;
                case 'environment':
                    $this->fixEnvironmentIssue($issue);
                    break;
                default:
                    $this->warn("Cannot auto-fix issue: {$issue['message']}");
            }
        }
    }

    /**
     * Fix file permission issue
     */
    private function fixFilePermission(array $issue): void
    {
        $path = base_path($issue['path']);
        $permission = octdec($issue['expected']);
        
        if (chmod($path, $permission)) {
            $this->info("✅ Fixed permissions for {$issue['path']}");
        } else {
            $this->error("❌ Failed to fix permissions for {$issue['path']}");
        }
    }

    /**
     * Fix environment issue
     */
    private function fixEnvironmentIssue(array $issue): void
    {
        if ($issue['variable'] === 'APP_DEBUG' && env('APP_ENV') === 'production') {
            $this->warn("Please manually set APP_DEBUG=false in .env file for production");
        } else {
            $this->warn("Please manually configure {$issue['variable']} in .env file");
        }
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}