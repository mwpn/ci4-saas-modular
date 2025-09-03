# Installer Documentation

Dokumentasi untuk sistem installer template CI4 SaaS Modular.

## 📦 Jenis Installer

Template ini menyediakan 3 jenis installer untuk berbagai kebutuhan:

### 1. Basic Installer (`install.php`)

Installer dasar dengan interaksi minimal.

**Fitur:**

- ✅ Install Composer dependencies
- ✅ Cek file `env` dan `env.example`
- ✅ Generate encryption key otomatis
- ✅ Tambah `app.indexPage = ''` untuk URL tanpa index.php
- ✅ Skip chmod di Windows
- ✅ Opsi auto-migrate & auto-seed
- ✅ Deteksi composer (fallback ke `php composer.phar`)

**Usage:**

```bash
php install.php
```

### 2. Interactive Installer (`install-interactive.php`)

Installer dengan interaksi user untuk konfigurasi database.

**Fitur:**

- ✅ Semua fitur Basic Installer
- ✅ Input konfigurasi database interaktif
- ✅ Update `.env` file otomatis
- ✅ User-friendly prompts

**Usage:**

```bash
php install-interactive.php
```

**Contoh Output:**

```
🚀 CodeIgniter 4 SaaS Modular Template Installer (Interactive)
=============================================================

📦 Installing Composer dependencies...
✅ Composer ditemukan: composer
✅ Dependencies berhasil diinstall.

⚙️  Creating .env file...
✅ File .env berhasil dibuat dari env.example.

🔑 Generating encryption key...
✅ Encryption key berhasil digenerate.

🗄️  Database Configuration:
Database host [localhost]:
Database name [ci4_saas]: my_saas_app
Database username [root]: myuser
Database password []: mypassword
✅ Database configuration updated.

📁 Creating writable directories...
✅ Created: writable/cache
✅ Created: writable/logs
✅ Created: writable/session
✅ Created: writable/uploads
✅ Created: writable/debugbar

🔐 Setting permissions...
✅ Permissions set for: writable/cache
✅ Permissions set for: writable/logs
✅ Permissions set for: writable/session
✅ Permissions set for: writable/uploads
✅ Permissions set for: writable/debugbar

🤔 Auto-migrate database (y/n) [n]: y
🤔 Auto-seed database (y/n) [n]: y

🔄 Running database migrations...
✅ Database migrations berhasil dijalankan.

🌱 Running database seeders...
✅ Database seeders berhasil dijalankan.

🎉 Installation completed!
```

### 3. CI/CD Installer (`install-ci.php`)

Installer untuk CI/CD pipeline dengan opsi command line.

**Fitur:**

- ✅ Semua fitur Basic Installer
- ✅ Command line options
- ✅ Non-interactive
- ✅ Production-ready
- ✅ Optimized untuk CI/CD

**Usage:**

```bash
php install-ci.php [options]
```

**Options:**

```bash
--skip-composer    # Skip Composer installation
--skip-migrate     # Skip database migrations
--skip-seed        # Skip database seeding
--help             # Show help message
```

**Contoh Usage:**

```bash
# Full installation
php install-ci.php

# Skip Composer (already installed)
php install-ci.php --skip-composer

# Skip migrations (manual setup)
php install-ci.php --skip-migrate

# Production deployment
php install-ci.php --skip-seed

# Show help
php install-ci.php --help
```

## 🔧 Fitur Installer

### Composer Detection

Installer akan mencoba berbagai command Composer:

1. `composer` (global)
2. `php composer.phar` (local)

### Environment File Detection

Installer akan mencari file environment dalam urutan:

1. `env` (CI4 default)
2. `env.example` (template)

### Encryption Key Generation

Installer otomatis generate encryption key menggunakan:

```bash
php spark key:generate
```

### URL Configuration

Installer menambahkan `app.indexPage = ''` untuk URL tanpa index.php:

```env
app.baseURL = 'http://localhost:8080/'
app.indexPage = ''
```

### Platform Detection

Installer mendeteksi platform dan menyesuaikan:

- **Windows**: Skip chmod (tidak relevan)
- **Linux/Mac**: Set permissions 755

### Database Setup

Installer menyediakan opsi:

- **Auto-migrate**: Jalankan `php spark migrate`
- **Auto-seed**: Jalankan `php spark db:seed TenantSeeder`

## 📁 File yang Dibuat

### Writable Directories

```
writable/
├── cache/
├── logs/
├── session/
├── uploads/
└── debugbar/
```

### Environment File

```
.env (dari env atau env.example)
```

### Generated Files

- Encryption key di `.env`
- Database configuration
- Permissions set (Linux/Mac)

## 🚨 Troubleshooting

### Composer Not Found

```bash
# Install Composer globally
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Or use local composer.phar
php composer.phar install
```

### Spark Not Found

```bash
# Make sure you're in project root
ls -la spark

# If not found, check if vendor is installed
composer install
```

### Permission Issues (Linux/Mac)

```bash
# Manual permission setting
chmod -R 755 writable/
chown -R www-data:www-data writable/
```

### Database Connection Issues

```bash
# Check .env configuration
cat .env | grep database

# Test connection
php spark db:table
```

## 🔄 CI/CD Integration

### GitHub Actions

```yaml
- name: Install Dependencies
  run: php install-ci.php --skip-migrate --skip-seed

- name: Run Tests
  run: php spark test

- name: Deploy
  run: php install-ci.php --skip-composer
```

### Docker

```dockerfile
COPY . /var/www/html
WORKDIR /var/www/html
RUN php install-ci.php --skip-migrate --skip-seed
```

### Jenkins

```groovy
stage('Install') {
    steps {
        sh 'php install-ci.php --skip-migrate'
    }
}
```

## 📝 Best Practices

### Development

- Gunakan `install-interactive.php` untuk setup lokal
- Gunakan `install.php` untuk quick setup

### Production

- Gunakan `install-ci.php` untuk deployment
- Set environment variables untuk database
- Skip seeders di production

### CI/CD

- Gunakan `--skip-composer` jika dependencies sudah di-cache
- Gunakan `--skip-migrate` untuk manual migration
- Gunakan `--skip-seed` untuk production

---

**Note**: Installer ini dirancang untuk memudahkan setup template CI4 SaaS Modular dengan berbagai skenario penggunaan.
