<?php
require_once 'config.php';

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Configuration Base de Données - Site de Location</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        .info { color: #17a2b8; }
    </style>
</head>
<body class='bg-light'>
    <div class='container py-5'>
        <h1 class='text-center mb-5'>⚙️ Configuration de la Base de Données</h1>
        
        <div class='row'>
            <div class='col-lg-8 mx-auto'>
                <div class='card'>
                    <div class='card-body'>
                        <h5 class='card-title'>🚗 Mise à jour des Images des Voitures</h5>
                        <p class='card-text'>Ce script va mettre à jour la base de données avec les nouvelles images téléchargées.</p>
                        
                        <form method='POST' action=''>
                            <div class='mb-3'>
                                <label class='form-label'>Action à effectuer :</label>
                                <select name='action' class='form-select' required>
                                    <option value=''>Choisir une action...</option>
                                    <option value='update'>Mettre à jour les images existantes</option>
                                    <option value='add'>Ajouter de nouvelles voitures</option>
                                    <option value='both'>Faire les deux</option>
                                    <option value='check'>Vérifier l'état actuel</option>
                                </select>
                            </div>
                            
                            <div class='d-grid gap-2'>
                                <button type='submit' class='btn btn-primary'>🚀 Exécuter</button>
                            </div>
                        </form>
                    </div>
                </div>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    echo "<div class='mt-4'>";
    
    try {
        switch ($action) {
            case 'check':
                echo "<h4>🔍 Vérification de l'état actuel</h4>";
                checkCurrentState($pdo);
                break;
                
            case 'update':
                echo "<h4>🔄 Mise à jour des images existantes</h4>";
                updateExistingImages($pdo);
                break;
                
            case 'add':
                echo "<h4>➕ Ajout de nouvelles voitures</h4>";
                addNewCars($pdo);
                break;
                
            case 'both':
                echo "<h4>🚀 Mise à jour complète</h4>";
                updateExistingImages($pdo);
                echo "<hr>";
                addNewCars($pdo);
                break;
                
            default:
                echo "<div class='alert alert-warning'>Action non reconnue.</div>";
        }
    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>Erreur : " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    
    echo "</div>";
}

echo "</div>
        </div>
        
        <div class='row mt-4'>
            <div class='col-12 text-center'>
                <a href='test_images.php' class='btn btn-info btn-lg'>🧪 Tester les Images</a>
                <a href='index.php' class='btn btn-primary btn-lg ms-2'>🏠 Retour à l'accueil</a>
                <a href='admin/' class='btn btn-secondary btn-lg ms-2'>⚙️ Administration</a>
            </div>
        </div>
    </div>
    
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";

// Fonctions pour la gestion de la base de données
function checkCurrentState($pdo) {
    echo "<div class='card mb-3'>";
    echo "<div class='card-body'>";
    
    // Compter les voitures
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM cars");
    $total = $stmt->fetch()['total'];
    
    // Compter les voitures avec images
    $stmt = $pdo->query("SELECT COUNT(*) as with_images FROM cars WHERE image IS NOT NULL AND image != ''");
    $withImages = $stmt->fetch()['with_images'];
    
    // Compter les voitures disponibles
    $stmt = $pdo->query("SELECT COUNT(*) as available FROM cars WHERE disponible = 1");
    $available = $stmt->fetch()['available'];
    
    echo "<h6>📊 Statistiques actuelles :</h6>";
    echo "<ul>";
    echo "<li><strong>Total des voitures :</strong> $total</li>";
    echo "<li><strong>Avec images :</strong> $withImages</li>";
    echo "<li><strong>Disponibles :</strong> $available</li>";
    echo "</ul>";
    
    // Lister les voitures actuelles
    $stmt = $pdo->query("SELECT id, titre, image, prix_jour, disponible FROM cars ORDER BY prix_jour ASC");
    $cars = $stmt->fetchAll();
    
    if ($cars) {
        echo "<h6>🚗 Voitures actuelles :</h6>";
        echo "<div class='table-responsive'>";
        echo "<table class='table table-sm'>";
        echo "<thead><tr><th>ID</th><th>Modèle</th><th>Image</th><th>Prix/jour</th><th>Disponible</th></tr></thead>";
        echo "<tbody>";
        
        foreach ($cars as $car) {
            $status_class = $car['disponible'] ? 'success' : 'warning';
            $status_text = $car['disponible'] ? 'Oui' : 'Non';
            $image_status = $car['image'] ? '✅' : '❌';
            
            echo "<tr>";
            echo "<td>{$car['id']}</td>";
            echo "<td>{$car['titre']}</td>";
            echo "<td>$image_status {$car['image']}</td>";
            echo "<td>{$car['prix_jour']} €</td>";
            echo "<td class='$status_class'>$status_text</td>";
            echo "</tr>";
        }
        
        echo "</tbody></table>";
        echo "</div>";
    }
    
    echo "</div></div>";
}

function updateExistingImages($pdo) {
    echo "<div class='card mb-3'>";
    echo "<div class='card-body'>";
    
    // Mise à jour des images existantes
    $updates = [
        "UPDATE cars SET image = 'bmw-serie7.jpg' WHERE titre LIKE '%BMW%'" => 'BMW',
        "UPDATE cars SET image = 'mercedes-classe-s.jpg' WHERE titre LIKE '%Mercedes%'" => 'Mercedes',
        "UPDATE cars SET image = 'audi-a8.jpg' WHERE titre LIKE '%Audi%'" => 'Audi',
        "UPDATE cars SET image = 'porsche-911.jpg' WHERE titre LIKE '%Porsche%'" => 'Porsche',
        "UPDATE cars SET image = 'lamborghini-huracan.jpg' WHERE titre LIKE '%Lamborghini%'" => 'Lamborghini',
        "UPDATE cars SET image = 'range-rover-sport.jpg' WHERE titre LIKE '%Range Rover%'" => 'Range Rover',
        "UPDATE cars SET image = 'tesla-model-s.jpg' WHERE titre LIKE '%Tesla%'" => 'Tesla',
        "UPDATE cars SET image = 'rolls-royce-phantom.jpg' WHERE titre LIKE '%Rolls%'" => 'Rolls-Royce',
        "UPDATE cars SET image = 'bentley-continental-gt.jpg' WHERE titre LIKE '%Bentley%'" => 'Bentley'
    ];
    
    $updated = 0;
    foreach ($updates as $sql => $brand) {
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $affected = $stmt->rowCount();
            if ($affected > 0) {
                echo "<div class='alert alert-success'>✅ $brand : $affected voiture(s) mise(s) à jour</div>";
                $updated += $affected;
            } else {
                echo "<div class='alert alert-info'>ℹ️ $brand : Aucune voiture trouvée</div>";
            }
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>❌ Erreur pour $brand : " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
    
    echo "<div class='alert alert-info'>📊 Total des mises à jour : $updated voiture(s)</div>";
    echo "</div></div>";
}

function addNewCars($pdo) {
    echo "<div class='card mb-3'>";
    echo "<div class='card-body'>";
    
    // Nouvelles voitures à ajouter
    $newCars = [
        [
            'titre' => 'BMW Série 7',
            'description' => 'Berline de luxe ultime avec intérieur premium et technologie de pointe. Idéale pour les voyages d\'affaires et les déplacements familiaux.',
            'prix_jour' => 150.00,
            'image' => 'bmw-serie7.jpg'
        ],
        [
            'titre' => 'Mercedes Classe S',
            'description' => 'Élégance allemande et technologie de pointe. La berline de référence pour le luxe et le confort.',
            'prix_jour' => 180.00,
            'image' => 'mercedes-classe-s.jpg'
        ],
        [
            'titre' => 'Audi A8',
            'description' => 'Technologie et confort au service du luxe. Intérieur raffiné et performances exceptionnelles.',
            'prix_jour' => 160.00,
            'image' => 'audi-a8.jpg'
        ],
        [
            'titre' => 'Porsche 911',
            'description' => 'Performance pure et design iconique. La sportive par excellence pour les passionnés de conduite.',
            'prix_jour' => 200.00,
            'image' => 'porsche-911.jpg'
        ],
        [
            'titre' => 'Lamborghini Huracán',
            'description' => 'Supercar italienne au design agressif. Performance et exclusivité pour des expériences uniques.',
            'prix_jour' => 350.00,
            'image' => 'lamborghini-huracan.jpg'
        ],
        [
            'titre' => 'Range Rover Sport',
            'description' => 'Luxe et tout-terrain en un. Élégance britannique et capacités hors-route exceptionnelles.',
            'prix_jour' => 220.00,
            'image' => 'range-rover-sport.jpg'
        ],
        [
            'titre' => 'Tesla Model S',
            'description' => 'Électrique premium et performance. Technologie de pointe et respect de l\'environnement.',
            'prix_jour' => 180.00,
            'image' => 'tesla-model-s.jpg'
        ],
        [
            'titre' => 'Rolls-Royce Phantom',
            'description' => 'L\'excellence automobile britannique. Le summum du luxe et du raffinement.',
            'prix_jour' => 500.00,
            'image' => 'rolls-royce-phantom.jpg'
        ],
        [
            'titre' => 'Bentley Continental GT',
            'description' => 'Grand tourisme britannique raffiné. Confort et performance pour les voyages longue distance.',
            'prix_jour' => 280.00,
            'image' => 'bentley-continental-gt.jpg'
        ]
    ];
    
    $added = 0;
    foreach ($newCars as $car) {
        try {
            // Vérifier si la voiture existe déjà
            $stmt = $pdo->prepare("SELECT id FROM cars WHERE titre = ?");
            $stmt->execute([$car['titre']]);
            
            if ($stmt->rowCount() == 0) {
                // Ajouter la nouvelle voiture
                $stmt = $pdo->prepare("INSERT INTO cars (titre, description, prix_jour, image, disponible) VALUES (?, ?, ?, ?, 1)");
                $stmt->execute([$car['titre'], $car['description'], $car['prix_jour'], $car['image']]);
                
                echo "<div class='alert alert-success'>✅ {$car['titre']} ajoutée avec succès</div>";
                $added++;
            } else {
                echo "<div class='alert alert-info'>ℹ️ {$car['titre']} existe déjà</div>";
            }
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>❌ Erreur pour {$car['titre']} : " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
    
    echo "<div class='alert alert-info'>📊 Total des nouvelles voitures ajoutées : $added</div>";
    echo "</div></div>";
}
?>