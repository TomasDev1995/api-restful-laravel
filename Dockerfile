# Usa la imagen base de PHP con Apache
FROM php:7.4-apache

# Instalar dependencias necesarias y oniguruma
RUN apt-get update && apt-get install -y \
    libonig-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# Habilita los módulos de Apache y PHP necesarios
RUN a2enmod rewrite ssl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /var/www/html/

# Crear el proyecto Laravel 8.x en la raíz del directorio de trabajo
WORKDIR /var/www/html

# Cambia los permisos del directorio de trabajo
RUN chmod -R 777 /var/www/html

# Establece los permisos específicos para storage y cache
RUN chmod -R 777 /var/www/html/storage 
    
# Copia el archivo de configuración de Apache
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY docker/apache/default-ssl.conf /etc/apache2/sites-available/default-ssl.conf

# Habilita la configuración SSL
RUN a2ensite default-ssl.conf

# Expon los puertos 80 y 443
EXPOSE 80 443

# Reinicia Apache
CMD ["apache2-foreground"]
