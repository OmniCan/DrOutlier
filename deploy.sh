#!/bin/bash
# Hostinger Git Deployment Hook

echo "=== DrOutlier Deployment Started ==="

# Install Admin (Laravel) dependencies
echo "Installing Admin Panel dependencies..."
cd admin/application
composer install --no-dev --optimize-autoloader --no-interaction
php artisan config:cache
php artisan route:cache
php artisan view:cache
cd ../..

# Install PHP Frontend dependencies  
echo "Installing Frontend dependencies..."
cd frontend
composer install --no-dev --optimize-autoloader --no-interaction
cd ..

# Set proper permissions
echo "Setting permissions..."
chmod -R 755 admin/application/storage
chmod -R 755 admin/application/bootstrap/cache
chmod -R 755 frontend/storage

echo "=== Deployment Complete ==="
