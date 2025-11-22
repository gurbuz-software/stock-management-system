# Stok Takip Sistemi

Modern, şık ve kullanıcı dostu bir stok takip uygulaması. PHP, MySQL ve CSS kullanılarak geliştirilmiştir.

## Özellikler

### 🔐 Kullanıcı Yönetimi
- Kullanıcı kayıt ve giriş sistemi
- Şifre hashleme (güvenli)
- Oturum yönetimi

### 👑 Admin Panel
- Kullanıcı yönetimi
- Yetki sistemi (Admin/Kullanıcı)
- Sistem istatistikleri
- Kullanıcı düzenleme ve silme

### 📦 Ürün Yönetimi
- Ürün ekleme, düzenleme, silme
- Kategori bazlı filtreleme
- Stok takibi
- Düşük stok uyarıları
- Barkod desteği
- Ürün resimleri

### 🎨 Modern Tasarım
- Responsive tasarım
- Modern CSS (Grid, Flexbox)
- Animasyonlar ve geçişler
- Toast mesajları
- Modal pencereler

## Kurulum

### Docker ile Kurulum (Önerilen)

#### 1. Gereksinimler
- Docker
- Docker Compose

#### 2. Uygulamayı Başlatma
```bash
# Tüm servisleri başlat
docker-compose up -d

# Sadece uygulamayı başlat (veritabanı zaten çalışıyorsa)
docker-compose up app -d
```

#### 3. Erişim
- **Uygulama:** http://localhost:8080
- **phpMyAdmin:** http://localhost:8081 (isteğe bağlı)
- **MySQL:** localhost:3306

### Dokploy ile Deployment

#### 1. Repository'yi Dokploy'a Bağlama
1. Dokploy panelinde "New Application" seçeneğine tıklayın
2. Git repository URL'nizi girin
3. Branch seçin (genellikle main/master)

#### 2. Environment Variables (Ortam Değişkenleri)
Dokploy'da aşağıdaki environment değişkenlerini ayarlayın:

```env
DB_HOST=your_mysql_host
DB_NAME=your_database_name
DB_USER=your_database_user
DB_PASSWORD=your_database_password
DB_PORT=3306
```

#### 3. Build Settings
- **Build Method:** Dockerfile
- **Dockerfile Path:** Dockerfile (default)
- **Build Context:** . (default)

#### 4. Veritabanı Tabloları
Uygulama ilk başlatıldığında otomatik olarak:
- Veritabanı bağlantısını kontrol eder
- Tabloları oluşturur
- Varsayılan admin kullanıcısını ekler
- Temel kategorileri oluşturur

#### 5. Health Check
Uygulama otomatik health check ile izlenir:
- **App:** HTTP 200 status kontrolü
- **Database:** MySQL ping kontrolü

### Manuel Kurulum

#### 1. Gereksinimler
- PHP 7.4+
- MySQL 5.7+
- Web sunucusu (Apache/Nginx)

#### 2. Veritabanı Kurulumu
```sql
-- MySQL komut satırında çalıştırın
mysql -u root -p < config/init.sql
```

#### 3. Yapılandırma
`config/database.php` dosyasını düzenleyerek veritabanı bağlantı bilgilerinizi girin:

```php
$host = 'localhost';
$dbname = 'stock_management';
$username = 'root';
$password = 'sifreniz';
```

#### 4. Web Sunucusu
Proje dosyalarını web sunucunuzun kök dizinine taşıyın ve tarayıcıdan erişin.

## Demo Hesaplar

### Admin Hesabı
- **Kullanıcı Adı:** admin
- **Şifre:** password

### Normal Kullanıcı
Kayıt sayfasından yeni hesap oluşturabilirsiniz.

## Kullanım

### Giriş Yapma
1. Tarayıcıdan proje URL'sine gidin
2. Giriş sayfasında admin hesabıyla giriş yapın
3. Dashboard'a yönlendirileceksiniz

### Ürün Ekleme
1. "Ürünler" sayfasına gidin
2. "Yeni Ürün Ekle" formunu doldurun
3. "Ürün Ekle" butonuna tıklayın

### Admin İşlemleri
1. "Admin Panel" sayfasına gidin
2. Kullanıcıları görüntüleyin ve yönetin
3. Yetkileri düzenleyin

## Dosya Yapısı

```
├── config/
│   ├── database.php      # Veritabanı bağlantısı
│   └── init.sql          # Veritabanı şeması
├── css/
│   └── style.css         # Modern CSS stilleri
├── includes/
│   ├── auth.php          # Kimlik doğrulama
│   ├── products.php      # Ürün yönetimi
│   └── users.php         # Kullanıcı yönetimi
├── js/
│   └── script.js         # JavaScript fonksiyonları
├── pages/
│   ├── login.php         # Giriş sayfası
│   ├── register.php      # Kayıt sayfası
│   ├── dashboard.php     # Ana panel
│   ├── products.php      # Ürün yönetimi
│   ├── admin.php         # Admin paneli
│   └── logout.php        # Çıkış işlemi
├── index.php             # Ana sayfa
└── README.md             # Bu dosya
```

## Güvenlik Özellikleri

- SQL Injection koruması (PDO prepared statements)
- XSS koruması (htmlspecialchars)
- Şifre hashleme (password_hash)
- Oturum yönetimi
- CSRF koruması (form tokenları)
- Input validasyonu

## Teknolojiler

- **Backend:** PHP 7.4+
- **Database:** MySQL 5.7+
- **Frontend:** HTML5, CSS3, JavaScript
- **Styling:** Modern CSS (CSS Grid, Flexbox)
- **Security:** PDO, password_hash, session management

## Geliştirici

Bu proje modern web geliştirme prensipleri kullanılarak geliştirilmiştir. Kodlar temiz, okunabilir ve bakımı kolay şekilde yazılmıştır.

## Docker Komutları

```bash
# Tüm servisleri başlat
docker-compose up -d

# Servisleri durdur
docker-compose down

# Servisleri durdur ve volume'leri sil
docker-compose down -v

# Logları görüntüle
docker-compose logs -f

# Sadece uygulamayı yeniden başlat
docker-compose restart app

# Container durumunu kontrol et
docker-compose ps

# Uygulama container'ına bağlan
docker-compose exec app bash

# Veritabanı container'ına bağlan
docker-compose exec db mysql -u root -p
```

## Production için Docker & Dokploy

### Dokploy Production Ayarları
1. **Auto Deploy:** Aktif edin (yeni commit'lerde otomatik deploy)
2. **Health Checks:** Aktif edin
3. **Restart Policy:** `unless-stopped`
4. **Resource Limits:** Uygun CPU/Memory limitleri ayarlayın

### Environment Variables (Production)
```env
DB_HOST=production_db_host
DB_NAME=production_database
DB_USER=production_user
DB_PASSWORD=strong_production_password
DB_PORT=3306
```

### Güvenlik Önlemleri
- Veritabanı şifreleri güçlü ve unique olmalı
- SSL/TLS bağlantıları kullanın
- Regular backup alın
- Monitoring ve logging aktif edin

## Lisans

Bu proje MIT lisansı altında lisanslanmıştır.