# Guia Completo de Comandos Artisan - QR Code SaaS

## 📋 Comandos por Categoria

### 🔧 Configuração e Manutenção

#### Configuração de Servidor Compartilhado
```bash
# Verificar configuração atual do servidor
php artisan shared-hosting:configure --check

# Configurar aplicação para servidor compartilhado
php artisan shared-hosting:configure

# Limpeza e otimização para servidor compartilhado
php artisan shared-hosting:cleanup
php artisan shared-hosting:cleanup --force
```

**Exemplo de saída:**
```
🔧 Configuring application for shared hosting...

📊 Checking server limitations...
┌─────────────────────┬─────────┬──────────┐
│ Setting             │ Value   │ Status   │
├─────────────────────┼─────────┼──────────┤
│ Memory Limit        │ 256M    │ ✅ Good  │
│ Max Execution Time  │ 300s    │ ✅ Good  │
│ Upload Max Filesize │ 10M     │ ✅ Good  │
└─────────────────────┴─────────┴──────────┘

✅ Application configured for shared hosting!
```

#### Auditoria de Segurança
```bash
# Executar auditoria completa
php artisan security:audit

# Exportar relatório para arquivo
php artisan security:audit --export

# Corrigir problemas automaticamente
php artisan security:audit --fix
```

**Exemplo de saída:**
```
🔒 Starting security audit...

📁 Checking file permissions...
⚙️ Checking environment configuration...
🗄️ Checking database security...
🔐 Checking application security...

📋 Security Audit Results

🚨 Security Issues Found:
🔴 Critical Issues:
  • Environment variable APP_KEY is not properly configured
  • Environment variable DB_PASSWORD is not properly configured

💡 Security Recommendations:
  • Ensure all sensitive files have proper permissions
  • Review all environment variables

📊 Summary:
  Total Issues: 2
  Critical: 2
  High: 0
  Medium: 0
  Low: 0
```

#### Monitoramento de Performance
```bash
# Monitorar métricas básicas
php artisan performance:monitor

# Exportar métricas para arquivo JSON
php artisan performance:monitor --export
```

**Exemplo de saída:**
```
📊 Collecting performance metrics...

{
  "timestamp": "2024-01-15 10:30:00",
  "database": {
    "connection_status": "ok",
    "database_size": "45.2 MB",
    "active_connections": 3
  },
  "cache": {
    "driver": "file",
    "keys_count": 1250
  },
  "memory": {
    "current_usage_mb": 45.2,
    "peak_usage_mb": 67.8,
    "memory_limit": "256M"
  }
}
```

### 💳 Assinatura e Pagamentos

#### Gerenciamento de Trials
```bash
# Expirar trials de usuários
php artisan subscriptions:expire-trials

# Enviar notificações de trial expirando
php artisan subscriptions:send-trial-expiring-notifications
```

**Exemplo de saída:**
```
🔄 Processing trial subscriptions...

Found 15 users with expired trials
✅ Updated 15 user subscription statuses
📧 Sent 12 trial expiration notifications
⚠️  3 users could not be notified (invalid email)

Trial expiration process completed!
```

### 🗄️ Banco de Dados

#### Otimização de Banco
```bash
# Otimizar banco completo (ANALYZE + VACUUM)
php artisan db:optimize --analyze --vacuum

# Apenas ANALYZE (atualizar estatísticas)
php artisan db:optimize --analyze

# Apenas VACUUM (limpar espaço)
php artisan db:optimize --vacuum
```

**Exemplo de saída:**
```
🗄️ Starting database optimization...

Analyzing table: users
Analyzing table: qr_codes
Analyzing table: qr_scans
Vacuuming table: users
Vacuuming table: qr_codes
Vacuuming table: qr_scans

✅ Database optimization complete.
```

#### Migrações
```bash
# Executar migrações
php artisan migrate

# Executar em produção (sem confirmação)
php artisan migrate --force

# Reverter última migração
php artisan migrate:rollback

# Reverter todas as migrações
php artisan migrate:reset

# Recriar banco (desenvolvimento)
php artisan migrate:fresh
php artisan migrate:fresh --seed
```

