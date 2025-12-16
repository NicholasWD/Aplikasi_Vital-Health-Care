-- Database setup for Vital Health Tracker

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create vital_categories table
CREATE TABLE IF NOT EXISTS vital_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    unit VARCHAR(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default vital categories
INSERT INTO vital_categories (name, unit) VALUES 
    ('Blood Pressure', 'mmHg'),
    ('Heart Rate', 'bpm'),
    ('Temperature', '°C'),
    ('Blood Sugar', 'mg/dL'),
    ('Weight', 'kg'),
    ('Height', 'cm'),
    ('Oxygen Saturation', '%'),
    ('Respiratory Rate', 'breaths/min');

-- Create vital_logs table
CREATE TABLE IF NOT EXISTS vital_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    value VARCHAR(50) NOT NULL,
    note TEXT,
    log_date DATE NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES vital_categories(id) ON DELETE CASCADE,
    INDEX idx_user_date (user_id, log_date),
    INDEX idx_category (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create index for better query performance
CREATE INDEX idx_user_logs ON vital_logs(user_id, log_date DESC);

-- Sample data (optional)
-- Uncomment below to add sample data

INSERT INTO users (name, email, password) VALUES 
    ('John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
    ('Jane Smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

INSERT INTO vital_logs (user_id, category_id, value, note, log_date) VALUES
    (1, 1, '120/80', 'Normal reading', '2024-12-15'),
    (1, 2, '72', 'After exercise', '2024-12-15'),
    (1, 3, '36.6', 'Morning temperature', '2024-12-15'),
    (1, 4, '95', 'Fasting glucose', '2024-12-15');