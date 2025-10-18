# QR Code SaaS Platform - Deploy em Servidor Compartilhado

Este guia fornece instruções específicas para fazer o deploy da plataforma QR Code SaaS em um servidor compartilhado com Apache.

## 📋 Pré-requisitos do Servidor Compartilhado

### Requisitos Mínimos
- PHP 8.2 ou superior
- PostgreSQL 12+ (ou MySQL 8+)
- Apache com mod_rewrite habilitado
- Composer disponível
- Node.js 18+ (opcional, para compilação de assets)
- SSL/HTTPS configurado

### Extensões PHP Necessárias
- `pdo_pgsql` ou `pdo_mysql`
- `gd` ou `imagick`
- `curl`
- `openssl`
- `json`
- `mbstring`
- `xml`
- `fileinfo`
- `zip` (opcional)
- `exif` (opcional)

### Extensões PHP Opcionais (para melhor performance)
- `redis` (para cache)
- `memcached` (para cache)

## 🚀 Deploy Automático

### 1. Preparar o Servidor

```bash
# Conectar via SSH ao servidor compartilhado
ssh usuario@servidor.com

# Navegar para o diretório do domínio
cd /home/usuario/public_html/qr.fluxti.com.br
```

### 2. Executar Script de Deploy

```bash
# Clonar o repositório
git clone https://github.com/yourusername/qrcodesaas.git .

# Executar script de deploy
chmod +x deploy/shared-hosting-deploy.sh
./deploy/shared-hosting-deploy.sh
```

### 3. Configurar Variáveis de Ambiente

```bash
# Copiar arquivo de exemplo
cp deploy/env.shared-hosting.example .env

# Editar configurações
nano .env
```

**Configurações obrigatórias:**
- `APP_KEY` - Chave da aplicação
- `DB_PASSWORD` - Senha do banco de dados
- `MERCADOPAGO_ACCESS_TOKEN` - Token do Mercado Pago
- `WHATSAPP_ACCESS_TOKEN` - Token do WhatsApp Business API
- `RECAPTCHA_SECRET_KEY` - Chave secreta do reCAPTCHA

## 🔧 Deploy Manual

### 1. Configurar Banco de Dados

```sql
-- Criar banco de dados PostgreSQL
CREATE DATABASE qr_fluxti_production;
CREATE USER qr_fluxti_user WITH PASSWORD 'sua_senha_segura';
GRANT ALL PRIVILEGES ON DATABASE qr_fluxti_production TO qr_fluxti_user;
```

### 2. Configurar Apache

```bash
# Copiar .htaccess
cp deploy/.htaccess public/.htaccess

# Verificar se mod_rewrite está habilitado
apache2ctl -M | grep rewrite
```

### 3. Configurar Aplicação

```bash
# Instalar dependências
composer install --no-dev --optimize-autoloader

# Configurar aplicação
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Executar migrations
php artisan migrate --force

# Criar link simbólico para storage
php artisan storage:link
```

### 4. Configurar Permissões

```bash
# Definir permissões corretas
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

## ⚙️ Configurações Específicas para Servidor Compartilhado

### 1. Configurar Cache

```bash
# Verificar configuração de cache
php artisan shared-hosting:configure --check

