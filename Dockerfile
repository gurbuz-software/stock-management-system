# PHP Apache imajını kullan
FROM php:8.2-apache

# Sistem paketlerini güncelle ve gerekli paketleri kur
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mysqli zip

# Apache modüllerini etkinleştir
RUN a2enmod rewrite

# Çalışma dizinini ayarla
WORKDIR /var/www/html

# Uygulama dosyalarını kopyala
COPY . .

# Dosya izinlerini ayarla
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Veritabanı başlangıç scriptini oluştur
RUN echo '#!/bin/bash\n\
# Veritabanı bağlantısını kontrol et ve tabloları oluştur\n\
echo "Veritabanı bağlantısı kontrol ediliyor..."\n\
max_attempts=30\n\
attempt=1\n\
while [ $attempt -le $max_attempts ]; do\n\
    php -r "\n\
    \\$host = getenv(\\\"DB_HOST\\\") ?: \\\"localhost\\\";\n\
    \\$dbname = getenv(\\\"DB_NAME\\\") ?: \\\"stock_management\\\";\n\
    \\$username = getenv(\\\"DB_USER\\\") ?: \\\"root\\\";\n\
    \\$password = getenv(\\\"DB_PASSWORD\\\") ?: \\\"\\\";\n\
    try {\n\
        \\$pdo = new PDO(\\\"mysql:host=\\\$host;dbname=\\\$dbname;charset=utf8\\\", \\$username, \\$password);\n\
        \\$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);\n\
        echo \\\"Veritabanı bağlantısı başarılı! Tablolar oluşturuluyor...\\n\\\";\n\
        \n\
        # SQL dosyasını oku ve çalıştır\n\
        \\$sql = file_get_contents(\\\"/var/www/html/config/init.sql\\\");\n\
        \\$pdo->exec(\\$sql);\n\
        echo \\\"Tablolar başarıyla oluşturuldu!\\n\\\";\n\
        exit(0);\n\
    } catch (PDOException \\$e) {\n\
        echo \\\"Bağlantı denemesi \\\" . \\$attempt . \\\": \\\" . \\$e->getMessage() . \\\"\\n\\\";\n\
        exit(1);\n\
    }\n\
    \" && break\n\
    sleep 2\n\
    attempt=\\$((attempt + 1))\n\
done\n\
if [ $attempt -gt $max_attempts ]; then\n\
    echo \"Veritabanı bağlantısı başarısız!\"\n\
    exit 1\n\
fi\n\
\n\
# Apache'yi başlat\n\
apache2-foreground' > /usr/local/bin/start.sh

RUN chmod +x /usr/local/bin/start.sh

# Apache portunu aç
EXPOSE 80

# Başlangıç scriptini çalıştır
CMD ["/usr/local/bin/start.sh"]