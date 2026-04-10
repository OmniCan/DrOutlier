#!/bin/bash
# Install missing dependencies on server

echo "=== Installing DrOutlier Dependencies ==="

cd ~/public_html

echo "Current directory: $(pwd)"
echo ""

# Check if composer.json exists
if [ ! -f "composer.json" ]; then
    echo "ERROR: composer.json not found!"
    echo "You might be in the wrong directory"
    exit 1
fi

echo "composer.json found ✓"
echo ""

# Show current composer.json
echo "Current composer.json:"
cat composer.json
echo ""
echo "---"

# Remove vendor directory
echo "Removing old vendor directory..."
rm -rf vendor composer.lock

# Install dependencies
echo ""
echo "Installing dependencies..."
composer install --no-dev --optimize-autoloader --verbose

# Check if Dotenv was installed
echo ""
echo "Checking if Dotenv was installed..."
if [ -d "vendor/vlucas/phpdotenv" ]; then
    echo "✓ Dotenv installed successfully!"
else
    echo "✗ Dotenv NOT installed - check composer.json"
fi

# List installed packages
echo ""
echo "Installed packages:"
composer show

echo ""
echo "=== Installation Complete ==="
echo "Now visit: check.php to verify"
