#!/bin/bash

# QR Code SaaS Platform - Deploy Script
# This script automates the deployment process for production

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
APP_NAME="qrcodesaas"
APP_PATH="/var/www/$APP_NAME"
BACKUP_PATH="/var/backups/$APP_NAME"
NGINX_CONFIG="/etc/nginx/sites-available/$APP_NAME"
NGINX_ENABLED="/etc/nginx/sites-enabled/$APP_NAME"
PHP_FPM_CONFIG="/etc/php/8.2/fpm/pool.d/$APP_NAME.conf"
SUPERVISOR_CONFIG="/etc/supervisor/conf.d/$APP_NAME.conf"

# Functions
log() {
    echo -e "${BLUE}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1"
}

success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1"
    exit 1
}

# Check if running as root
check_root() {
    if [[ $EUID -ne 0 ]]; then
        error "This script must be run as root"
    fi
}

# Check system requirements
check_requirements() {
    log "Checking system requirements..."
    
    # Check if required packages are installed
    local required_packages=("nginx" "php8.2-fpm" "php8.2-cli" "php8.2-mysql" "php8.2-xml" "php8.2-mbstring" "php8.2-curl" "php8.2-zip" "php8.2-gd" "php8.2-redis" "postgresql" "redis-server" "supervisor" "certbot")
    
    for package in "${required_packages[@]}"; do
        if ! dpkg -l | grep -q "^ii  $package "; then
            error "Required package $package is not installed"
        fi
    done
    
    success "All required packages are installed"
}

# Create backup
create_backup() {
    log "Creating backup..."
    
    local backup_dir="$BACKUP_PATH/$(date +%Y%m%d_%H%M%S)"
    mkdir -p "$backup_dir"
    
    # Backup application files
    if [ -d "$APP_PATH" ]; then
        cp -r "$APP_PATH" "$backup_dir/app"
        success "Application files backed up to $backup_dir"
    fi
    
    # Backup database
    if [ -f "$APP_PATH/.env" ]; then
        source "$APP_PATH/.env"
        pg_dump -h "$DB_HOST" -U "$DB_USERNAME" -d "$DB_DATABASE" > "$backup_dir/database.sql"
        success "Database backed up to $backup_dir/database.sql"
    fi
    
    # Keep only last 5 backups
    cd "$BACKUP_PATH"
    ls -t | tail -n +6 | xargs -r rm -rf
}

# Deploy application
deploy_app() {
    log "Deploying application..."
    
    # Create application directory
    mkdir -p "$APP_PATH"
    cd "$APP_PATH"
    
    # Clone or update repository
    if [ -d ".git" ]; then
        git pull origin main
    else
        git clone https://github.com/yourusername/qrcodesaas.git .
    fi
    
    # Install/update dependencies
    composer install --no-dev --optimize-autoloader
    npm ci --production
    
    # Build assets
    npm run production
    
    # Set permissions
    chown -R www-data:www-data "$APP_PATH"
    chmod -R 755 "$APP_PATH"
    chmod -R 775 "$APP_PATH/storage"
    chmod -R 775 "$APP_PATH/bootstrap/cache"
    
    success "Application deployed successfully"
}

# Configure environment
configure_env() {
    log "Configuring environment..."
    
    if [ ! -f "$APP_PATH/.env" ]; then
        cp "$APP_PATH/.env.example" "$APP_PATH/.env"
        warning "Please configure .env file with your settings"
    fi
    
    # Generate application key
    cd "$APP_PATH"
    php artisan key:generate --force
    
    # Cache configuration
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    success "Environment configured"
}

# Run database migrations
run_migrations() {
    log "Running database migrations..."
    
    cd "$APP_PATH"
    php artisan migrate --force
    
    # Run seeders if needed
    if [ "$1" = "--seed" ]; then
        php artisan db:seed --force
    fi
    
    success "Database migrations completed"
}

# Configure Nginx
configure_nginx() {
    log "Configuring Nginx..."
    
    # Copy Nginx configuration
    cp "$APP_PATH/deploy/nginx.conf" "$NGINX_CONFIG"
    
    # Enable site
    ln -sf "$NGINX_CONFIG" "$NGINX_ENABLED"
    
    # Test configuration
    nginx -t || error "Nginx configuration test failed"
    
    # Reload Nginx
    systemctl reload nginx
    
    success "Nginx configured and reloaded"
}

