# Guia de Configuração de Produção - QR Code SaaS Platform

## 📋 Visão Geral

Este guia fornece instruções detalhadas para configurar o sistema QR Code SaaS em ambiente de produção, incluindo servidor compartilhado e VPS.

## 🖥️ Configuração para Servidor Compartilhado

### 1. Requisitos do Servidor

#### Requisitos Mínimos
- **PHP**: 8.2 ou superior
- **PostgreSQL**: 12+ (ou MySQL 8+)
- **Apache**: com mod_rewrite habilitado
- **Composer**: disponível
- **Node.js**: 18+ (opcional)
- **SSL/HTTPS**: configurado

#### Extensões PHP Necessárias
```bash
# Verificar extensões instaladas
php -m | grep -E "(pdo_pgsql|gd|curl|openssl|json|mbstring|xml|fileinfo)"
```

**Extensões obrigatórias:**
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

### 2. Deploy Automático

#### Script de Deploy
```bash
# 1. Conectar ao servidor
ssh usuario@servidor.com

# 2. Navegar para o diretório
cd /home/usuario/public_html/qr.fluxti.com.br

# 3. Clonar repositório
git clone https://github.com/yourusername/qrcodesaas.git .

# 4. Executar deploy
chmod +x deploy/shared-hosting-deploy.sh
./deploy/shared-hosting-deploy.sh
```

#### Deploy Manual
```bash
# 1. Instalar dependências
composer install --no-dev --optimize-autoloader

# 2. Configurar ambiente
cp deploy/env.shared-hosting.example .env
nano .env

# 3. Configurar Apache
cp deploy/.htaccess public/.htaccess

# 4. Configurar aplicação
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Executar migrations
php artisan migrate --force

# 6. Criar link de storage
php artisan storage:link

# 7. Configurar permissões
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

### 3. Configuração do Banco de Dados

#### PostgreSQL
```sql
-- Criar banco de dados
CREATE DATABASE qr_fluxti_production;

-- Criar usuário
CREATE USER qr_fluxti_user WITH PASSWORD 'sua_senha_segura';

-- Conceder permissões
GRANT ALL PRIVILEGES ON DATABASE qr_fluxti_production TO qr_fluxti_user;

-- Conectar ao banco
\c qr_fluxti_production;

-- Conceder permissões no schema
GRANT ALL ON SCHEMA public TO qr_fluxti_user;
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO qr_fluxti_user;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO qr_fluxti_user;
```

#### MySQL (Alternativa)
```sql
-- Criar banco de dados
CREATE DATABASE qr_fluxti_production CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Criar usuário
CREATE USER 'qr_fluxti_user'@'localhost' IDENTIFIED BY 'sua_senha_segura';

-- Conceder permissões
GRANT ALL PRIVILEGES ON qr_fluxti_production.* TO 'qr_fluxti_user'@'localhost';
FLUSH PRIVILEGES;
```

### 4. Configuração do Apache

#### Virtual Host
```apache
<VirtualHost *:443>
    ServerName qr.fluxti.com.br
    ServerAlias www.qr.fluxti.com.br
    DocumentRoot /home/usuario/public_html/qr.fluxti.com.br/public
    
    # SSL Configuration
    SSLEngine on
    SSLCertificateFile /path/to/ssl/qr.fluxti.com.br.crt
    SSLCertificateKeyFile /path/to/ssl/qr.fluxti.com.br.key
    SSLCertificateChainFile /path/to/ssl/qr.fluxti.com.br.chain.crt
    
    # Security Headers
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
    
    # PHP Configuration
    <IfModule mod_php8.c>
        php_value upload_max_filesize 10M
        php_value post_max_size 10M
        php_value max_execution_time 300
        php_value memory_limit 256M
        php_value session.cookie_secure 1
        php_value session.cookie_httponly 1
        php_value date.timezone "America/Sao_Paulo"
        php_flag display_errors Off
        php_flag log_errors On
        php_value expose_php Off
    </IfModule>
    
    # Directory Configuration
    <Directory "/home/usuario/public_html/qr.fluxti.com.br/public">
        AllowOverride All
        Require all granted
        
        # Block sensitive files
        <FilesMatch "\.(env|log|sql|bak|backup|old|tmp)$">
            Require all denied
        </FilesMatch>
    </Directory>
    
    # Logs
    ErrorLog /home/usuario/logs/qr.fluxti.com.br_error.log
    CustomLog /home/usuario/logs/qr.fluxti.com.br_access.log combined
