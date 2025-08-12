<?php
// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'location_voitures');
define('DB_USER', 'votre_utilisateur');
define('DB_PASS', 'votre_mot_de_passe');

// Configuration du site
define('SITE_NAME', 'Location de Voitures');
define('SITE_URL', 'https://votre-domaine.com');

// Configuration des uploads
define('UPLOAD_DIR', 'uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 Mo
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png']);

// Configuration admin
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'admin123'); // À changer en production !

// Connexion à la base de données
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch(PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Fonction de nettoyage des entrées
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Fonction de validation des dates
function validateDates($dateDebut, $dateFin) {
    $debut = new DateTime($dateDebut);
    $fin = new DateTime($dateFin);
    $aujourdhui = new DateTime();
    
    if ($debut < $aujourdhui) {
        return false;
    }
    
    if ($debut >= $fin) {
        return false;
    }
    
    return true;
}

// Fonction de validation des fichiers
function validateFile($file) {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return false;
    }
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        return false;
    }
    
    return true;
}
?>