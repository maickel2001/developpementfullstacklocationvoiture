-- Schéma de base de données pour SMM Platform
-- Créé le : 2024
-- Version : 1.0

-- Création de la base de données
CREATE DATABASE IF NOT EXISTS smm_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE smm_platform;

-- Table des utilisateurs
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    active BOOLEAN DEFAULT TRUE,
    email_verified BOOLEAN DEFAULT FALSE,
    verification_token VARCHAR(255),
    reset_token VARCHAR(255),
    reset_token_expires DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login DATETIME,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_active (active)
);

-- Table des tentatives de connexion (sécurité)
CREATE TABLE login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    email VARCHAR(255) NOT NULL,
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ip_time (ip_address, attempt_time),
    INDEX idx_time (attempt_time)
);

-- Table des catégories de services
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_active (active),
    INDEX idx_sort (sort_order)
);

-- Table des services
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    price_per_unit ENUM('per_1000', 'per_unit', 'fixed') DEFAULT 'per_1000',
    min_quantity INT DEFAULT 1,
    max_quantity INT DEFAULT 100000,
    delivery_time VARCHAR(100),
    active BOOLEAN DEFAULT TRUE,
    popularity INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    INDEX idx_category (category_id),
    INDEX idx_active (active),
    INDEX idx_popularity (popularity),
    INDEX idx_price (price)
);

-- Table des commandes
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_id INT NOT NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    link VARCHAR(500) NOT NULL,
    quantity INT NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
    admin_notes TEXT,
    cancellation_reason TEXT,
    payment_proof_path VARCHAR(500),
    payment_proof_original_name VARCHAR(255),
    payment_proof_size INT,
    payment_proof_type VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    completed_at DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT,
    INDEX idx_user (user_id),
    INDEX idx_service (service_id),
    INDEX idx_status (status),
    INDEX idx_created (created_at),
    INDEX idx_order_number (order_number)
);

-- Table des avis clients
CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    order_id INT,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_order (order_id),
    INDEX idx_rating (rating),
    INDEX idx_active (active)
);

-- Table des paramètres du site
CREATE TABLE site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('string', 'number', 'boolean', 'json') DEFAULT 'string',
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Table des logs d'emails
CREATE TABLE email_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    recipient VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    template VARCHAR(100),
    status ENUM('sent', 'failed', 'pending') DEFAULT 'pending',
    error_message TEXT,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_recipient (recipient),
    INDEX idx_status (status),
    INDEX idx_sent_at (sent_at)
);

-- Table des logs système
CREATE TABLE system_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    level ENUM('DEBUG', 'INFO', 'WARNING', 'ERROR') DEFAULT 'INFO',
    message TEXT NOT NULL,
    context JSON,
    user_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_level (level),
    INDEX idx_created (created_at),
    INDEX idx_user (user_id)
);

-- Insertion des données initiales

-- Catégories par défaut
INSERT INTO categories (name, description, icon, sort_order) VALUES
('Instagram', 'Services pour Instagram : followers, likes, commentaires', '📸', 1),
('Facebook', 'Services pour Facebook : likes, partages, commentaires', '📘', 2),
('YouTube', 'Services pour YouTube : vues, likes, abonnés', '📺', 3),
('TikTok', 'Services pour TikTok : followers, likes, vues', '🎵', 4),
('Twitter', 'Services pour Twitter : followers, retweets, likes', '🐦', 5),
('LinkedIn', 'Services pour LinkedIn : connexions, recommandations', '💼', 6);

-- Services par défaut
INSERT INTO services (category_id, name, description, price, min_quantity, max_quantity, delivery_time, popularity) VALUES
(1, 'Followers Instagram', 'Followers Instagram de haute qualité', 2.50, 100, 10000, '24-48h', 100),
(1, 'Likes Instagram', 'Likes Instagram sur vos posts', 1.00, 100, 5000, '1-2h', 95),
(1, 'Commentaires Instagram', 'Commentaires personnalisés', 5.00, 10, 100, '2-4h', 80),
(2, 'Likes Facebook', 'Likes Facebook sur vos posts', 1.50, 100, 10000, '1-3h', 90),
(2, 'Partages Facebook', 'Partages de vos contenus', 3.00, 50, 1000, '2-6h', 85),
(3, 'Vues YouTube', 'Vues YouTube sur vos vidéos', 1.00, 1000, 100000, '24-72h', 100),
(3, 'Abonnés YouTube', 'Abonnés YouTube réels', 8.00, 100, 5000, '48-96h', 95),
(4, 'Followers TikTok', 'Followers TikTok actifs', 3.00, 100, 5000, '24-48h', 90),
(5, 'Followers Twitter', 'Followers Twitter de qualité', 2.00, 100, 10000, '24-48h', 85);