# Configure PHP-FPM
configure_php_fpm() {
    log "Configuring PHP-FPM..."
    
    cat > "$PHP_FPM_CONFIG" << EOF
[$APP_NAME]
user = www-data
group = www-data
listen = /var/run/php/php8.2-fpm-$APP_NAME.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
pm.max_requests = 1000
pm.process_idle_timeout = 10s
request_terminate_timeout = 300s
php_admin_value[error_log] = /var/log/php8.2-fpm-$APP_NAME.log
php_admin_flag[log_errors] = on
php_value[session.save_handler] = redis
php_value[session.save_path] = "tcp://127.0.0.1:6379"
php_value[opcache.enable] = 1
php_value[opcache.memory_consumption] = 128
php_value[opcache.interned_strings_buffer] = 8
php_value[opcache.max_accelerated_files] = 4000
php_value[opcache.revalidate_freq] = 2
php_value[opcache.fast_shutdown] = 1
EOF
    
    # Reload PHP-FPM
    systemctl reload php8.2-fpm
    
    success "PHP-FPM configured and reloaded"
}

# Configure Supervisor
configure_supervisor() {
    log "Configuring Supervisor..."
    
    cat > "$SUPERVISOR_CONFIG" << EOF
[program:$APP_NAME-worker]
process_name=%(program_name)s_%(process_num)02d
command=php $APP_PATH/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=$APP_PATH/storage/logs/worker.log
stopwaitsecs=3600

[program:$APP_NAME-scheduler]
process_name=%(program_name)s
command=php $APP_PATH/artisan schedule:work
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
redirect_stderr=true
stdout_logfile=$APP_PATH/storage/logs/scheduler.log
EOF
    
    # Reload Supervisor
    supervisorctl reread
    supervisorctl update
    supervisorctl start "$APP_NAME-worker:*"
    supervisorctl start "$APP_NAME-scheduler"
    
    success "Supervisor configured and started"
}

# Configure SSL
configure_ssl() {
    log "Configuring SSL..."
    
    # Install SSL certificate
    certbot --nginx -d qrcodesaas.com -d www.qrcodesaas.com --non-interactive --agree-tos --email admin@qrcodesaas.com
    
    # Setup auto-renewal
    (crontab -l 2>/dev/null; echo "0 12 * * * /usr/bin/certbot renew --quiet") | crontab -
    
    success "SSL configured"
}

# Setup cron jobs
setup_cron() {
    log "Setting up cron jobs..."
    
    # Add Laravel scheduler to crontab
    (crontab -l 2>/dev/null; echo "* * * * * cd $APP_PATH && php artisan schedule:run >> /dev/null 2>&1") | crontab -
    
    # Add cleanup jobs
    (crontab -l 2>/dev/null; echo "0 2 * * * cd $APP_PATH && php artisan queue:prune-failed >> /dev/null 2>&1") | crontab -
    (crontab -l 2>/dev/null; echo "0 3 * * * cd $APP_PATH && php artisan cache:clear-all >> /dev/null 2>&1") | crontab -
    
    success "Cron jobs configured"
}

# Setup log rotation
setup_log_rotation() {
    log "Setting up log rotation..."
    
    cat > "/etc/logrotate.d/$APP_NAME" << EOF
$APP_PATH/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
    postrotate
        systemctl reload php8.2-fpm
    endscript
}
EOF
    
    success "Log rotation configured"
}

# Health check
health_check() {
    log "Performing health check..."
    
    # Check if services are running
    systemctl is-active --quiet nginx || error "Nginx is not running"
    systemctl is-active --quiet php8.2-fpm || error "PHP-FPM is not running"
    systemctl is-active --quiet postgresql || error "PostgreSQL is not running"
    systemctl is-active --quiet redis-server || error "Redis is not running"
    
    # Check if application is responding
    curl -f http://localhost/health > /dev/null 2>&1 || error "Application health check failed"
    
    success "Health check passed"
}

# Main deployment function
main() {
    log "Starting deployment of $APP_NAME..."
    
    check_root
    check_requirements
    create_backup
    deploy_app
    configure_env
    run_migrations "$@"
    configure_nginx
    configure_php_fpm
    configure_supervisor
    configure_ssl
    setup_cron
    setup_log_rotation
    health_check
    
    success "Deployment completed successfully!"
    log "Application is now running at https://qrcodesaas.com"
}

# Run main function with all arguments
main "$@"
