-- Base de données pour le site de location de voitures
-- À exécuter sur votre serveur MySQL Hostinger

-- Création de la base de données
CREATE DATABASE IF NOT EXISTS `location_voitures` 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `location_voitures`;

-- Table des voitures
CREATE TABLE IF NOT EXISTS `cars` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `titre` varchar(255) NOT NULL,
    `description` text NOT NULL,
    `prix_jour` decimal(10,2) NOT NULL,
    `image` varchar(255) NOT NULL,
    `disponible` tinyint(1) DEFAULT 1,
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table des réservations
CREATE TABLE IF NOT EXISTS `bookings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `nom_client` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `voiture_id` int(11) NOT NULL,
    `date_debut` date NOT NULL,
    `date_fin` date NOT NULL,
    `montant` decimal(10,2) NOT NULL,
    `operateur` enum('MTN','Moov') NOT NULL,
    `fichier_preuve` varchar(255) NOT NULL,
    `statut` enum('en_attente','validee','refusee') DEFAULT 'en_attente',
    `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `voiture_id` (`voiture_id`),
    CONSTRAINT `fk_booking_car` FOREIGN KEY (`voiture_id`) REFERENCES `cars` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion de données d'exemple pour les voitures
INSERT INTO `cars` (`titre`, `description`, `prix_jour`, `image`) VALUES
('Toyota Corolla', 'Berline compacte économique et fiable, parfaite pour la ville et les trajets quotidiens.', 25.00, 'toyota-corolla.jpg'),
('Honda Civic', 'Berline sportive avec un design moderne et des performances excellentes.', 30.00, 'honda-civic.jpg'),
('Ford Focus', 'Compact polyvalent offrant confort et espace pour toute la famille.', 28.00, 'ford-focus.jpg'),
('Volkswagen Golf', 'Hatchback allemand réputé pour sa qualité et sa finition.', 32.00, 'vw-golf.jpg'),
('Renault Clio', 'Citadine française élégante et maniable, idéale pour la ville.', 22.00, 'renault-clio.jpg'),
('Peugeot 208', 'Petite voiture moderne avec un design distinctif et des technologies avancées.', 24.00, 'peugeot-208.jpg'),
('BMW Série 1', 'Berline premium compacte avec des performances sportives.', 45.00, 'bmw-serie1.jpg'),
('Mercedes Classe A', 'Compacte luxueuse avec un intérieur raffiné et des équipements haut de gamme.', 50.00, 'mercedes-classea.jpg');

-- Création d'un utilisateur admin (optionnel, pour une meilleure sécurité)
-- CREATE USER 'admin_location'@'localhost' IDENTIFIED BY 'mot_de_passe_securise';
-- GRANT ALL PRIVILEGES ON location_voitures.* TO 'admin_location'@'localhost';
-- FLUSH PRIVILEGES;