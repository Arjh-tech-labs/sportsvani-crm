#!/bin/bash
# This is a guide script - do not run directly

# 1. Connect to your server via SSH
# ssh username@your_server_ip

# 2. Navigate to the correct directory
cd /var/www/html

# 3. Set proper permissions
sudo chown -R www-data:www-data /var/www/html/sportsvani
sudo find /var/www/html/sportsvani -type f -exec chmod 644 {} \;
sudo find /var/www/html/sportsvani -type d -exec chmod 755 {} \;
sudo chmod -R 775 /var/www/html/sportsvani/storage
sudo chmod -R 775 /var/www/html/sportsvani/bootstrap/cache

# 4. Install Composer dependencies
cd /var/www/html/sportsvani
composer install --no-dev --optimize-autoloader

# 5. Create .env file if it doesn't exist
cp .env.example .env

# 6. Generate application key
php artisan key:generate

# 7. Configure .env file
# Edit the .env file with your database credentials and other settings
# nano .env

# 8. Run migrations and seed the database
php artisan migrate --seed

# 9. Create symbolic link for storage
php artisan storage:link

# 10. Configure Apache
sudo cp sportsvani.conf /etc/apache2/sites-available/
sudo a2ensite sportsvani.conf
sudo a2enmod rewrite
sudo systemctl restart apache2

# 11. Set up SSL (optional but recommended)
# sudo certbot --apache -d sportsvani.in -d www.sportsvani.in

# 12. Optimize Laravel for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

