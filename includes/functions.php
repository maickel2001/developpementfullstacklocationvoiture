<?php
require_once __DIR__ . '/../config/config.php';

function isMaintenanceMode() {
    return defined('MAINTENANCE_MODE') && MAINTENANCE_MODE === true;
}

function getPopularServices() {
    try {
        $pdo = getDB();
        $stmt = $pdo->prepare("SELECT * FROM services WHERE active = 1 ORDER BY popularity DESC LIMIT 6");
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Erreur lors de la récupération des services: " . $e->getMessage());
        return [];
    }
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function formatPrice($price) {
    return number_format($price, 2, ',', ' ') . '€';
}
?>