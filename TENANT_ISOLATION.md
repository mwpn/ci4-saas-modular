# Tenant Isolation Middleware

Dokumentasi untuk sistem tenant isolation yang telah diimplementasi dalam template CI4 SaaS Modular.

## 🏗️ Arsitektur

### 1. TenantIsolationMiddleware

Middleware utama yang menangani isolasi tenant pada level request.

**Lokasi**: `app/Middleware/TenantIsolationMiddleware.php`

**Fungsi**:

- Resolve tenant dari request (subdomain, path, atau header)
- Validasi tenant di database
- Set tenant context untuk seluruh aplikasi
- Handle database connection per tenant (opsional)

### 2. TenantService

Service untuk resolusi dan validasi tenant.

**Lokasi**: `app/Services/TenantService.php`

**Fungsi**:

- Resolve tenant ID dari berbagai mode (subdomain, path, header)
- Validasi tenant di database
- Get tenant object dan settings
- Cache management

### 3. TenantContext

Context manager untuk menyimpan data tenant.

**Lokasi**: `app/Services/TenantContext.php`

**Fungsi**:

- Set/get tenant context
- Session management
- Settings management
- Context clearing

### 4. BaseModel

Model base dengan tenant isolation otomatis.

**Lokasi**: `modules/Core/Infrastructure/Models/BaseModel.php`

**Fungsi**:

- Auto-apply tenant scope pada queries
- Tenant ID injection pada insert/update
- Query builder dengan tenant awareness

## 🚀 Cara Penggunaan

### 1. Konfigurasi Routes

```php
// Gunakan filter tenant-isolation pada routes yang memerlukan tenant
$routes->group('users', ['filter' => 'tenant-isolation'], function ($routes) {
    $routes->get('/', 'UserController::index');
    $routes->post('/', 'UserController::store');
});
```

### 2. Model dengan Tenant Isolation

```php
// Model akan otomatis apply tenant scope
class UserModel extends BaseModel
{
    protected $table = 'users';
    protected $tenantAware = true; // Default: true
    protected $tenantField = 'tenant_id'; // Default: tenant_id
}

// Penggunaan
$userModel = new UserModel();
$users = $userModel->findAll(); // Otomatis filter by tenant_id
```

### 3. Manual Tenant Context

```php
// Set tenant context
$tenant = $tenantModel->find(1);
TenantContext::setTenant($tenant);

// Get tenant info
$tenantId = TenantContext::getTenantId();
$tenantName = TenantContext::getTenantName();
$settings = TenantContext::getSettings();
```

### 4. Query Builder Options

```php
$userModel = new UserModel();

// Normal query (dengan tenant scope)
$users = $userModel->findAll();

// Query tanpa tenant scope (untuk admin)
$allUsers = $userModel->withoutTenantScope()->findAll();

// Force apply tenant scope
$tenantUsers = $userModel->withTenantScope()->findAll();
```

## ⚙️ Konfigurasi

### Environment Variables

```env
# Mode tenancy: subdomain, path, header
saas.tenancy = subdomain

# Header name untuk mode header
saas.tenantHeader = X-TENANT-ID

# Enable tenant isolation
saas.enableIsolation = true

# Database per tenant (opsional)
saas.databasePerTenant = false
```

### Filter Configuration

```php
// app/Config/Filters.php
public array $aliases = [
    'tenant-isolation' => \App\Middleware\TenantIsolationMiddleware::class,
];
```

## 🔧 Mode Tenancy

### 1. Subdomain Mode

```
https://tenant1.example.com/users
https://tenant2.example.com/users
```

### 2. Path Mode

```
https://example.com/tenant1/users
https://example.com/tenant2/users
```

### 3. Header Mode

```
X-TENANT-ID: tenant1
```

## 📊 Database Schema

### Tabel dengan Tenant Isolation

Semua tabel yang menggunakan `BaseModel` harus memiliki kolom `tenant_id`:

```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tenant_id INT NOT NULL,
    name VARCHAR(100),
    email VARCHAR(255),
    created_at DATETIME,
    updated_at DATETIME,
    INDEX idx_tenant_id (tenant_id)
);
```

### Tabel Global

Untuk tabel yang tidak memerlukan tenant isolation:

```php
class GlobalModel extends BaseModel
{
    protected $tenantAware = false;
}
```

## 🛡️ Security Features

### 1. Automatic Tenant Scope

- Semua query otomatis di-scope ke tenant yang aktif
- Mencegah akses data tenant lain

### 2. Tenant Validation

- Validasi tenant di database
- Cek status aktif tenant
- Redirect jika tenant tidak valid

### 3. Context Management

- Tenant context tersimpan di session
- Automatic cleanup setelah request

## 🚨 Troubleshooting

### 1. Tenant tidak ter-resolve

```php
// Cek konfigurasi tenancy mode
echo env('saas.tenancy'); // subdomain, path, atau header

// Cek tenant service
$tenantService = service('tenant');
echo $tenantService->id();
```

### 2. Data tidak ter-filter

```php
// Pastikan model extends BaseModel
class UserModel extends BaseModel
{
    protected $tenantAware = true;
}

// Cek tenant context
echo TenantContext::getTenantId();
```

### 3. Middleware tidak jalan

```php
// Pastikan filter terdaftar
// app/Config/Filters.php
'tenant-isolation' => \App\Middleware\TenantIsolationMiddleware::class,

// Pastikan routes menggunakan filter
$routes->group('users', ['filter' => 'tenant-isolation'], function($routes) {
    // routes
});
```

## 📝 Best Practices

### 1. Model Design

- Selalu extend `BaseModel` untuk model yang memerlukan tenant isolation
- Set `$tenantAware = false` untuk tabel global
- Gunakan `withoutTenantScope()` untuk query admin

### 2. Controller

- Gunakan `TenantContext` untuk akses tenant info
- Validasi tenant di controller jika diperlukan

### 3. Views

- Gunakan `TenantContext::getTenantName()` untuk display
- Access tenant settings via `TenantContext::getSetting()`

### 4. Testing

- Mock tenant context untuk testing
- Test dengan berbagai tenant ID
- Test tenant isolation boundaries

## 🔄 Migration Guide

### Dari Filter Lama ke Middleware Baru

```php
// Lama
$routes->group('users', ['filter' => 'tenant'], function($routes) {
    // routes
});

// Baru
$routes->group('users', ['filter' => 'tenant-isolation'], function($routes) {
    // routes
});
```

### Update Model

```php
// Lama
class UserModel extends Model
{
    // manual tenant handling
}

// Baru
class UserModel extends BaseModel
{
    // automatic tenant handling
}
```

---

**Note**: Middleware tenant isolation ini memberikan isolasi data yang kuat dan otomatis untuk aplikasi SaaS multi-tenant. Pastikan untuk menguji semua fitur sebelum production deployment.
