-- Initialize database for CI4 SaaS Modular
CREATE DATABASE IF NOT EXISTS ci4_saas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Create user for application
CREATE USER IF NOT EXISTS 'ci4_user'@'%' IDENTIFIED BY 'ci4_password';
GRANT ALL PRIVILEGES ON ci4_saas.* TO 'ci4_user'@'%';
FLUSH PRIVILEGES;

-- Use the database
USE ci4_saas;

-- Create initial tables (will be handled by migrations)
-- This is just for initial setup