# Configurar para servidor compartilhado
php artisan shared-hosting:configure
```

### 2. Configurar Filas

Em servidor compartilhado, as filas são executadas de forma síncrona:

```php
// config/queue.php
'default' => env('QUEUE_CONNECTION', 'sync'),
```

### 3. Configurar Sessões

```php
// config/session.php
'driver' => env('SESSION_DRIVER', 'file'),
'lifetime' => env('SESSION_LIFETIME', 120),
'secure' => env('SESSION_SECURE_COOKIE', true),
'http_only' => true,
'same_site' => 'lax',
```

### 4. Configurar Storage

```php
// config/filesystems.php
'default' => env('FILESYSTEM_DISK', 'local'),
'disks' => [
    'local' => [
        'driver' => 'local',
        'root' => storage_path('app'),
    ],
    'public' => [
        'driver' => 'local',
        'root' => storage_path('app/public'),
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
],
```

## 🔒 Configurações de Segurança

### 1. Headers de Segurança

O arquivo `.htaccess` já inclui headers de segurança:
- X-Content-Type-Options
- X-Frame-Options
- X-XSS-Protection
- Strict-Transport-Security
- Content-Security-Policy

### 2. Bloqueio de Arquivos Sensíveis

```apache
# Bloquear acesso a arquivos sensíveis
<FilesMatch "\.(env|log|sql|bak|backup|old|tmp)$">
    Order allow,deny
    Deny from all
</FilesMatch>
```

### 3. Configurações PHP

```ini
# php.ini ou .htaccess
expose_php = Off
allow_url_fopen = Off
allow_url_include = Off
display_errors = Off
log_errors = On
```

## 📊 Monitoramento e Manutenção

### 1. Comandos Úteis

```bash
# Verificar configuração do servidor
php artisan shared-hosting:configure --check

# Limpar cache
php artisan cache:clear-all

# Otimizar aplicação
php artisan optimize

# Verificar logs
tail -f storage/logs/laravel.log
```

### 2. Backup

```bash
# Backup dos arquivos
tar -czf backup_$(date +%Y%m%d_%H%M%S).tar.gz .

# Backup do banco de dados
pg_dump -h localhost -U qr_fluxti_user qr_fluxti_production > backup_db_$(date +%Y%m%d_%H%M%S).sql
```

### 3. Limpeza Automática

```bash
# Adicionar ao crontab
crontab -e

# Adicionar estas linhas:
* * * * * cd /home/usuario/public_html/qr.fluxti.com.br && php artisan schedule:run >> /dev/null 2>&1
0 2 * * * cd /home/usuario/public_html/qr.fluxti.com.br && php artisan cache:clear-all >> /dev/null 2>&1
0 3 * * * find /home/usuario/public_html/qr.fluxti.com.br/storage/logs -name "*.log" -mtime +7 -delete
```

## 🚨 Troubleshooting

### Problemas Comuns

1. **Erro 500 Internal Server Error**
   ```bash
   # Verificar logs de erro
   tail -f /var/log/apache2/error.log
   tail -f storage/logs/laravel.log
   
   # Verificar permissões
   chmod -R 755 storage
   chmod -R 755 bootstrap/cache
   ```

2. **Erro de permissões**
   ```bash
   # Corrigir permissões
   find . -type f -exec chmod 644 {} \;
   find . -type d -exec chmod 755 {} \;
   chmod -R 755 storage
   chmod -R 755 bootstrap/cache
   ```

3. **Problemas de cache**
   ```bash
   # Limpar todos os caches
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   
   # Recriar caches
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

4. **Problemas de storage**
   ```bash
   # Recriar link simbólico
   rm public/storage
   php artisan storage:link
   ```

### Logs Importantes

- **Apache**: `/var/log/apache2/error.log`
- **Aplicação**: `storage/logs/laravel.log`
- **Segurança**: `storage/logs/security.log`
- **Pagamentos**: `storage/logs/payment.log`

## 📈 Otimizações de Performance

### 1. Configurações PHP

```ini
# Otimizações para servidor compartilhado
memory_limit = 256M
max_execution_time = 300
upload_max_filesize = 10M
post_max_size = 10M
max_input_vars = 3000
max_file_uploads = 20

# OPcache (se disponível)
opcache.enable = 1
opcache.memory_consumption = 64
opcache.max_accelerated_files = 2000
opcache.revalidate_freq = 2
```

### 2. Configurações de Cache

```php
// config/cache.php
'default' => env('CACHE_DRIVER', 'file'),
'stores' => [
    'file' => [
        'driver' => 'file',
        'path' => storage_path('framework/cache/data'),
    ],
],
```

### 3. Configurações de Sessão

```php
// config/session.php
'driver' => env('SESSION_DRIVER', 'file'),
'lifetime' => env('SESSION_LIFETIME', 120),
'files' => storage_path('framework/sessions'),
```

## 🔐 Segurança Adicional

### 1. Configurar Firewall

```bash
# Se disponível no servidor compartilhado
ufw allow ssh
ufw allow 'Apache Full'
ufw enable
```

### 2. Configurar Fail2Ban

```bash
# Se disponível no servidor compartilhado
sudo apt install fail2ban -y
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

### 3. Monitoramento de Segurança

```bash
# Executar auditoria de segurança
php artisan security:audit

# Exportar relatório
php artisan security:audit --export
```

## 📞 Suporte

Para suporte técnico específico de servidor compartilhado:
- Email: support@qr.fluxti.com.br
- WhatsApp: +55 11 99999-9999
- Documentação: https://docs.qr.fluxti.com.br

## 📄 Notas Importantes

1. **Limitações do Servidor Compartilhado:**
   - Filas executam de forma síncrona
   - Cache limitado ao sistema de arquivos
   - Recursos de CPU e memória compartilhados
   - Sem acesso root para configurações avançadas

2. **Recomendações:**
   - Monitore o uso de recursos regularmente
   - Faça backups frequentes
   - Mantenha logs limpos
   - Use cache agressivamente

3. **Upgrade para VPS:**
   - Considere upgrade para VPS quando o tráfego aumentar
   - VPS oferece melhor performance e controle
   - Permite uso de Redis e filas assíncronas