</VirtualHost>
```

### 5. Configuração de SSL

#### Let's Encrypt (Recomendado)
```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-apache

# Obter certificado
sudo certbot --apache -d qr.fluxti.com.br -d www.qr.fluxti.com.br

# Renovação automática
sudo crontab -e
# Adicionar: 0 12 * * * /usr/bin/certbot renew --quiet
```

#### Certificado Comercial
```bash
# Upload dos arquivos de certificado
# Configurar no Virtual Host do Apache
```

## 🖥️ Configuração para VPS/Dedicado

### 1. Requisitos do Servidor

#### Especificações Recomendadas
- **CPU**: 2+ cores
- **RAM**: 4GB+ (8GB recomendado)
- **Storage**: 50GB+ SSD
- **OS**: Ubuntu 22.04 LTS
- **Nginx**: 1.20+
- **PHP**: 8.2+ com PHP-FPM
- **PostgreSQL**: 15+
- **Redis**: 7.0+

### 2. Instalação do Sistema

#### Ubuntu 22.04 LTS
```bash
# Atualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar dependências
sudo apt install -y software-properties-common curl wget git unzip

# Adicionar repositórios
sudo add-apt-repository ppa:ondrej/php -y
sudo add-apt-repository ppa:ondrej/nginx -y
sudo apt update
```

#### Instalar PHP 8.2
```bash
# Instalar PHP e extensões
sudo apt install -y php8.2-fpm php8.2-cli php8.2-common php8.2-mysql \
    php8.2-pgsql php8.2-zip php8.2-gd php8.2-mbstring php8.2-curl \
    php8.2-xml php8.2-bcmath php8.2-json php8.2-tokenizer php8.2-fileinfo \
    php8.2-redis php8.2-imagick

# Configurar PHP-FPM
sudo systemctl enable php8.2-fpm
sudo systemctl start php8.2-fpm
```

#### Instalar Nginx
```bash
# Instalar Nginx
sudo apt install -y nginx

# Configurar Nginx
sudo systemctl enable nginx
sudo systemctl start nginx
```

#### Instalar PostgreSQL
```bash
# Instalar PostgreSQL
sudo apt install -y postgresql postgresql-contrib

# Configurar PostgreSQL
sudo systemctl enable postgresql
sudo systemctl start postgresql

# Criar banco e usuário
sudo -u postgres psql
CREATE DATABASE qr_fluxti_production;
CREATE USER qr_fluxti_user WITH PASSWORD 'sua_senha_segura';
GRANT ALL PRIVILEGES ON DATABASE qr_fluxti_production TO qr_fluxti_user;
\q
```

#### Instalar Redis
```bash
# Instalar Redis
sudo apt install -y redis-server

# Configurar Redis
sudo systemctl enable redis-server
sudo systemctl start redis-server
```

#### Instalar Composer
```bash
# Baixar e instalar Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer
```

#### Instalar Node.js
```bash
# Instalar Node.js 18
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

### 3. Deploy da Aplicação

#### Configurar Usuário
```bash
# Criar usuário para aplicação
sudo adduser qrcodesaas
sudo usermod -aG www-data qrcodesaas

# Criar diretório da aplicação
sudo mkdir -p /var/www/qrcodesaas
sudo chown -R qrcodesaas:www-data /var/www/qrcodesaas
```

#### Deploy
```bash
# Conectar como usuário da aplicação
sudo su - qrcodesaas

# Clonar repositório
cd /var/www/qrcodesaas
git clone https://github.com/yourusername/qrcodesaas.git .

# Instalar dependências
composer install --no-dev --optimize-autoloader
npm ci --production
npm run production

# Configurar ambiente
cp deploy/env.production.example .env
nano .env

# Configurar aplicação
php artisan key:generate
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
php artisan storage:link

# Configurar permissões
sudo chown -R qrcodesaas:www-data /var/www/qrcodesaas
sudo chmod -R 755 /var/www/qrcodesaas
sudo chmod -R 775 /var/www/qrcodesaas/storage
sudo chmod -R 775 /var/www/qrcodesaas/bootstrap/cache
```

