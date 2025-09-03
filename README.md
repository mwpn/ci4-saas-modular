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
php spark make:module ModuleName
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

```bash
php spark test
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

- **Core Module** - Tenant management system
- **Example Module** - User management sebagai referensi
- **Multiple Installers** - 3 jenis installer: basic, interactive, dan CI/CD
- **Environment Template** - File `env.example` untuk konfigurasi
- **Database Migrations** - Struktur database yang terorganisir
- **Professional UI** - Interface minimalis dengan Bootstrap 5
- **Multi-Tenant Support** - 3 mode: subdomain, path, header
- **Clean Architecture** - DDD pattern dengan Repository & Service layers
- **Tenant Isolation** - Middleware untuk isolasi data tenant
- **Auto Key Generation** - Generate encryption key otomatis

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
