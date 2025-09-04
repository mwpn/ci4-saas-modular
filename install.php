<?php

/**
 * CodeIgniter 4 SaaS Modular Template Installer
 * 
 * Script untuk setup otomatis template CI4 SaaS Modular
 */

// Check PHP version
if (version_compare(PHP_VERSION, '8.1.0', '<')) {
    die("❌ PHP 8.1 atau lebih tinggi diperlukan. Versi saat ini: " . PHP_VERSION . "\n");
}

echo "🚀 CodeIgniter 4 SaaS Modular Template Installer\n";
echo "================================================\n\n";

// Check if composer is installed
if (!file_exists('composer.json')) {
    die("❌ File composer.json tidak ditemukan. Pastikan Anda berada di direktori proyek yang benar.\n");
}

// Check if vendor directory exists
if (!is_dir('vendor')) {
    echo "📦 Installing Composer dependencies...\n";

    // Try different composer commands
    $composerCommands = ['composer', 'php composer.phar'];
    $composerInstalled = false;

    foreach ($composerCommands as $cmd) {
        exec("$cmd --version 2>/dev/null", $output, $returnCode);
        if ($returnCode === 0) {
            echo "✅ Composer ditemukan: $cmd\n";
            exec("$cmd install", $output, $returnCode);
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

// Create .env file if not exists
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
        $envContent = "CI_ENVIRONMENT = development\n";
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
    echo "⚠️  Silakan edit file .env untuk konfigurasi database dan lainnya.\n\n";
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

// Function to create database if not exists
function createDatabaseIfNotExists($host, $username, $password, $database) {
    try {
        // Try mysqli first
        $connection = @mysqli_connect($host, $username, $password);
        
        if ($connection) {
            // Check if database exists
            $result = mysqli_query($connection, "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '$database'");
            
            if (mysqli_num_rows($result) > 0) {
                echo "✅ Database '$database' already exists\n";
                mysqli_close($connection);
                return true;
            } else {
                // Create database
                $createQuery = "CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
                if (mysqli_query($connection, $createQuery)) {
                    echo "✅ Database '$database' created successfully\n";
                    mysqli_close($connection);
                    return true;
                } else {
                    echo "❌ Failed to create database: " . mysqli_error($connection) . "\n";
                    mysqli_close($connection);
                    return false;
                }
            }
        } else {
            echo "❌ Failed to connect to MySQL server: " . mysqli_connect_error() . "\n";
            return false;
        }
    } catch (Exception $e) {
        echo "❌ Database creation error: " . $e->getMessage() . "\n";
        return false;
    }
}

// Check if database is configured and create if needed
echo "\n🗄️  Checking database configuration...\n";
$envFile = file_get_contents('.env');

// Extract database configuration from .env
$dbHost = 'localhost';
$dbName = 'ci4_saas';
$dbUser = 'root';
$dbPass = '';

preg_match('/database\.default\.hostname = (.+)/', $envFile, $matches);
if (!empty($matches[1])) $dbHost = trim($matches[1]);

preg_match('/database\.default\.database = (.+)/', $envFile, $matches);
if (!empty($matches[1])) $dbName = trim($matches[1]);

preg_match('/database\.default\.username = (.+)/', $envFile, $matches);
if (!empty($matches[1])) $dbUser = trim($matches[1]);

preg_match('/database\.default\.password = (.+)/', $envFile, $matches);
if (!empty($matches[1])) $dbPass = trim($matches[1]);

echo "🔍 Database config: $dbUser@$dbHost/$dbName\n";

// Ask if user wants to auto-create database
echo "\n🤔 Auto-create database if not exists? (y/n): ";
$autoCreateDb = trim(fgets(STDIN));

if (strtolower($autoCreateDb) === 'y' || strtolower($autoCreateDb) === 'yes') {
    echo "🔄 Creating database if not exists...\n";
    $dbCreated = createDatabaseIfNotExists($dbHost, $dbUser, $dbPass, $dbName);
    
    if (!$dbCreated) {
        echo "⚠️  Database creation failed. Please create database manually.\n";
        echo "   SQL: CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n\n";
    }
} else {
    echo "ℹ️  Skipping database creation. Please ensure database '$dbName' exists.\n";
}

// Create writable directories if not exist
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

// Ask for auto-migrate and auto-seed
echo "\n🤔 Auto-setup options:\n";
echo "1. Auto-migrate database (y/n): ";
$autoMigrate = trim(fgets(STDIN));
echo "2. Auto-seed database (y/n): ";
$autoSeed = trim(fgets(STDIN));

// Auto-migrate
if (strtolower($autoMigrate) === 'y' || strtolower($autoMigrate) === 'yes') {
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
if (strtolower($autoSeed) === 'y' || strtolower($autoSeed) === 'yes') {
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
echo "1. Database sudah dikonfigurasi dan dibuat (jika dipilih)\n";
if (strtolower($autoMigrate) !== 'y' && strtolower($autoMigrate) !== 'yes') {
    echo "2. Jalankan migrasi: php spark migrate\n";
}
if (strtolower($autoSeed) !== 'y' && strtolower($autoSeed) !== 'yes') {
    echo "3. Jalankan seeder: php spark db:seed TenantSeeder\n";
}
echo "4. Akses aplikasi di browser: http://localhost:8080\n\n";

echo "📚 Documentation: README.md\n";
echo "🔧 Configuration: app/Config/SaaS.php\n";
echo "🛡️  Tenant Isolation: TENANT_ISOLATION.md\n\n";

echo "Happy coding! 🚀\n";
