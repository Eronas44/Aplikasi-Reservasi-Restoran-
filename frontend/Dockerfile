# Use official PHP-FPM Alpine image
FROM php:8.2-fpm-alpine

WORKDIR /app

# Install Nginx, supervisor, and dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    bash

# Create necessary directories
RUN mkdir -p /var/run/php /var/run/nginx /var/log/nginx /app/logs

# Copy PHP-FPM config
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

# Copy nginx config
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/default.conf /etc/nginx/conf.d/default.conf

# Copy supervisor config
COPY docker/supervisord.conf /etc/supervisord.conf

# Copy application code
COPY . .

# Set proper permissions
RUN chmod -R 755 /app && \
    chmod -R 755 /var/run/nginx && \
    chmod -R 755 /var/log/nginx

# Health check
HEALTHCHECK --interval=15s --timeout=5s --retries=3 \
    CMD curl -f http://localhost:8000/ || exit 1

# Expose port
EXPOSE 8000

# Start script
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Tambahkan 2 baris ini di Dockerfile frontend paling bawah:
RUN apk add --no-cache dos2unix
RUN dos2unix /entrypoint.sh && chmod +x /entrypoint.sh

ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