-- Paramètres du site par défaut
INSERT INTO site_settings (setting_key, setting_value, setting_type, description) VALUES
('site_name', 'SMM Platform', 'string', 'Nom du site'),
('site_description', 'Plateforme de services de marketing sur les réseaux sociaux', 'string', 'Description du site'),
('primary_color', '#ff7a00', 'string', 'Couleur primaire du site'),
('maintenance_mode', 'false', 'boolean', 'Mode maintenance activé/désactivé'),
('maintenance_message', 'Site en maintenance. Merci de revenir plus tard.', 'string', 'Message de maintenance'),
('smtp_host', 'localhost', 'string', 'Serveur SMTP'),
('smtp_port', '587', 'number', 'Port SMTP'),
('smtp_from', 'noreply@smmplatform.com', 'string', 'Email expéditeur'),
('smtp_bcc', 'admin@smmplatform.com', 'string', 'Email BCC'),
('upload_max_size', '5242880', 'number', 'Taille maximale des uploads (en octets)'),
('upload_allowed_types', '["jpg","jpeg","png","pdf"]', 'json', 'Types de fichiers autorisés'),
('session_timeout', '3600', 'number', 'Timeout de session (en secondes)'),
('password_min_length', '8', 'number', 'Longueur minimale du mot de passe'),
('login_max_attempts', '5', 'number', 'Nombre maximum de tentatives de connexion'),
('login_lockout_time', '900', 'number', 'Temps de verrouillage (en secondes)');

-- Création d'un utilisateur admin par défaut (mot de passe: admin123)
INSERT INTO users (email, password, first_name, last_name, role, email_verified, active) VALUES
('admin@smmplatform.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'System', 'admin', TRUE, TRUE);

-- Index supplémentaires pour les performances
CREATE INDEX idx_orders_user_status ON orders(user_id, status);
CREATE INDEX idx_orders_service_status ON orders(service_id, status);
CREATE INDEX idx_services_category_active ON services(category_id, active);
CREATE INDEX idx_users_role_active ON users(role, active);

-- Vues utiles
CREATE VIEW order_summary AS
SELECT 
    o.id,
    o.order_number,
    o.status,
    o.total_amount,
    o.created_at,
    u.email as user_email,
    u.first_name,
    u.last_name,
    s.name as service_name,
    c.name as category_name
FROM orders o
JOIN users u ON o.user_id = u.id
JOIN services s ON o.service_id = s.id
JOIN categories c ON s.category_id = c.id;

CREATE VIEW service_stats AS
SELECT 
    s.id,
    s.name,
    s.price,
    COUNT(o.id) as total_orders,
    SUM(CASE WHEN o.status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
    SUM(CASE WHEN o.status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
    SUM(CASE WHEN o.status = 'processing' THEN 1 ELSE 0 END) as processing_orders,
    SUM(CASE WHEN o.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders
FROM services s
LEFT JOIN orders o ON s.id = o.service_id
GROUP BY s.id, s.name, s.price;

-- Procédures stockées utiles
DELIMITER //

CREATE PROCEDURE CleanupOldLogs(IN days_to_keep INT)
BEGIN
    DELETE FROM login_attempts WHERE attempt_time < DATE_SUB(NOW(), INTERVAL days_to_keep DAY);
    DELETE FROM email_logs WHERE sent_at < DATE_SUB(NOW(), INTERVAL days_to_keep DAY);
    DELETE FROM system_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL days_to_keep DAY);
END //

CREATE PROCEDURE GetOrderStats(IN user_id_param INT)
BEGIN
    SELECT 
        status,
        COUNT(*) as count,
        SUM(total_amount) as total_amount
    FROM orders 
    WHERE user_id = user_id_param 
    GROUP BY status;
END //

DELIMITER ;

-- Déclencheurs pour la mise à jour automatique
DELIMITER //

CREATE TRIGGER update_order_updated_at
BEFORE UPDATE ON orders
FOR EACH ROW
BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END //

CREATE TRIGGER update_service_updated_at
BEFORE UPDATE ON services
FOR EACH ROW
BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END //

CREATE TRIGGER update_user_updated_at
BEFORE UPDATE ON users
FOR EACH ROW
BEGIN
    SET NEW.updated_at = CURRENT_TIMESTAMP;
END //

DELIMITER ;

-- Commentaires sur les tables
ALTER TABLE users COMMENT 'Table des utilisateurs de la plateforme';
ALTER TABLE orders COMMENT 'Table des commandes des clients';
ALTER TABLE services COMMENT 'Table des services proposés';
ALTER TABLE categories COMMENT 'Table des catégories de services';
ALTER TABLE reviews COMMENT 'Table des avis clients';
ALTER TABLE site_settings COMMENT 'Table des paramètres configurables du site';
ALTER TABLE email_logs COMMENT 'Table des logs des emails envoyés';
ALTER TABLE system_logs COMMENT 'Table des logs système';
ALTER TABLE login_attempts COMMENT 'Table des tentatives de connexion pour la sécurité';