#!/bin/bash
set -e

# Use PORT from Railway or default to 80
export APACHE_PORT=${PORT:-80}

# Ensure only one MPM is loaded (disable all except mpm_prefork)
echo "Cleaning up Apache MPM modules..."
a2dismod mpm_worker mpm_event 2>/dev/null || true
a2enmod mpm_prefork 2>/dev/null || true

# Update Apache ports configuration only if not already configured
if ! grep -q "Listen ${APACHE_PORT}" /etc/apache2/ports.conf 2>/dev/null; then
  echo "Configuring Apache to listen on port ${APACHE_PORT}..."
  # Append instead of overwriting to preserve existing MPM configuration
  echo "Listen ${APACHE_PORT}" >> /etc/apache2/ports.conf
  sed -i "s/<VirtualHost \*:[0-9]\+>/<VirtualHost *:${APACHE_PORT}>/" /etc/apache2/sites-available/000-default.conf
fi

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
