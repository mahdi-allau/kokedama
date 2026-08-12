# Kokedama — Deploy su Render.com (o qualsiasi hosting Docker)
FROM php:8.2-apache

# 1. Abilita mod_rewrite per gli URL SEF (richiesto dall'app)
RUN a2enmod rewrite

# 2. Assicurati che le estensioni SQLite3 e PDO_SQLite siano attive
#    (in php:8.2-apache sono già abilitate di default, ma confermiamo)
RUN docker-php-ext-install pdo_sqlite 2>/dev/null || true

# 3. Copia l'applicazione nella document root
COPY app/ /var/www/html/

# 4. Permessi: Apache deve poter leggere tutto e scrivere il database SQLite
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod 664 /var/www/html/database.sqlite \
    && chmod 775 /var/www/html/assets/uploads

# 5. Configurazione Apache: abilita .htaccess e imposta DocumentRoot
RUN { \
    echo '<Directory /var/www/html>'; \
    echo '  Options Indexes FollowSymLinks'; \
    echo '  AllowOverride All'; \
    echo '  Require all granted'; \
    echo '</Directory>'; \
} > /etc/apache2/conf-available/kokedama.conf \
    && a2enconf kokedama

# 6. Esponi la porta 80
EXPOSE 80

# 7. Avvia Apache in foreground
CMD ["apache2-foreground"]
