-- Database Schema for B2B CRM

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'manager', 'sales') DEFAULT 'sales',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS companies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    sector VARCHAR(100),
    size ENUM('Small', 'Medium', 'Large', 'Enterprise'),
    website VARCHAR(255),
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(50),
    position VARCHAR(100),
    is_primary BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS leads (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL, -- Person name or Company placeholder
    company_name VARCHAR(255),
    email VARCHAR(100),
    phone VARCHAR(50),
    source VARCHAR(100), -- LinkedIn, Website, Referral
    status ENUM('New', 'Contacted', 'Qualified', 'Lost', 'Converted') DEFAULT 'New',
    score INT DEFAULT 0,
    assigned_to INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS pipeline_stages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    probability INT DEFAULT 0, -- 0 to 100
    display_order INT DEFAULT 0
);

CREATE TABLE IF NOT EXISTS opportunities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    company_id INT,
    contact_id INT,
    title VARCHAR(255) NOT NULL,
    value_estimated DECIMAL(15, 2) DEFAULT 0.00,
    value_real DECIMAL(15, 2) DEFAULT 0.00,
    stage_id INT,
    close_date DATE,
    assigned_to INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE SET NULL,
    FOREIGN KEY (stage_id) REFERENCES pipeline_stages(id),
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS interactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    related_to_type ENUM('company', 'lead', 'opportunity') NOT NULL,
    related_to_id INT NOT NULL,
    type ENUM('Call', 'Email', 'Meeting', 'Note') NOT NULL,
    notes TEXT,
    interaction_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    due_date DATETIME,
    status ENUM('Pending', 'In Progress', 'Completed') DEFAULT 'Pending',
    assigned_to INT,
    related_to_type ENUM('company', 'lead', 'opportunity'),
    related_to_id INT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Seed Data

-- Default Stages
INSERT INTO pipeline_stages (name, probability, display_order) VALUES
('Lead', 10, 1),
('Qualificado', 30, 2),
('Proposta', 60, 3),
('Negociação', 80, 4),
('Fechado', 100, 5);

-- Default Admin User (Password: admin123) - hash should be generated in PHP, keeping it simple here or using a placeholder.
-- $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi is 'password' in standard bcrypt
INSERT INTO users (name, email, password, role) VALUES
('Admin User', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');
