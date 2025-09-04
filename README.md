# CodeIgniter 4 SaaS Modular

Proyek CodeIgniter 4 dengan arsitektur modular untuk aplikasi SaaS (Software as a Service) multi-tenant.

## 🚀 Fitur

- **Arsitektur Modular** - Struktur proyek yang terorganisir dengan baik
- **Multi-Tenant** - Dukungan untuk multiple tenant dengan isolasi data
- **Clean Architecture** - Menggunakan Domain-Driven Design (DDD)
- **Repository Pattern** - Pemisahan logika bisnis dan data access
- **Service Layer** - Abstraksi untuk business logic
- **Filter System** - Tenant filtering untuk isolasi data
- **Database Migration** - Sistem migrasi database yang terstruktur
- **Auto-Installer** - Script instalasi otomatis
- **Example Module** - Contoh module lengkap untuk referensi
- **Professional UI** - Interface minimalis dan responsive

## 📁 Struktur Proyek

```
ci4-modular/
├── app/                          # Core application
│   ├── Config/                   # Konfigurasi aplikasi
│   ├── Filters/                  # Custom filters
│   ├── Services/                 # Core services
│   └── Views/                    # Views aplikasi
├── modules/                      # Modular structure
│   └── Core/                     # Core module
│       ├── Application/          # Application layer
│       │   └── Services/         # Application services
│       ├── Domain/               # Domain layer
│       │   └── Entities/         # Domain entities
│       ├── Infrastructure/       # Infrastructure layer
│       │   ├── Models/           # Data models
│       │   └── Repositories/     # Data repositories
│       ├── Presentation/         # Presentation layer
│       │   ├── Controllers/      # Controllers
│       │   └── Views/            # Views
│       ├── Database/             # Database
│       │   ├── Migrations/       # Database migrations
│       │   └── Seeds/            # Database seeds
│       └── Providers/            # Service providers
└── public/                       # Web accessible files
```

## 🛠️ Instalasi

### Metode 1: Auto Installer (Recommended)

1. **Clone repository**

   ```bash
   git clone <repository-url>
   cd ci4-modular
   ```

2. **Jalankan installer**

   ```bash
   php install.php
   ```

3. **Setup database**

   - Edit file `.env` dan sesuaikan konfigurasi database
   - Buat database MySQL sesuai konfigurasi

4. **Jalankan migrasi**

   ```bash
   php spark migrate
   ```

5. **Seed data (opsional)**
   ```bash
   php spark db:seed TenantSeeder
   ```

### Metode 2: Manual Installation

1. **Clone repository**

   ```bash
   git clone <repository-url>
   cd ci4-modular
   ```

2. **Install dependencies**

   ```bash
   composer install
   ```

3. **Konfigurasi environment**

   ```bash
   cp env.example .env
   ```

4. **Setup database**

   - Edit file `.env` dan sesuaikan konfigurasi database
   - Buat database MySQL sesuai konfigurasi

5. **Jalankan migrasi**

   ```bash
   php spark migrate
   ```

6. **Seed data (opsional)**
   ```bash
   php spark db:seed TenantSeeder
   ```

### Metode 3: Interactive Installer

1. **Clone repository**

   ```bash
   git clone <repository-url>
   cd ci4-modular
   ```

2. **Jalankan interactive installer**

   ```bash
   php install-interactive.php
   ```

3. **Ikuti petunjuk installer**
   - Masukkan konfigurasi database
   - Pilih opsi auto-migrate dan auto-seed

### Metode 4: CI/CD Installer

1. **Clone repository**

   ```bash
   git clone <repository-url>
   cd ci4-modular
   ```

2. **Jalankan CI/CD installer**

   ```bash
   php install-ci.php
   ```

3. **Opsi CI/CD installer**

   ```bash
   php install-ci.php --skip-composer    # Skip Composer installation
   php install-ci.php --skip-migrate     # Skip database migrations
   php install-ci.php --skip-seed        # Skip database seeding
   php install-ci.php --help             # Show help
   ```

## ⚙️ Konfigurasi

### Database

Edit file `.env`:

```env
database.default.hostname = localhost
database.default.database = ci4_saas
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
```

### SaaS Configuration

Edit file `app/Config/SaaS.php`:

```php
public string $tenancy = 'subdomain'; // subdomain, path, header
public string $tenantHeader = 'X-TENANT-ID';
public bool $enableIsolation = true;
```

## 🏗️ Arsitektur

### Domain Layer

- **Entities**: Representasi objek bisnis
- **Value Objects**: Objek yang tidak memiliki identitas

### Application Layer

- **Services**: Business logic dan use cases
- **DTOs**: Data Transfer Objects

### Infrastructure Layer

- **Models**: Data access layer
- **Repositories**: Abstraksi data access
- **External Services**: Integrasi dengan layanan eksternal

### Presentation Layer

- **Controllers**: HTTP request handling
- **Views**: User interface
- **Middleware**: Request/response processing

## 🔧 Penggunaan

### Membuat Module Baru