### 🗂️ Cache e Otimização

#### Limpeza de Cache
```bash
# Limpar todo o cache da aplicação
php artisan cache:clear-all

# Limpar cache para usuário específico
php artisan cache:clear-all --user=123

# Limpar cache por tipo
php artisan cache:clear-all --type=dashboard
php artisan cache:clear-all --type=qrcode
```

**Exemplo de saída:**
```
🧹 Clearing application cache...

Clearing cache for user ID: 123...
✅ User cache cleared.

Clearing cache for type: dashboard...
⚠️ Specific dashboard cache clearing for all users is not directly implemented via type.

✅ Cache clearing completed.
```

#### Cache da Aplicação
```bash
# Limpar caches específicos
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan event:clear

# Criar caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Otimização geral
php artisan optimize
php artisan optimize:clear
```

### 🔄 Filas e Jobs

#### Gerenciamento de Filas
```bash
# Processar filas
php artisan queue:work

# Processar com timeout específico
php artisan queue:work --timeout=300

# Processar filas específicas
php artisan queue:work --queue=webhooks,notifications

# Limpar filas falhadas
php artisan queue:prune-failed

# Reprocessar jobs falhados
php artisan queue:retry all
php artisan queue:retry 5

# Limpar todos os jobs falhados
php artisan queue:flush
```

**Exemplo de saída:**
```
🔄 Processing queue jobs...

Processing job: ProcessSubscriptionWebhook
✅ Job completed successfully

Processing job: GenerateQrCodeFile
✅ Job completed successfully

Queue processing completed!
```

### 📁 Storage e Arquivos

#### Gerenciamento de Storage
```bash
# Criar link simbólico para storage
php artisan storage:link

# Limpar arquivos antigos
php artisan storage:cleanup
```

### 🌱 Seeders e Dados

#### Seeders de Dados
```bash
# Executar todos os seeders
php artisan db:seed

# Executar seeder específico
php artisan db:seed --class=UserSeeder
php artisan db:seed --class=QrCodeSeeder
php artisan db:seed --class=TeamSeeder
php artisan db:seed --class=SupportTicketSeeder

# Seeders com dados de demonstração
php artisan demo:seed
php artisan demo:seed --fresh
```

**Exemplo de saída:**
```
🌱 Seeding demo data...

Creating admin user...
Creating trial users...
Creating premium users...
Creating QR codes...
Creating teams...
Creating support tickets...

✅ Demo data seeding complete!

Test Accounts:
┌─────────────┬─────────────────────────┬──────────────┐
│ Role        │ Email                   │ Password     │
├─────────────┼─────────────────────────┼──────────────┤
│ Admin       │ admin@qrcodesaas.com    │ password123  │
│ Trial User  │ trial@example.com       │ password123  │
│ Premium     │ premium@example.com     │ password123  │
└─────────────┴─────────────────────────┴──────────────┘
```

### 🧪 Testes

#### Execução de Testes
```bash
# Executar todos os testes
php artisan test

# Executar testes específicos
php artisan test --filter=QrCodeControllerTest
php artisan test --filter=AuthenticationTest
php artisan test --filter=SubscriptionTest

# Executar com cobertura
php artisan test --coverage

# Executar em paralelo
php artisan test --parallel
```

**Exemplo de saída:**
```
🧪 Running tests...

PASS  Tests\Feature\QrCodeControllerTest
✓ can create qr code
✓ can view qr code
✓ can update qr code
✓ can delete qr code

PASS  Tests\Feature\AuthenticationTest
✓ can register user
✓ can login user
✓ can logout user

✅ Tests completed successfully!
```

## 🔍 Comandos de Diagnóstico

### Verificação do Sistema
```bash
# Informações gerais do sistema
php artisan about

# Mostrar configurações
php artisan config:show

# Listar todas as rotas
php artisan route:list

# Mostrar tabela de cache
php artisan cache:table
```

