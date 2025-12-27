-- Create database
CREATE DATABASE IF NOT EXISTS pet_qr_code_app CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE pet_qr_code_app;

-- Create pets table
CREATE TABLE IF NOT EXISTS pets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    breed VARCHAR(100),
    age INT,
    owner_name VARCHAR(100),
    owner_contact VARCHAR(100),
    medical_history TEXT,
    qr_code_path VARCHAR(255),
    unique_token VARCHAR(64) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_unique_token (unique_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
