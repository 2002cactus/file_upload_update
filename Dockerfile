FROM php:7.3-apache

# Set working directory
WORKDIR /var/www/html/

# Copy source code
COPY ./src/ .

# OPTIONAL: Copy Apache config if needed
# COPY apache2.conf /etc/apache2/apache2.conf

# Fix permission so Apache (www-data) can serve files
RUN chown -R www-data:www-data /var/www/html
RUN find . -type f -exec chmod 644 {} \;
RUN find . -type d -exec chmod 755 {} \;

# Make upload directory writable (if needed by PHP)
RUN chmod 775 /var/www/html/upload/
RUN chmod +t -R /var/www/html/upload/

# DEBUG: Add a flag (if you're doing a CTF-style setup)
RUN echo "FLAG{second_secret}" > /second_secret.txt
