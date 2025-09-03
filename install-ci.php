<?php

/**
 * CodeIgniter 4 SaaS Modular Template Installer (CI/CD)
 * 
 * Script untuk setup otomatis template CI4 SaaS Modular untuk CI/CD
 * Usage: php install-ci.php [--skip-composer] [--skip-migrate] [--skip-seed]
 */

// Parse command line arguments
$options = getopt('', [
    'skip-composer',
    'skip-migrate',
    'skip-seed',
    'help'
]);

if (isset($options['help'])) {
    echo "CodeIgniter 4 SaaS Modular Template Installer (CI/CD)\n";
    echo "Usage: php install-ci.php [options]\n\n";
    echo "Options:\n";
    echo "  --skip-composer    Skip Composer installation\n";
    echo "  --skip-migrate     Skip database migrations\n";
    echo "  --skip-seed        Skip database seeding\n";
    echo "  --help             Show this help message\n";
    exit(0);
}

// Check PHP version
if (version_compare(PHP_VERSION, '8.1.0', '<')) {
    die("❌ PHP 8.1 atau lebih tinggi diperlukan. Versi saat ini: " . PHP_VERSION . "\n");
}

echo "🚀 CodeIgniter 4 SaaS Modular Template Installer (CI/CD)\n";
echo "=======================================================\n\n";

// Check if composer is installed
if (!file_exists('composer.json')) {
    die("❌ File composer.json tidak ditemukan. Pastikan Anda berada di direktori proyek yang benar.\n");
}

// Install Composer dependencies
if (!isset($options['skip-composer']) && !is_dir('vendor')) {
    echo "📦 Installing Composer dependencies...\n";

    $composerCommands = ['composer', 'php composer.phar'];
    $composerInstalled = false;

    foreach ($composerCommands as $cmd) {
        exec("$cmd --version 2>/dev/null", $output, $returnCode);
        if ($returnCode === 0) {
            echo "✅ Composer ditemukan: $cmd\n";
            exec("$cmd install --no-dev --optimize-autoloader", $output, $returnCode);
            if ($returnCode === 0) {
                echo "✅ Dependencies berhasil diinstall.\n\n";
                $composerInstalled = true;
                break;
            }
        }
    }

    if (!$composerInstalled) {
        die("❌ Gagal menginstall dependencies. Pastikan Composer sudah terinstall.\n");
    }
}

// Create .env file
if (!file_exists('.env')) {
    echo "⚙️  Creating .env file...\n";

    // Check for both env and env.example
    $envSource = null;
    if (file_exists('env')) {
        $envSource = 'env';
    } elseif (file_exists('env.example')) {
        $envSource = 'env.example';
    }

    if ($envSource) {
        copy($envSource, '.env');
        echo "✅ File .env berhasil dibuat dari $envSource.\n";
    } else {
        // Create basic .env file
        $envContent = "CI_ENVIRONMENT = production\n";
        $envContent .= "app.baseURL = 'http://localhost:8080/'\n";
        $envContent .= "app.indexPage = ''\n";
        $envContent .= "database.default.hostname = localhost\n";
        $envContent .= "database.default.database = ci4_saas\n";
        $envContent .= "database.default.username = root\n";
        $envContent .= "database.default.password = \n";
        $envContent .= "database.default.DBDriver = MySQLi\n";
        $envContent .= "saas.tenancy = subdomain\n";
        $envContent .= "encryption.key = \n";

        file_put_contents('.env', $envContent);
        echo "✅ File .env berhasil dibuat.\n";
    }
}

// Generate encryption key
echo "🔑 Generating encryption key...\n";
if (file_exists('spark')) {
    exec('php spark key:generate', $output, $returnCode);
    if ($returnCode === 0) {
        echo "✅ Encryption key berhasil digenerate.\n";
    } else {
        echo "⚠️  Gagal generate encryption key. Silakan jalankan: php spark key:generate\n";
    }
} else {
    echo "⚠️  File spark tidak ditemukan. Silakan jalankan: php spark key:generate\n";
}

// Create writable directories
$writableDirs = [
    'writable/cache',
    'writable/logs',
    'writable/session',
    'writable/uploads',
    'writable/debugbar'
];

echo "📁 Creating writable directories...\n";
foreach ($writableDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
        echo "✅ Created: $dir\n";
    }
}

// Set permissions (skip on Windows)
if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
    echo "🔐 Setting permissions...\n";
    foreach ($writableDirs as $dir) {
        if (is_dir($dir)) {
            chmod($dir, 0755);
            echo "✅ Permissions set for: $dir\n";
        }
    }
} else {
    echo "ℹ️  Skipping permissions (Windows detected)\n";
}

// Auto-migrate
if (!isset($options['skip-migrate'])) {
    echo "\n🔄 Running database migrations...\n";
    if (file_exists('spark')) {
        exec('php spark migrate', $output, $returnCode);
        if ($returnCode === 0) {
            echo "✅ Database migrations berhasil dijalankan.\n";
        } else {
            echo "❌ Gagal menjalankan migrations. Silakan jalankan: php spark migrate\n";
        }
    } else {
        echo "⚠️  File spark tidak ditemukan. Silakan jalankan: php spark migrate\n";
    }
}

// Auto-seed
if (!isset($options['skip-seed'])) {
    echo "\n🌱 Running database seeders...\n";
    if (file_exists('spark')) {
        exec('php spark db:seed TenantSeeder', $output, $returnCode);
        if ($returnCode === 0) {
            echo "✅ Database seeders berhasil dijalankan.\n";
        } else {
            echo "❌ Gagal menjalankan seeders. Silakan jalankan: php spark db:seed TenantSeeder\n";
        }
    } else {
        echo "⚠️  File spark tidak ditemukan. Silakan jalankan: php spark db:seed TenantSeeder\n";
    }
}

echo "\n🎉 Installation completed!\n\n";

echo "📋 Next steps:\n";
echo "1. Edit file .env untuk konfigurasi database\n";
echo "2. Buat database MySQL sesuai konfigurasi di .env\n";
if (isset($options['skip-migrate'])) {
    echo "3. Jalankan migrasi: php spark migrate\n";
}
if (isset($options['skip-seed'])) {
    echo "4. Jalankan seeder: php spark db:seed TenantSeeder\n";
}
echo "5. Akses aplikasi di browser: http://localhost:8080\n\n";

echo "📚 Documentation: README.md\n";
echo "🔧 Configuration: app/Config/SaaS.php\n";
echo "🛡️  Tenant Isolation: TENANT_ISOLATION.md\n\n";

echo "Happy coding! 🚀\n";
