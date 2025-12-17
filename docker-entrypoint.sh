#!/bin/bash
set -e

# Generate .env from Railway environment variables if not exists
if [ ! -f /var/www/html/.env ]; then
  echo "Generating .env from Railway environment variables..."
  cp /var/www/html/.env.example /var/www/html/.env
  
  # Set database credentials from Railway variables
  if [ ! -z "$DATABASE_URL" ]; then
    # Parse DATABASE_URL if available
    echo "Using DATABASE_URL from Railway"
  fi
  
  # Update .env with database credentials
  sed -i "s/database.default.hostname = .*/database.default.hostname = ${MYSQL_HOSTNAME:-mysql-udqa.railway.internal}/" /var/www/html/.env
  sed -i "s/database.default.database = .*/database.default.database = ${MYSQL_DATABASE:-railway}/" /var/www/html/.env
  sed -i "s/database.default.username = .*/database.default.username = ${MYSQL_USERNAME:-root}/" /var/www/html/.env
  sed -i "s|database.default.password = .*|database.default.password = ${MYSQL_PASSWORD:-}|" /var/www/html/.env
  sed -i "s/database.default.port = .*/database.default.port = ${MYSQL_PORT:-3306}/" /var/www/html/.env
fi

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
if [ ! -z "$MYSQL_HOSTNAME" ] || [ ! -z "$DATABASE_URL" ]; then
  DB_HOST=${MYSQL_HOSTNAME:-mysql-udqa.railway.internal}
  DB_PORT=${MYSQL_PORT:-3306}
  echo "Waiting for database at $DB_HOST:$DB_PORT..."
  timeout=30
  while ! nc -z $DB_HOST $DB_PORT 2>/dev/null; do
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
