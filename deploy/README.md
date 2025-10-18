# QR Code SaaS Platform - Deploy Guide

Este guia fornece instruções completas para fazer o deploy da plataforma QR Code SaaS em um servidor de produção.

## 📋 Pré-requisitos

### Servidor
- Ubuntu 20.04 LTS ou superior
- Mínimo 2GB RAM, 4GB recomendado
- Mínimo 20GB de espaço em disco
- Acesso root ou sudo

### Software Necessário
- Nginx 1.18+
- PHP 8.2+
- PostgreSQL 15+
- Redis 6+
- Node.js 18+
- Composer 2+
- Git
- Certbot (Let's Encrypt)
- Supervisor

## 🚀 Instalação Automática

### 1. Executar Script de Deploy

```bash
# Clonar o repositório
git clone https://github.com/yourusername/qrcodesaas.git /var/www/qrcodesaas
cd /var/www/qrcodesaas

# Executar script de deploy
sudo chmod +x deploy/deploy.sh
sudo ./deploy/deploy.sh
```

### 2. Configurar Variáveis de Ambiente

```bash
# Copiar arquivo de exemplo
cp deploy/env.production.example .env

# Editar configurações
nano .env
```

**Configurações obrigatórias:**
- `APP_KEY` - Chave da aplicação
- `DB_PASSWORD` - Senha do banco de dados
- `MERCADOPAGO_ACCESS_TOKEN` - Token do Mercado Pago
- `WHATSAPP_ACCESS_TOKEN` - Token do WhatsApp Business API
- `RECAPTCHA_SECRET_KEY` - Chave secreta do reCAPTCHA

### 3. Executar Migrations

```bash
cd /var/www/qrcodesaas
php artisan migrate --force
php artisan db:seed --force
```

## 🔧 Instalação Manual

### 1. Instalar Dependências do Sistema

```bash
# Atualizar sistema
sudo apt update && sudo apt upgrade -y

# Instalar Nginx
sudo apt install nginx -y

# Instalar PHP 8.2
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install php8.2-fpm php8.2-cli php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd php8.2-redis -y

# Instalar PostgreSQL
sudo apt install postgresql postgresql-contrib -y

# Instalar Redis
sudo apt install redis-server -y

# Instalar Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install nodejs -y

# Instalar Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Instalar Supervisor
sudo apt install supervisor -y

# Instalar Certbot
sudo apt install certbot python3-certbot-nginx -y
```

### 2. Configurar PostgreSQL

```bash
# Acessar PostgreSQL
sudo -u postgres psql

# Criar banco de dados e usuário
CREATE DATABASE qrcodesaas_production;
CREATE USER qrcodesaas_user WITH PASSWORD 'sua_senha_segura';
GRANT ALL PRIVILEGES ON DATABASE qrcodesaas_production TO qrcodesaas_user;
\q
```

### 3. Configurar Nginx

```bash
# Copiar configuração
sudo cp /var/www/qrcodesaas/deploy/nginx.conf /etc/nginx/sites-available/qrcodesaas

# Habilitar site
sudo ln -s /etc/nginx/sites-available/qrcodesaas /etc/nginx/sites-enabled/

# Remover site padrão
sudo rm /etc/nginx/sites-enabled/default

# Testar configuração
sudo nginx -t

# Recarregar Nginx
sudo systemctl reload nginx
```

### 4. Configurar SSL

```bash
# Obter certificado SSL
sudo certbot --nginx -d qrcodesaas.com -d www.qrcodesaas.com

# Configurar renovação automática
sudo crontab -e
# Adicionar: 0 12 * * * /usr/bin/certbot renew --quiet
```

### 5. Configurar Supervisor

```bash
# Copiar configuração
sudo cp /var/www/qrcodesaas/deploy/supervisor.conf /etc/supervisor/conf.d/qrcodesaas.conf

# Recarregar Supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start qrcodesaas-worker:*
sudo supervisorctl start qrcodesaas-scheduler
```

## 🔒 Configurações de Segurança

### 1. Firewall

```bash
# Configurar UFW
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw enable
```

### 2. Configurar Logs de Segurança

```bash
# Executar auditoria de segurança
cd /var/www/qrcodesaas
php artisan security:audit

# Configurar rotação de logs
sudo cp /var/www/qrcodesaas/deploy/logrotate.conf /etc/logrotate.d/qrcodesaas
```

### 3. Monitoramento

```bash
# Verificar status dos serviços
sudo systemctl status nginx
sudo systemctl status php8.2-fpm
sudo systemctl status postgresql
sudo systemctl status redis-server
sudo supervisorctl status
```

## 📊 Monitoramento e Manutenção

### 1. Comandos Úteis

```bash
# Verificar logs
tail -f /var/www/qrcodesaas/storage/logs/laravel.log
tail -f /var/www/qrcodesaas/storage/logs/security.log

# Limpar cache
php artisan cache:clear-all

# Otimizar banco de dados
php artisan db:optimize --analyze --vacuum

# Monitorar performance
php artisan performance:monitor

# Executar auditoria de segurança
php artisan security:audit --export
```

### 2. Backup

```bash
# Backup do banco de dados
pg_dump -h localhost -U qrcodesaas_user qrcodesaas_production > backup_$(date +%Y%m%d_%H%M%S).sql

# Backup dos arquivos
tar -czf backup_files_$(date +%Y%m%d_%H%M%S).tar.gz /var/www/qrcodesaas
```

### 3. Atualizações

```bash
# Atualizar aplicação
cd /var/www/qrcodesaas
git pull origin main
composer install --no-dev --optimize-autoloader
npm ci --production
npm run production
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🚨 Troubleshooting

### Problemas Comuns

1. **Erro 502 Bad Gateway**
   ```bash
   sudo systemctl restart php8.2-fpm
   sudo systemctl restart nginx
   ```

2. **Erro de permissões**
   ```bash
   sudo chown -R www-data:www-data /var/www/qrcodesaas
   sudo chmod -R 755 /var/www/qrcodesaas
   sudo chmod -R 775 /var/www/qrcodesaas/storage
   sudo chmod -R 775 /var/www/qrcodesaas/bootstrap/cache
   ```

3. **Problemas de SSL**
   ```bash
   sudo certbot renew --dry-run
   sudo systemctl reload nginx
   ```

4. **Problemas de filas**
   ```bash
   sudo supervisorctl restart qrcodesaas-worker:*
   php artisan queue:restart
   ```

### Logs Importantes

- **Nginx**: `/var/log/nginx/qrcodesaas_error.log`
- **PHP-FPM**: `/var/log/php8.2-fpm.log`
- **Aplicação**: `/var/www/qrcodesaas/storage/logs/laravel.log`
- **Segurança**: `/var/www/qrcodesaas/storage/logs/security.log`
- **Supervisor**: `/var/www/qrcodesaas/storage/logs/worker.log`

## 📈 Otimizações de Performance

### 1. Configurações do PHP

```bash
# Editar php.ini
sudo nano /etc/php/8.2/fpm/php.ini

# Configurações recomendadas:
memory_limit = 256M
max_execution_time = 300
upload_max_filesize = 10M
post_max_size = 10M
opcache.enable = 1
opcache.memory_consumption = 128
opcache.max_accelerated_files = 4000
```

### 2. Configurações do PostgreSQL

```bash
# Editar postgresql.conf
sudo nano /etc/postgresql/15/main/postgresql.conf

# Configurações recomendadas:
shared_buffers = 256MB
effective_cache_size = 1GB
work_mem = 4MB
maintenance_work_mem = 64MB
```

### 3. Configurações do Redis

```bash
# Editar redis.conf
sudo nano /etc/redis/redis.conf

# Configurações recomendadas:
maxmemory 256mb
maxmemory-policy allkeys-lru
```

## 🔐 Segurança Adicional

### 1. Configurar Fail2Ban

```bash
sudo apt install fail2ban -y
sudo cp /etc/fail2ban/jail.conf /etc/fail2ban/jail.local

# Editar configuração
sudo nano /etc/fail2ban/jail.local

# Adicionar regras para Laravel
[laravel-auth]
enabled = true
port = http,https
filter = laravel-auth
logpath = /var/www/qrcodesaas/storage/logs/laravel.log
maxretry = 3
bantime = 3600
```

### 2. Configurar ModSecurity

```bash
sudo apt install libapache2-mod-security2 -y
sudo a2enmod security2
sudo systemctl restart apache2
```

## 📞 Suporte

Para suporte técnico:
- Email: support@qrcodesaas.com
- WhatsApp: +55 11 99999-9999
- Documentação: https://docs.qrcodesaas.com

## 📄 Licença

Este projeto está licenciado sob a licença MIT. Veja o arquivo LICENSE para mais detalhes.
