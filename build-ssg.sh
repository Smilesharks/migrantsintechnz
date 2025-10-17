#!/bin/bash

# Migrants in Tech NZ - Static Site Generation Build Script
# This script builds the Statamic site for static deployment

set -e  # Exit on any error

echo "🚀 Starting SSG build for Migrants in Tech NZ..."

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${BLUE}▶ $1${NC}"
}

print_success() {
    echo -e "${GREEN}✓ $1${NC}"
}

print_warning() {
    echo -e "${YELLOW}⚠ $1${NC}"
}

print_error() {
    echo -e "${RED}✗ $1${NC}"
}

# Check if we're in the right directory
if [ ! -f "composer.json" ] || [ ! -f "package.json" ]; then
    print_error "This script must be run from the project root directory"
    exit 1
fi

# Check if required dependencies are installed
print_status "Checking dependencies..."

if ! command -v php &> /dev/null; then
    print_error "PHP is not installed or not in PATH"
    exit 1
fi

if ! command -v composer &> /dev/null; then
    print_error "Composer is not installed or not in PATH"
    exit 1
fi

if ! command -v npm &> /dev/null; then
    print_error "npm is not installed or not in PATH"
    exit 1
fi

print_success "All required dependencies found"

# Install PHP dependencies
print_status "Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction
print_success "PHP dependencies installed"

# Install Node dependencies
print_status "Installing Node.js dependencies..."
npm ci --silent
print_success "Node.js dependencies installed"

# Clear Laravel caches
print_status "Clearing Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
print_success "Laravel caches cleared"

# Clear Statamic caches
print_status "Clearing Statamic caches..."
php artisan statamic:stache:clear
php artisan statamic:static:clear
print_success "Statamic caches cleared"

# Build frontend assets
print_status "Building frontend assets..."
npm run production
print_success "Frontend assets built"

# Warm Statamic cache
print_status "Warming Statamic cache..."
php artisan statamic:stache:warm
print_success "Statamic cache warmed"

# Update search indexes
print_status "Updating search indexes..."
php artisan statamic:search:update --all
print_success "Search indexes updated"

# Generate static site
print_status "Generating static site..."
php please ssg:generate

# Check if static generation was successful
if [ ! -d "storage/app/static" ]; then
    print_error "Static site generation failed - output directory not found"
    exit 1
fi

print_success "Static site generated successfully"

# Display build information
echo ""
echo "🎉 Build completed successfully!"
echo ""
echo "📊 Build Summary:"
echo "├── Static files location: storage/app/static/"
echo "├── Frontend assets: public/build/"
echo "└── Cache status: Warmed and optimized"
echo ""
echo "📤 Next steps for deployment:"
echo "1. Upload contents of storage/app/static/ to your hosting provider"
echo "2. Configure your web server to serve static files"
echo "3. Set up redirects if needed"
echo ""
print_success "SSG build script completed!"