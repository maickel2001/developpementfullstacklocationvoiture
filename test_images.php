<?php
require_once 'config.php';

echo "<!DOCTYPE html>
<html lang='fr'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Test des Images - Site de Location</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
    <style>
        .test-image { max-width: 300px; height: 200px; object-fit: cover; border-radius: 8px; }
        .image-card { margin-bottom: 20px; }
        .status-ok { color: #28a745; }
        .status-error { color: #dc3545; }
    </style>
</head>
<body class='bg-light'>
    <div class='container py-5'>
        <h1 class='text-center mb-5'>🧪 Test des Images du Site</h1>
        
        <div class='row'>
            <div class='col-md-6'>
                <h3>🚗 Images des Voitures</h3>";

// Test des images des voitures
$car_images = [
    'bmw-serie7.jpg' => 'BMW Série 7',
    'mercedes-classe-s.jpg' => 'Mercedes Classe S',
    'audi-a8.jpg' => 'Audi A8',
    'porsche-911.jpg' => 'Porsche 911',
    'lamborghini-huracan.jpg' => 'Lamborghini Huracán',
    'range-rover-sport.jpg' => 'Range Rover Sport',
    'tesla-model-s.jpg' => 'Tesla Model S',
    'rolls-royce-phantom.jpg' => 'Rolls-Royce Phantom',
    'bentley-continental-gt.jpg' => 'Bentley Continental GT'
];

foreach ($car_images as $image => $name) {
    $path = "images/cars/$image";
    $exists = file_exists($path);
    $status_class = $exists ? 'status-ok' : 'status-error';
    $status_text = $exists ? '✅ OK' : '❌ Manquant';
    
    echo "<div class='card image-card'>
            <div class='card-body'>
                <h5 class='card-title'>$name</h5>
                <p class='card-text'>Fichier: <code>$image</code></p>
                <p class='card-text'>Statut: <span class='$status_class'>$status_text</span></p>";
    
    if ($exists) {
        $size = filesize($path);
        $size_kb = round($size / 1024, 1);
        echo "<p class='card-text'>Taille: $size_kb KB</p>
              <img src='$path' alt='$name' class='test-image img-fluid'>";
    }
    
    echo "</div></div>";
}

echo "</div>
            <div class='col-md-6'>
                <h3>🎨 Images d'Arrière-plan</h3>";

// Test des images d'arrière-plan
$bg_images = [
    'hero-bg.jpg' => 'Hero Background',
    'road-coastal.jpg' => 'Route Côtière'
];

foreach ($bg_images as $image => $name) {
    $path = "images/backgrounds/$image";
    $exists = file_exists($path);
    $status_class = $exists ? 'status-ok' : 'status-error';
    $status_text = $exists ? '✅ OK' : '❌ Manquant';
    
    echo "<div class='card image-card'>
            <div class='card-body'>
                <h5 class='card-title'>$name</h5>
                <p class='card-text'>Fichier: <code>$image</code></p>
                <p class='card-text'>Statut: <span class='$status_class'>$status_text</span></p>";
    
    if ($exists) {
        $size = filesize($path);
        $size_kb = round($size / 1024, 1);
        echo "<p class='card-text'>Taille: $size_kb KB</p>
              <img src='$path' alt='$name' class='test-image img-fluid'>";
    }
    
    echo "</div></div>";
}

echo "</div>
        </div>
        
        <div class='row mt-4'>
            <div class='col-12'>
                <h3>🔄 Images de Fallback</h3>";
                
// Test des images de fallback
$fallback_path = "images/fallback/default-car.jpg";
$fallback_exists = file_exists($fallback_path);
$fallback_status_class = $fallback_exists ? 'status-ok' : 'status-error';
$fallback_status_text = $fallback_exists ? '✅ OK' : '❌ Manquant';

echo "<div class='card image-card'>
        <div class='card-body'>
            <h5 class='card-title'>Voiture par défaut</h5>
            <p class='card-text'>Fichier: <code>default-car.jpg</code></p>
            <p class='card-text'>Statut: <span class='$fallback_status_class'>$fallback_status_text</span></p>";

if ($fallback_exists) {
    $size = filesize($fallback_path);
    $size_kb = round($size / 1024, 1);
    echo "<p class='card-text'>Taille: $size_kb KB</p>
          <img src='$fallback_path' alt='Voiture par défaut' class='test-image img-fluid'>";
}

echo "</div></div>
            </div>
        </div>
        
        <div class='row mt-4'>
            <div class='col-12'>
                <h3>📊 Statistiques</h3>
                <div class='card'>
                    <div class='card-body'>";

// Calcul des statistiques
$total_images = 0;
$total_size = 0;
$missing_images = 0;

// Compter les images des voitures
foreach ($car_images as $image => $name) {
    $path = "images/cars/$image";
    if (file_exists($path)) {
        $total_images++;
        $total_size += filesize($path);
    } else {
        $missing_images++;
    }
}

// Compter les images d'arrière-plan
foreach ($bg_images as $image => $name) {
    $path = "images/backgrounds/$image";
    if (file_exists($path)) {
        $total_images++;
        $total_size += filesize($path);
    } else {
        $missing_images++;
    }
}

// Compter l'image de fallback
if (file_exists($fallback_path)) {
    $total_images++;
    $total_size += filesize($fallback_path);
} else {
    $missing_images++;
}

$total_size_mb = round($total_size / (1024 * 1024), 2);

echo "<p><strong>Total des images:</strong> $total_images</p>
        <p><strong>Images manquantes:</strong> $missing_images</p>
        <p><strong>Taille totale:</strong> $total_size_mb MB</p>
        <p><strong>Statut général:</strong> " . ($missing_images == 0 ? '✅ Toutes les images sont présentes' : '⚠️ Certaines images sont manquantes') . "</p>";

echo "</div></div>
            </div>
        </div>
        
        <div class='row mt-4'>
            <div class='col-12 text-center'>
                <a href='index.php' class='btn btn-primary btn-lg'>🏠 Retour à l'accueil</a>
                <a href='admin/' class='btn btn-secondary btn-lg ms-2'>⚙️ Administration</a>
            </div>
        </div>
    </div>
    
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
?>