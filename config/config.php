<?php
// Configuration générale du site
define('SITE_NAME', 'SMM Platform');
define('SITE_URL', 'http://localhost');
define('SITE_EMAIL', 'support@smmplatform.com');

// Configuration des couleurs (modifiable via admin)
define('PRIMARY_COLOR', '#ff7a00');
define('SECONDARY_COLOR', '#1a1a1a');
define('BACKGROUND_COLOR', '#0f0f0f');
define('TEXT_COLOR', '#ffffff');

// Configuration des emails
define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 587);
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SMTP_FROM', 'noreply@smmplatform.com');
define('SMTP_BCC', 'admin@smmplatform.com');

// Configuration des uploads
define('UPLOAD_MAX_SIZE', 5 * 1024 * 1024); // 5MB
define('UPLOAD_ALLOWED_TYPES', ['jpg', 'jpeg', 'png', 'pdf']);
define('UPLOAD_PATH', 'uploads/proofs/');

// Configuration de sécurité
define('SESSION_TIMEOUT', 3600); // 1 heure
define('PASSWORD_MIN_LENGTH', 8);
define('LOGIN_MAX_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900); // 15 minutes

// Mode maintenance
define('MAINTENANCE_MODE', false);
define('MAINTENANCE_MESSAGE', 'Site en maintenance. Merci de revenir plus tard.');

// Configuration des logs
define('LOG_ENABLED', true);
define('LOG_PATH', 'logs/');
define('LOG_LEVEL', 'INFO'); // DEBUG, INFO, WARNING, ERROR
?>