### 4. Configuração do Nginx

#### Configuração Principal
```nginx
server {
    listen 80;
    server_name qr.fluxti.com.br www.qr.fluxti.com.br;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name qr.fluxti.com.br www.qr.fluxti.com.br;
    root /var/www/qrcodesaas/public;
    index index.php;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/qr.fluxti.com.br/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/qr.fluxti.com.br/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied expired no-cache no-store private must-revalidate auth;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/javascript;

    # Rate Limiting
    limit_req_zone $binary_remote_addr zone=login:10m rate=5r/m;
    limit_req_zone $binary_remote_addr zone=api:10m rate=100r/m;

    # Main Location
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP Processing
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Static Files
    location ~* \.(jpg|jpeg|gif|png|css|js|ico|xml)$ {
        expires 5d;
        add_header Cache-Control "public, immutable";
    }

    # Security
    location ~ /\.ht {
        deny all;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Rate Limiting
    location /login {
        limit_req zone=login burst=5 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
    }

    location /api/ {
        limit_req zone=api burst=20 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Logs
    access_log /var/log/nginx/qr.fluxti.com.br.access.log;
    error_log /var/log/nginx/qr.fluxti.com.br.error.log;
}

# Wildcard for Custom Domains
server {
    listen 443 ssl http2;
    server_name *.qr.fluxti.com.br;
    root /var/www/qrcodesaas/public;
    index index.php;

    # SSL Configuration (wildcard certificate)
    ssl_certificate /etc/letsencrypt/live/qr.fluxti.com.br/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/qr.fluxti.com.br/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:SSL:10m;
    ssl_session_timeout 10m;

    # Same configuration as main server
    # ... (copy from above)
}
```

### 5. Configuração do Supervisor

#### Instalar Supervisor
```bash
sudo apt install -y supervisor
```

#### Configuração do Worker
```ini
# /etc/supervisor/conf.d/qrcodesaas-worker.conf
[program:qrcodesaas-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/qrcodesaas/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=qrcodesaas
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/qrcodesaas/storage/logs/worker.log
stopwaitsecs=3600
```

#### Configuração do Scheduler
```ini
# /etc/supervisor/conf.d/qrcodesaas-scheduler.conf
[program:qrcodesaas-scheduler]
command=php /var/www/qrcodesaas/artisan schedule:work
autostart=true
autorestart=true
user=qrcodesaas
redirect_stderr=true
stdout_logfile=/var/www/qrcodesaas/storage/logs/scheduler.log
```

#### Iniciar Supervisor
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start all
```

### 6. Configuração de SSL

#### Let's Encrypt
```bash
# Instalar Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obter certificado
sudo certbot --nginx -d qr.fluxti.com.br -d www.qr.fluxti.com.br

# Obter certificado wildcard
sudo certbot certonly --manual --preferred-challenges dns -d qr.fluxti.com.br -d *.qr.fluxti.com.br

# Renovação automática
sudo crontab -e
# Adicionar: 0 12 * * * /usr/bin/certbot renew --quiet
```

## ⚙️ Configurações de Ambiente

### 1. Arquivo .env para Produção

#### Servidor Compartilhado
```env
APP_NAME="QR Code SaaS"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://qr.fluxti.com.br

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=qr_fluxti_production
DB_USERNAME=qr_fluxti_user
DB_PASSWORD=YOUR_SECURE_DB_PASSWORD

CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=smtp.fluxti.com.br
MAIL_PORT=587
MAIL_USERNAME=noreply@qr.fluxti.com.br
MAIL_PASSWORD=YOUR_MAIL_PASSWORD
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@qr.fluxti.com.br
MAIL_FROM_NAME="${APP_NAME}"

MERCADOPAGO_ACCESS_TOKEN=YOUR_MERCADOPAGO_ACCESS_TOKEN
MERCADOPAGO_PUBLIC_KEY=YOUR_MERCADOPAGO_PUBLIC_KEY
MERCADOPAGO_PLAN_ID=YOUR_MERCADOPAGO_PLAN_ID
MERCADOPAGO_WEBHOOK_SECRET=YOUR_MERCADOPAGO_WEBHOOK_SECRET