```bash
# Basic module creation
php spark make:module ModuleName

# With additional features
php spark make:module ModuleName --with-migration --with-seeder --with-tests --with-api --with-views

# Force overwrite existing module
php spark make:module ModuleName --force
```

**Options:**
- `--with-migration` - Create migration files
- `--with-seeder` - Create seeder files  
- `--with-tests` - Create test files
- `--with-api` - Create API controller and routes
- `--with-views` - Create view files
- `--force` - Force overwrite existing files

**Generated Structure:**
```
modules/ModuleName/
├── Application/Services/          # Business logic
├── Domain/Entities/              # Domain entities
├── Infrastructure/
│   ├── Models/                   # Data models
│   └── Repositories/             # Data repositories
├── Presentation/
│   ├── Controllers/              # HTTP controllers
│   └── Views/                    # User interface
├── Database/
│   ├── Migrations/               # Database migrations
│   └── Seeds/                    # Database seeders
├── Providers/ServiceProvider.php # Module service provider
└── Routes.php                    # Module routes
```

### Menjalankan Migrasi

```bash
php spark migrate
```

### Menjalankan Seeder

```bash
php spark db:seed SeederName
```

## 📝 API Endpoints

### Tenants

- `GET /tenants` - Daftar semua tenant
- `POST /tenants` - Buat tenant baru
- `GET /tenants/{id}` - Detail tenant
- `PUT /tenants/{id}` - Update tenant
- `DELETE /tenants/{id}` - Hapus tenant

### Users (Example Module)

- `GET /users` - Daftar semua user
- `POST /users` - Buat user baru
- `GET /users/{id}` - Detail user
- `PUT /users/{id}` - Update user
- `DELETE /users/{id}` - Hapus user

## 🔒 Multi-Tenant

### Tenant Resolution

Sistem mendukung 3 mode tenancy:

1. **Subdomain**: `tenant1.example.com`
2. **Path**: `example.com/tenant1`
3. **Header**: `X-TENANT-ID: tenant1`

### Tenant Filter

Filter `TenantFilter` secara otomatis memvalidasi dan mengisolasi data berdasarkan tenant.

## 🧪 Testing

### Run All Tests
```bash
composer test
```

### Run Specific Test Suites
```bash
# Run feature tests
composer test:feature

# Run unit tests  
composer test:unit

# Run with coverage
composer test:coverage
```

### Run Individual Tests
```bash
vendor/bin/phpunit tests/feature/ApiTest.php
vendor/bin/phpunit tests/unit/AuthTest.php
```

## 📚 Dokumentasi

- [CodeIgniter 4 Documentation](https://codeigniter.com/user_guide/)
- [Modular Extensions](https://github.com/codeigniter4/modules)

## 🤝 Kontribusi

1. Fork repository
2. Buat feature branch
3. Commit perubahan
4. Push ke branch
5. Buat Pull Request

## 📄 Lisensi

MIT License

## 👥 Tim

- **Developer**: [Nama Developer]
- **Email**: [email@example.com]

## 📚 Template Features

### ✅ Yang Sudah Tersedia:

- **🔐 Complete Authentication System** - Login, register, password reset, profile management
- **🛡️ Authorization & Permissions** - Role-based access control (RBAC) dengan middleware
- **🏢 Multi-Tenant Architecture** - 3 mode: subdomain, path, header dengan isolasi data
- **📊 Professional Dashboard** - Admin panel dengan statistics dan activity monitoring
- **🔌 RESTful API** - Complete API dengan Swagger/OpenAPI documentation
- **🧪 Testing Framework** - Unit tests, feature tests, dan database testing
- **🐳 Docker Support** - Complete Docker setup dengan docker-compose
- **📈 Monitoring & Logging** - Error tracking, performance monitoring, audit logs
- **🔒 Security Features** - CSRF protection, input validation, rate limiting
- **📱 Responsive UI** - Bootstrap 5 dengan design system yang konsisten
- **🏗️ Clean Architecture** - DDD pattern dengan Repository & Service layers
- **⚡ Performance Optimized** - Caching, database optimization, asset minification
- **🚀 Deployment Ready** - CI/CD scripts, production configurations
- **📚 Complete Documentation** - API docs, deployment guide, architecture docs

### 🎯 Cara Menggunakan Template:

1. **Clone template ini**
2. **Jalankan `php install.php`**
3. **Setup database di `.env`**
4. **Jalankan migrasi**
5. **Mulai development dengan struktur yang sudah ada**

### 🔧 Membuat Module Baru:

Ikuti struktur Example Module:

```
modules/YourModule/
├── Application/Services/
├── Domain/Entities/
├── Infrastructure/
│   ├── Models/
│   └── Repositories/
├── Presentation/
│   ├── Controllers/
│   └── Views/
├── Database/
│   ├── Migrations/
│   └── Seeds/
├── Providers/ServiceProvider.php
└── Routes.php
```

---

**Note**: Template ini menggunakan arsitektur modular dengan clean architecture principles untuk memudahkan maintenance dan pengembangan aplikasi SaaS multi-tenant.
