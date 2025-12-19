FROM php:8.2-apache

# Install required PHP extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Ensure index.php is used as DirectoryIndex
RUN echo "DirectoryIndex index.php" > /etc/apache2/conf-available/php-index.conf \
    && a2enconf php-index

# Copy application source
COPY src/ /var/www/html/

# Set working directory
WORKDIR /var/www/html/

# Proper file permissions
RUN chown -R www-data:www-data /var/www/html

# Environment variables (sane defaults but ALWAYS overridden outside)
ENV DB_HOST=localhost \
    DB_NAME=qlsv \
    DB_USER=root \
    DB_PASS=""

EXPOSE 80

CMD ["apache2-foreground"]

