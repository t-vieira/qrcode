#!/bin/bash

# QR Code SaaS Platform - Shared Hosting Deploy Script
# Para servidor compartilhado - qr.fluxti.com.br

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
APP_NAME="qrcodesaas"
APP_PATH="/home/fluxti/public_html/qr.fluxti.com.br"
BACKUP_PATH="/home/fluxti/backups/qrcodesaas"
DOMAIN="qr.fluxti.com.br"

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

# Check if running as correct user
check_user() {
    if [[ $USER != "fluxti" ]]; then
        error "This script must be run as the 'fluxti' user"
    fi
}

# Check shared hosting requirements
check_requirements() {
    log "Checking shared hosting requirements..."
    
    # Check if required directories exist
    if [ ! -d "$APP_PATH" ]; then
        error "Application directory $APP_PATH does not exist"
    fi
    
    # Check if .htaccess is writable
    if [ ! -w "$APP_PATH" ]; then
        error "Application directory is not writable"
    fi
    
    # Check PHP version
    PHP_VERSION=$(php -v | head -n 1 | cut -d " " -f 2 | cut -d "." -f 1,2)
    if [[ $(echo "$PHP_VERSION < 8.2" | bc -l) -eq 1 ]]; then
        error "PHP 8.2 or higher is required. Current version: $PHP_VERSION"
    fi
    
    # Check if Composer is available
    if ! command -v composer &> /dev/null; then
        error "Composer is not installed or not in PATH"
    fi
    
    success "All requirements met"
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
    
    # Backup database (if accessible)
    if [ -f "$APP_PATH/.env" ]; then
        source "$APP_PATH/.env"
        if command -v pg_dump &> /dev/null; then
            pg_dump -h "$DB_HOST" -U "$DB_USERNAME" -d "$DB_DATABASE" > "$backup_dir/database.sql" 2>/dev/null || warning "Database backup failed"
        fi
    fi
    
    # Keep only last 3 backups
    cd "$BACKUP_PATH"
    ls -t | tail -n +4 | xargs -r rm -rf
}

# Deploy application
deploy_app() {
    log "Deploying application..."
    
    cd "$APP_PATH"
    
    # Clone or update repository
    if [ -d ".git" ]; then
        git pull origin main
    else
        git clone https://github.com/yourusername/qrcodesaas.git .
    fi
    
    # Install/update dependencies
    composer install --no-dev --optimize-autoloader --no-interaction
    
    # Install Node.js dependencies (if available)
    if command -v npm &> /dev/null; then
        npm ci --production --silent
        npm run production
    else
        warning "Node.js/npm not available, skipping asset compilation"
    fi
    
    # Set permissions
    find "$APP_PATH" -type f -exec chmod 644 {} \;
    find "$APP_PATH" -type d -exec chmod 755 {} \;
    chmod -R 755 "$APP_PATH/storage"
    chmod -R 755 "$APP_PATH/bootstrap/cache"
    
    success "Application deployed successfully"
}

# Configure environment
configure_env() {
    log "Configuring environment..."
    
    if [ ! -f "$APP_PATH/.env" ]; then
        cp "$APP_PATH/deploy/env.shared-hosting.example" "$APP_PATH/.env"
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

# Configure Apache
configure_apache() {
    log "Configuring Apache..."
    
    # Copy .htaccess
    cp "$APP_PATH/deploy/.htaccess" "$APP_PATH/public/.htaccess"
    
    # Create storage link
    cd "$APP_PATH"
    php artisan storage:link
    
    success "Apache configured"
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

# Health check
health_check() {
    log "Performing health check..."
    
    # Check if application is responding
    curl -f https://$DOMAIN/health > /dev/null 2>&1 || error "Application health check failed"
    
    # Check if .htaccess is working
    curl -f https://$DOMAIN/ > /dev/null 2>&1 || error "Application is not responding"
    
    success "Health check passed"
}

# Optimize for shared hosting
optimize_shared_hosting() {
    log "Optimizing for shared hosting..."
    
    cd "$APP_PATH"
    
    # Disable Telescope and Debug Bar in production
    php artisan telescope:install --no-interaction || true
    php artisan vendor:publish --tag=laravel-telescope-assets --no-interaction || true
    
    # Clear all caches
    php artisan cache:clear
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    
    # Re-cache for production
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    
    # Optimize autoloader
    composer dump-autoload --optimize --no-dev
    
    success "Shared hosting optimization completed"
}

# Setup file permissions
setup_permissions() {
    log "Setting up file permissions..."
    
    # Set proper permissions for shared hosting
    find "$APP_PATH" -type f -name "*.php" -exec chmod 644 {} \;
    find "$APP_PATH" -type d -exec chmod 755 {} \;
    
    # Special permissions for storage and cache
    chmod -R 755 "$APP_PATH/storage"
    chmod -R 755 "$APP_PATH/bootstrap/cache"
    
    # Make sure .htaccess is readable
    chmod 644 "$APP_PATH/public/.htaccess"
    
    success "File permissions configured"
}

# Create necessary directories
create_directories() {
    log "Creating necessary directories..."
    
    mkdir -p "$APP_PATH/storage/app/public/qrcodes"
    mkdir -p "$APP_PATH/storage/logs"
    mkdir -p "$APP_PATH/storage/framework/cache"
    mkdir -p "$APP_PATH/storage/framework/sessions"
    mkdir -p "$APP_PATH/storage/framework/views"
    
    success "Directories created"
}

# Main deployment function
main() {
    log "Starting shared hosting deployment of $APP_NAME..."
    
    check_user
    check_requirements
    create_backup
    create_directories
    deploy_app
    configure_env
    run_migrations "$@"
    configure_apache
    setup_permissions
    optimize_shared_hosting
    setup_cron
    health_check
    
    success "Shared hosting deployment completed successfully!"
    log "Application is now running at https://$DOMAIN"
    log "Please configure your .env file with the correct database and API credentials"
}

# Run main function with all arguments
main "$@"