WHATSAPP_ACCESS_TOKEN=YOUR_WHATSAPP_ACCESS_TOKEN
WHATSAPP_PHONE_NUMBER_ID=YOUR_WHATSAPP_PHONE_NUMBER_ID
WHATSAPP_APP_ID=YOUR_WHATSAPP_APP_ID
WHATSAPP_APP_SECRET=YOUR_WHATSAPP_APP_SECRET

RECAPTCHA_SITE_KEY=YOUR_RECAPTCHA_SITE_KEY
RECAPTCHA_SECRET_KEY=YOUR_RECAPTCHA_SECRET_KEY
RECAPTCHA_MIN_SCORE=0.5

SHARED_HOSTING=true
```

#### VPS/Dedicado
```env
APP_NAME="QR Code SaaS"
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://qr.fluxti.com.br

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=qr_fluxti_production
DB_USERNAME=qr_fluxti_user
DB_PASSWORD=YOUR_SECURE_DB_PASSWORD

CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

MAIL_MAILER=smtp
MAIL_HOST=smtp.fluxti.com.br
MAIL_PORT=587
MAIL_USERNAME=noreply@qr.fluxti.com.br
MAIL_PASSWORD=YOUR_MAIL_PASSWORD
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@qr.fluxti.com.br
MAIL_FROM_NAME="${APP_NAME}"

MERCADOPAGO_ACCESS_TOKEN=YOUR_MERCADOPAGO_ACCESS_TOKEN
MERCADOPAGO_PUBLIC_KEY=YOUR_MERCADOPAGO_PUBLIC_KEY
MERCADOPAGO_PLAN_ID=YOUR_MERCADOPAGO_PLAN_ID
MERCADOPAGO_WEBHOOK_SECRET=YOUR_MERCADOPAGO_WEBHOOK_SECRET

WHATSAPP_ACCESS_TOKEN=YOUR_WHATSAPP_ACCESS_TOKEN
WHATSAPP_PHONE_NUMBER_ID=YOUR_WHATSAPP_PHONE_NUMBER_ID
WHATSAPP_APP_ID=YOUR_WHATSAPP_APP_ID
WHATSAPP_APP_SECRET=YOUR_WHATSAPP_APP_SECRET

RECAPTCHA_SITE_KEY=YOUR_RECAPTCHA_SITE_KEY
RECAPTCHA_SECRET_KEY=YOUR_RECAPTCHA_SECRET_KEY
RECAPTCHA_MIN_SCORE=0.5

SHARED_HOSTING=false
```

### 2. Configurações PHP

#### php.ini para Produção
```ini
# Memory and Execution
memory_limit = 256M
max_execution_time = 300
max_input_time = 300

# File Uploads
upload_max_filesize = 10M
post_max_size = 10M
max_file_uploads = 20

# Session
session.cookie_secure = 1
session.cookie_httponly = 1
session.cookie_samesite = "Strict"
session.use_strict_mode = 1

# Security
expose_php = Off
allow_url_fopen = Off
allow_url_include = Off
display_errors = Off
log_errors = On
error_log = /var/log/php/error.log

# OPcache
opcache.enable = 1
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 8
opcache.max_accelerated_files = 4000
opcache.revalidate_freq = 2
opcache.fast_shutdown = 1
opcache.enable_cli = 1

# Timezone
date.timezone = "America/Sao_Paulo"
```

### 3. Configurações PostgreSQL

#### postgresql.conf
```conf
# Memory
shared_buffers = 256MB
effective_cache_size = 1GB
work_mem = 4MB
maintenance_work_mem = 64MB

# Connections
max_connections = 100

# Logging
log_destination = 'stderr'
logging_collector = on
log_directory = 'pg_log'
log_filename = 'postgresql-%Y-%m-%d_%H%M%S.log'
log_statement = 'mod'
log_min_duration_statement = 1000

# Performance
random_page_cost = 1.1
effective_io_concurrency = 200
```

## 🔄 Cron Jobs

### 1. Crontab para Servidor Compartilhado
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

### 2. Crontab para VPS/Dedicado
```bash
# Editar crontab
sudo crontab -e

