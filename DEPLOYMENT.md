# Deployment Guide

Panduan lengkap untuk deployment CI4 SaaS Modular Template.

## 🐳 Docker Deployment (Recommended)

### Prerequisites

- Docker & Docker Compose
- Git

### Quick Start

```bash
# Clone repository
git clone https://github.com/mwpn/ci4-saas-modular.git
cd ci4-saas-modular

# Start services
docker-compose up -d

# Run migrations
docker-compose exec app php spark migrate

# Seed initial data
docker-compose exec app php spark db:seed TenantSeeder
```

### Services

- **App**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081
- **MailHog**: http://localhost:8025

## 🚀 Production Deployment

### 1. Server Requirements

- PHP 8.2+
- MySQL 8.0+
- Apache/Nginx
- Composer
- SSL Certificate

### 2. Environment Setup

```bash
# Clone repository
git clone https://github.com/mwpn/ci4-saas-modular.git
cd ci4-saas-modular

# Install dependencies
composer install --no-dev --optimize-autoloader

# Setup environment
cp env.example .env
# Edit .env with production values

# Generate encryption key
php spark key:generate

# Set permissions
chmod -R 755 .
chmod -R 777 writable/
```

### 3. Database Setup

```bash
# Create database
mysql -u root -p
CREATE DATABASE ci4_saas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Run migrations
php spark migrate

# Seed data
php spark db:seed TenantSeeder
```

### 4. Web Server Configuration

#### Apache (.htaccess)

```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php/$1 [QSA,L]
```

#### Nginx

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
    include fastcgi_params;
}
```

### 5. SSL Configuration

```bash
# Using Let's Encrypt
certbot --apache -d yourdomain.com -d www.yourdomain.com
```

## 🔧 CI/CD Pipeline

### GitHub Actions

```yaml
name: Deploy
on:
  push:
    branches: [main]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Deploy to server
        run: |
          # Your deployment script
```

### Environment Variables

```env
# Production
CI_ENVIRONMENT = production
app.baseURL = 'https://yourdomain.com/'
app.indexPage = ''

# Database
database.default.hostname = localhost
database.default.database = ci4_saas
database.default.username = your_username
database.default.password = your_password

# Security
encryption.key = your_encryption_key
```

## 📊 Monitoring & Logging

### Application Logs

```bash
# View logs
tail -f writable/logs/log-$(date +%Y-%m-%d).php

# Log rotation
logrotate /etc/logrotate.d/ci4-saas
```

### Performance Monitoring

- Enable OPcache
- Use Redis for caching
- Monitor database queries
- Set up error tracking (Sentry)

## 🔒 Security Checklist

- [ ] SSL certificate installed
- [ ] Environment variables secured
- [ ] Database credentials changed
- [ ] File permissions set correctly
- [ ] Firewall configured
- [ ] Regular backups scheduled
- [ ] Security headers enabled
- [ ] Rate limiting configured

## 🚨 Troubleshooting

### Common Issues

#### 1. Permission Errors

```bash
chmod -R 755 .
chmod -R 777 writable/
```

#### 2. Database Connection

```bash
# Test connection
php spark db:table users
```

#### 3. Module Loading

```bash
# Clear cache
php spark cache:clear
composer dump-autoload
```

#### 4. Tenant Resolution

```bash
# Check tenant service
php spark routes
```

## 📈 Scaling

### Horizontal Scaling

- Load balancer (Nginx/HAProxy)
- Multiple app servers
- Database replication
- Redis cluster

### Vertical Scaling

- Increase server resources
- Optimize database queries
- Enable caching
- Use CDN for static assets

## 🔄 Backup Strategy

### Database Backup

```bash
# Daily backup
mysqldump -u username -p ci4_saas > backup_$(date +%Y%m%d).sql

# Automated backup script
0 2 * * * /path/to/backup-script.sh
```

### File Backup

```bash
# Backup application files
tar -czf app_backup_$(date +%Y%m%d).tar.gz /var/www/html/
```

## 📞 Support

- **Documentation**: [GitHub Wiki](https://github.com/mwpn/ci4-saas-modular/wiki)
- **Issues**: [GitHub Issues](https://github.com/mwpn/ci4-saas-modular/issues)
- **Email**: support@example.com
