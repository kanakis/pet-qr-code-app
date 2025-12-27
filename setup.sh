#!/bin/bash
set -e
set -u

# Pet QR Code App - Setup and Test Script

echo "========================================"
echo "Pet QR Code App - Setup Script"
echo "========================================"
echo ""

# Check PHP version
echo "1. Checking PHP version..."
php -v | head -n 1
PHP_VERSION=$(php -r "echo PHP_VERSION;")
echo "PHP Version: $PHP_VERSION"
echo ""

# Check if composer is installed
echo "2. Checking for Composer..."
if command -v composer &> /dev/null; then
    echo "Composer is installed"
    composer --version
else
    echo "WARNING: Composer is not installed. Please install it from https://getcomposer.org/"
fi
echo ""

# Install dependencies
echo "3. Installing dependencies..."
if command -v composer &> /dev/null; then
    composer install --no-dev
else
    echo "Skipping dependency installation (Composer not available)"
fi
echo ""

# Check directory permissions
echo "4. Checking directory permissions..."
if [ -d "qr_codes" ]; then
    echo "qr_codes directory exists"
    if [ -w "qr_codes" ]; then
        echo "✓ qr_codes directory is writable"
    else
        echo "✗ qr_codes directory is NOT writable"
        echo "  Run: chmod 755 qr_codes"
    fi
else
    echo "✗ qr_codes directory does not exist"
    echo "  Creating qr_codes directory..."
    mkdir -p qr_codes
    chmod 755 qr_codes
fi
echo ""

# Check PHP extensions
echo "5. Checking required PHP extensions..."
REQUIRED_EXTENSIONS=("pdo" "pdo_mysql" "gd" "mbstring")
for ext in "${REQUIRED_EXTENSIONS[@]}"; do
    if php -m | grep -q "^$ext$"; then
        echo "✓ $ext extension is loaded"
    else
        echo "✗ $ext extension is NOT loaded"
    fi
done
echo ""

# Test database connection
echo "6. Testing database connection..."
php -r "
require_once 'includes/db.php';
\$pdo = getDbConnection();
if (\$pdo) {
    echo '✓ Database connection successful\n';
} else {
    echo '✗ Database connection failed\n';
    echo '  Please check your database configuration in includes/db.php\n';
}
" 2>&1
echo ""

echo "========================================"
echo "Setup Complete!"
echo "========================================"
echo ""
echo "Next steps:"
echo "1. Make sure your database is set up (run database.sql)"
echo "2. Update database credentials in includes/db.php"
echo "3. Configure your web server to point to this directory"
echo "4. Access the application at http://localhost/pet-qr-code-app/"
echo ""