# Adicionar estas linhas:
* * * * * cd /var/www/qrcodesaas && php artisan schedule:run >> /dev/null 2>&1
0 2 * * * cd /var/www/qrcodesaas && php artisan queue:prune-failed >> /dev/null 2>&1
0 3 * * * cd /var/www/qrcodesaas && php artisan cache:clear-all >> /dev/null 2>&1
0 4 * * * cd /var/www/qrcodesaas && php artisan storage:cleanup >> /dev/null 2>&1
0 5 * * * cd /var/www/qrcodesaas && php artisan subscriptions:expire-trials >> /dev/null 2>&1
0 6 * * * cd /var/www/qrcodesaas && php artisan db:optimize --analyze --vacuum >> /dev/null 2>&1
```

## 🔒 Configurações de Segurança

### 1. Firewall (UFW)
```bash
# Instalar UFW
sudo apt install -y ufw

# Configurar regras
sudo ufw default deny incoming
sudo ufw default allow outgoing
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw allow 5432/tcp  # PostgreSQL (se necessário)
sudo ufw allow 6379/tcp  # Redis (se necessário)

# Ativar firewall
sudo ufw enable
```

### 2. Fail2Ban
```bash
# Instalar Fail2Ban
sudo apt install -y fail2ban

# Configurar
sudo cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

### 3. Configuração de Logs
```bash
# Configurar rotação de logs
sudo nano /etc/logrotate.d/qrcodesaas

# Conteúdo:
/var/www/qrcodesaas/storage/logs/*.log {
    daily
    missingok
    rotate 7
    compress
    delaycompress
    notifempty
    create 644 qrcodesaas www-data
    postrotate
        sudo systemctl reload php8.2-fpm
    endscript
}
```

## 📊 Monitoramento

### 1. Health Check
```bash
# Verificar status dos serviços
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
sudo systemctl status postgresql
sudo systemctl status redis-server
sudo systemctl status supervisor

# Verificar logs
sudo tail -f /var/log/nginx/qr.fluxti.com.br.error.log
sudo tail -f /var/www/qrcodesaas/storage/logs/laravel.log
```

### 2. Comandos de Monitoramento
```bash
# Verificar configuração
php artisan shared-hosting:configure --check

# Verificar performance
php artisan performance:monitor

# Verificar segurança
php artisan security:audit

# Verificar espaço em disco
df -h
du -sh /var/www/qrcodesaas/storage
```

## 🚨 Troubleshooting

### 1. Problemas Comuns

#### Erro 500 Internal Server Error
```bash
# Verificar logs
sudo tail -f /var/log/nginx/qr.fluxti.com.br.error.log
sudo tail -f /var/www/qrcodesaas/storage/logs/laravel.log

# Verificar permissões
sudo chown -R qrcodesaas:www-data /var/www/qrcodesaas
sudo chmod -R 755 /var/www/qrcodesaas
sudo chmod -R 775 /var/www/qrcodesaas/storage
sudo chmod -R 775 /var/www/qrcodesaas/bootstrap/cache

# Limpar cache
cd /var/www/qrcodesaas
php artisan cache:clear-all
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### Problemas de Performance
```bash
# Verificar uso de recursos
htop
free -h
df -h

# Otimizar banco
php artisan db:optimize --analyze --vacuum

# Limpar arquivos antigos
php artisan shared-hosting:cleanup --force
```

#### Problemas de Banco de Dados
```bash
# Verificar conexão
php artisan tinker
>>> DB::connection()->getPdo();

# Executar migrations
php artisan migrate --force

# Verificar logs do PostgreSQL
sudo tail -f /var/log/postgresql/postgresql-*.log
```

### 2. Comandos de Recuperação
```bash
# Backup de emergência
pg_dump -h localhost -U qr_fluxti_user qr_fluxti_production > backup_$(date +%Y%m%d_%H%M%S).sql

# Restaurar backup
psql -h localhost -U qr_fluxti_user qr_fluxti_production < backup_file.sql

# Recriar caches
php artisan optimize

# Reprocessar filas
php artisan queue:retry all
```

## 📞 Suporte

Para suporte técnico:
- **Email**: support@qr.fluxti.com.br
- **WhatsApp**: +55 11 99999-9999
- **Documentação**: https://docs.qr.fluxti.com.br

---

**Este guia fornece todas as instruções necessárias para configurar o sistema QR Code SaaS em ambiente de produção.**
