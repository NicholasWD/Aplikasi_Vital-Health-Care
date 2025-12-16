#!/bin/bash
set -e

# Configure Apache to listen on Railway's PORT (default 8080)
export APACHE_PORT=${PORT:-80}

echo "ServerName localhost" >> /etc/apache2/apache2.conf
echo "Listen ${APACHE_PORT}" > /etc/apache2/ports.conf
sed -i "s/Listen 80/Listen ${APACHE_PORT}/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${APACHE_PORT}>/" /etc/apache2/sites-available/000-default.conf

echo "Starting Apache on port ${APACHE_PORT}..."
exec apache2-foreground
