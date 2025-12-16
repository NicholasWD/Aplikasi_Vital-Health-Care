#!/bin/bash
set -e

# Use PORT from Railway or default to 80
export APACHE_PORT=${PORT:-80}

# Ensure only one MPM is enabled
echo "Configuring Apache MPM modules..."
a2dismod mpm_worker mpm_event 2>/dev/null || true
a2enmod mpm_prefork 2>/dev/null || true

# Remove existing Listen directives and add the correct one
echo "Setting Apache to listen on port ${APACHE_PORT}..."
sed -i '/^Listen /d' /etc/apache2/ports.conf
echo "Listen ${APACHE_PORT}" >> /etc/apache2/ports.conf

# Update VirtualHost to use correct port
sed -i "s/<VirtualHost \*:[0-9]\+>/<VirtualHost *:${APACHE_PORT}>/" /etc/apache2/sites-available/000-default.conf

echo "Starting Apache on port ${APACHE_PORT}..."

# Optional: Wait for database if DATABASE_HOST is set
if [ ! -z "$DATABASE_HOST" ] && [ ! -z "$DATABASE_PORT" ]; then
  echo "Waiting for database at $DATABASE_HOST:$DATABASE_PORT..."
  timeout=30
  while ! nc -z $DATABASE_HOST $DATABASE_PORT 2>/dev/null; do
    timeout=$((timeout - 1))
    if [ $timeout -le 0 ]; then
      echo "Warning: Database not reachable, continuing anyway..."
      break
    fi
    sleep 1
  done
  if [ $timeout -gt 0 ]; then
    echo "Database is ready!"
  fi
fi

# Start Apache in foreground
exec apache2-foreground
