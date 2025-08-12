-- 🚗 Mise à jour des Images des Voitures
-- Script SQL pour mettre à jour la base de données avec les nouvelles images

USE location_voitures;

-- Mise à jour des images existantes
UPDATE cars SET image = 'bmw-serie7.jpg' WHERE titre LIKE '%BMW%';
UPDATE cars SET image = 'mercedes-classe-s.jpg' WHERE titre LIKE '%Mercedes%';
UPDATE cars SET image = 'audi-a8.jpg' WHERE titre LIKE '%Audi%';
UPDATE cars SET image = 'porsche-911.jpg' WHERE titre LIKE '%Porsche%';
UPDATE cars SET image = 'lamborghini-huracan.jpg' WHERE titre LIKE '%Lamborghini%';
UPDATE cars SET image = 'range-rover-sport.jpg' WHERE titre LIKE '%Range Rover%';
UPDATE cars SET image = 'tesla-model-s.jpg' WHERE titre LIKE '%Tesla%';
UPDATE cars SET image = 'rolls-royce-phantom.jpg' WHERE titre LIKE '%Rolls%';
UPDATE cars SET image = 'bentley-continental-gt.jpg' WHERE titre LIKE '%Bentley%';

-- Ajout de nouvelles voitures premium avec les images téléchargées
INSERT INTO cars (titre, description, prix_jour, image, disponible) VALUES
('BMW Série 7', 'Berline de luxe ultime avec intérieur premium et technologie de pointe. Idéale pour les voyages d\'affaires et les déplacements familiaux.', 150.00, 'bmw-serie7.jpg', 1),
('Mercedes Classe S', 'Élégance allemande et technologie de pointe. La berline de référence pour le luxe et le confort.', 180.00, 'mercedes-classe-s.jpg', 1),
('Audi A8', 'Technologie et confort au service du luxe. Intérieur raffiné et performances exceptionnelles.', 160.00, 'audi-a8.jpg', 1),
('Porsche 911', 'Performance pure et design iconique. La sportive par excellence pour les passionnés de conduite.', 200.00, 'porsche-911.jpg', 1),
('Lamborghini Huracán', 'Supercar italienne au design agressif. Performance et exclusivité pour des expériences uniques.', 350.00, 'lamborghini-huracan.jpg', 1),
('Range Rover Sport', 'Luxe et tout-terrain en un. Élégance britannique et capacités hors-route exceptionnelles.', 220.00, 'range-rover-sport.jpg', 1),
('Tesla Model S', 'Électrique premium et performance. Technologie de pointe et respect de l\'environnement.', 180.00, 'tesla-model-s.jpg', 1),
('Rolls-Royce Phantom', 'L\'excellence automobile britannique. Le summum du luxe et du raffinement.', 500.00, 'rolls-royce-phantom.jpg', 1),
('Bentley Continental GT', 'Grand tourisme britannique raffiné. Confort et performance pour les voyages longue distance.', 280.00, 'bentley-continental-gt.jpg', 1);

-- Vérification des mises à jour
SELECT id, titre, image, prix_jour, disponible FROM cars ORDER BY prix_jour ASC;

-- Statistiques des voitures
SELECT 
    COUNT(*) as total_voitures,
    COUNT(CASE WHEN disponible = 1 THEN 1 END) as voitures_disponibles,
    AVG(prix_jour) as prix_moyen,
    MIN(prix_jour) as prix_minimum,
    MAX(prix_jour) as prix_maximum
FROM cars;