#!/bin/sh
set -e

echo "========================================="
echo "Starting Frontend Services (Eronas)"
echo "========================================="

# Check if Nginx config is valid
echo "[*] Validating Nginx configuration..."
nginx -t

# Check if PHP-FPM config is valid
echo "[*] Validating PHP-FPM configuration..."
php-fpm -t

echo ""
echo "✅ All configurations valid!"
echo ""
echo "Starting services with supervisord..."
echo ""
echo "Services:"
echo "  • Nginx    → http://0.0.0.0:8000"
echo "  • PHP-FPM  → 127.0.0.1:9000"
echo ""
echo "========================================="

exec "$@"