**Exemplo de saída do `php artisan about`:**
```
  Application Name     : QR Code SaaS
  Laravel Version      : 11.0.0
  PHP Version          : 8.2.15
  Environment          : production
  Debug Mode           : Off
  URL                  : https://qr.fluxti.com.br
  Timezone             : America/Sao_Paulo
  Locale               : pt_BR
  Fallback Locale      : en
  Maintenance Mode     : Off
```

### Verificação de Segurança
```bash
# Verificar logs de segurança
tail -f storage/logs/security.log

# Verificar configuração de segurança
php artisan security:audit --check
```

## 📊 Comandos de Monitoramento

### Status dos Serviços
```bash
# Verificar status do sistema
php artisan system:status

# Verificar espaço em disco
php artisan system:disk-usage

# Verificar uso de memória
php artisan system:memory-usage
```

### Logs em Tempo Real
```bash
# Monitorar logs da aplicação
tail -f storage/logs/laravel.log

# Monitorar logs de segurança
tail -f storage/logs/security.log

# Monitorar logs de pagamento
tail -f storage/logs/payment.log

# Monitorar logs do WhatsApp
tail -f storage/logs/whatsapp.log
```

## 🚨 Comandos de Emergência

### Manutenção
```bash
# Ativar modo de manutenção
php artisan down

# Ativar com mensagem personalizada
php artisan down --message="Manutenção programada"

# Desativar modo de manutenção
php artisan up
```

### Recuperação
```bash
# Limpar todos os caches
php artisan cache:clear-all

# Recriar caches
php artisan optimize

# Verificar integridade do banco
php artisan db:optimize --analyze

# Reprocessar jobs falhados
php artisan queue:retry all
```

## 📝 Exemplos de Uso em Produção

### Deploy Diário
```bash
# 1. Backup automático (já incluído no script)
./deploy/shared-hosting-deploy.sh

# 2. Verificar configuração
php artisan shared-hosting:configure --check

# 3. Otimizar banco
php artisan db:optimize --analyze

# 4. Limpar cache antigo
php artisan cache:clear-all

# 5. Verificar logs de segurança
php artisan security:audit
```

### Manutenção Semanal
```bash
# 1. Limpeza completa
php artisan shared-hosting:cleanup --force

# 2. Otimização completa do banco
php artisan db:optimize --analyze --vacuum

# 3. Auditoria de segurança
php artisan security:audit --export

# 4. Monitoramento de performance
php artisan performance:monitor --export
```

### Troubleshooting
```bash
# 1. Verificar status geral
php artisan about

# 2. Verificar configurações
php artisan config:show

# 3. Verificar logs de erro
tail -f storage/logs/laravel.log

# 4. Verificar filas
php artisan queue:work --once

# 5. Verificar banco
php artisan tinker
>>> DB::connection()->getPdo();
```

## 🔧 Configuração de Cron Jobs

### Crontab Recomendado
```bash
# Editar crontab
crontab -e

# Adicionar estas linhas:
* * * * * cd /home/usuario/public_html/qr.fluxti.com.br && php artisan schedule:run >> /dev/null 2>&1
0 2 * * * cd /home/usuario/public_html/qr.fluxti.com.br && php artisan queue:prune-failed >> /dev/null 2>&1
0 3 * * * cd /home/usuario/public_html/qr.fluxti.com.br && php artisan cache:clear-all >> /dev/null 2>&1
0 4 * * * cd /home/usuario/public_html/qr.fluxti.com.br && php artisan shared-hosting:cleanup >> /dev/null 2>&1
0 5 * * * cd /home/usuario/public_html/qr.fluxti.com.br && php artisan subscriptions:expire-trials >> /dev/null 2>&1
0 6 * * * cd /home/usuario/public_html/qr.fluxti.com.br && php artisan db:optimize --analyze >> /dev/null 2>&1
```

## 📞 Suporte

Para dúvidas sobre comandos Artisan:
- **Email**: support@qr.fluxti.com.br
- **WhatsApp**: +55 11 99999-9999
- **Documentação**: https://docs.qr.fluxti.com.br

---

**Este guia contém todos os comandos Artisan disponíveis no sistema QR Code SaaS com exemplos práticos de uso.**
