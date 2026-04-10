#!/bin/bash
# Hostinger Git Deployment Hook

echo "=== DrOutlier Deployment Started ==="

# Install Frontend (Root level PHP) dependencies
echo "Installing Frontend dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Install Admin (Laravel) dependencies
echo "Installing Admin Panel dependencies..."
cd admin/application
composer install --no-dev --optimize-autoloader --no-interaction
php artisan config:cache
php artisan route:cache
php artisan view:cache
cd ../..

# Set proper permissions
echo "Setting permissions..."
chmod -R 755 admin/application/storage
chmod -R 755 admin/application/bootstrap/cache
chmod -R 755 storage/cache

echo "=== Deployment Complete ==="
