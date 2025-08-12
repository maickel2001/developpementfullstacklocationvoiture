<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Rediriger si déjà connecté
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

// Traitement de l'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $agree_terms = isset($_POST['agree_terms']);
    
    // Validation
    if (empty($email) || empty($password) || empty($confirm_password) || empty($first_name) || empty($last_name)) {
        $error = 'Veuillez remplir tous les champs';
    } elseif (!validateEmail($email)) {
        $error = 'Veuillez entrer une adresse email valide';
    } elseif (strlen($password) < PASSWORD_MIN_LENGTH) {
        $error = 'Le mot de passe doit contenir au moins ' . PASSWORD_MIN_LENGTH . ' caractères';
    } elseif ($password !== $confirm_password) {
        $error = 'Les mots de passe ne correspondent pas';
    } elseif (!$agree_terms) {
        $error = 'Vous devez accepter les conditions d\'utilisation';
    } else {
        try {
            $pdo = getDB();
            
            // Vérifier si l'email existe déjà
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $error = 'Cette adresse email est déjà utilisée';
            } else {
                // Créer l'utilisateur
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $verification_token = bin2hex(random_bytes(32));
                
                $stmt = $pdo->prepare("INSERT INTO users (email, password, first_name, last_name, role, verification_token, created_at) VALUES (?, ?, ?, ?, 'user', ?, NOW())");
                $stmt->execute([$email, $hashed_password, $first_name, $last_name, $verification_token]);
                
                $user_id = $pdo->lastInsertId();
                
                // Envoyer l'email de bienvenue
                if (sendWelcomeEmail($email, $first_name, $verification_token)) {
                    $success = 'Inscription réussie ! Un email de confirmation a été envoyé à votre adresse.';
                } else {
                    $success = 'Inscription réussie ! Vous pouvez maintenant vous connecter.';
                }
                
                // Rediriger vers la connexion après 3 secondes
                header("refresh:3;url=login.php");
            }
        } catch (Exception $e) {
            error_log("Erreur d'inscription: " . $e->getMessage());
            $error = 'Une erreur est survenue. Veuillez réessayer.';
        }
    }
}

// Fonction pour envoyer l'email de bienvenue
function sendWelcomeEmail($email, $first_name, $token) {
    try {
        $subject = "Bienvenue sur SMM Platform - Confirmez votre compte";
        $verification_link = SITE_URL . "/verify.php?token=" . $token;
        
        $message = "
        <html>
        <head>
            <title>Bienvenue sur SMM Platform</title>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h1 style='color: #ff7a00; text-align: center;'>Bienvenue sur SMM Platform !</h1>
                
                <p>Bonjour {$first_name},</p>
                
                <p>Merci de vous être inscrit sur SMM Platform. Votre compte a été créé avec succès !</p>
                
                <p>Pour commencer à utiliser nos services, veuillez confirmer votre adresse email en cliquant sur le bouton ci-dessous :</p>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='{$verification_link}' style='background: #ff7a00; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block;'>Confirmer mon compte</a>
                </div>
                
                <p>Si le bouton ne fonctionne pas, vous pouvez copier et coller ce lien dans votre navigateur :</p>
                <p style='word-break: break-all; color: #666;'>{$verification_link}</p>
                
                <p>Ce lien expirera dans 24 heures pour des raisons de sécurité.</p>
                
                <hr style='border: none; border-top: 1px solid #eee; margin: 30px 0;'>
                
                <p style='font-size: 14px; color: #666;'>
                    Si vous n'avez pas créé de compte sur SMM Platform, vous pouvez ignorer cet email.
                </p>
                
                <p style='font-size: 14px; color: #666;'>
                    Cordialement,<br>
                    L'équipe SMM Platform
                </p>
            </div>
        </body>
        </html>";
        
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . SMTP_FROM,
            'Reply-To: ' . SITE_EMAIL,
            'X-Mailer: PHP/' . phpversion()
        ];
        
        if (SMTP_BCC) {
            $headers[] = 'Bcc: ' . SMTP_BCC;
        }
        
        return mail($email, $subject, $message, implode("\r\n", $headers));
    } catch (Exception $e) {
        error_log("Erreur envoi email bienvenue: " . $e->getMessage());
        return false;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - SMM Platform</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: linear-gradient(135deg, var(--background-color) 0%, var(--surface-color) 100%);
        }
        
        .auth-card {
            background: var(--card-color);
            border-radius: var(--radius-lg);
            padding: 2.5rem;
            width: 100%;
            max-width: 500px;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-xl);
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .auth-logo {
            font-size: 2rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--primary-color), #ff9500);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }
        
        .auth-subtitle {
            color: var(--text-secondary);
            font-size: 1rem;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            color: var(--text-primary);
            font-size: 1rem;
            transition: var(--transition-fast);
        }
        
        .form-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(255, 122, 0, 0.1);
        }
        
        .checkbox-group {
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin: 0;
            margin-top: 0.25rem;
        }
        
        .checkbox-group label {
            font-size: 0.9rem;
            color: var(--text-secondary);
            line-height: 1.4;
        }
        
        .checkbox-group a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .checkbox-group a:hover {
            text-decoration: underline;
        }
        
        .auth-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }
        
        .auth-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
        
        .auth-footer a:hover {
            text-decoration: underline;
        }
        
        .alert {
            padding: 1rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
        
        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }
        
        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }
        
        @media (max-width: 480px) {
            .auth-card {
                padding: 2rem 1.5rem;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">SMM Platform</div>
                <p class="auth-subtitle">Créez votre compte</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">Prénom</label>
                        <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="last_name">Nom</label>
                        <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Adresse email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" minlength="<?php echo PASSWORD_MIN_LENGTH; ?>" required>
                    <small style="color: var(--text-muted); font-size: 0.875rem; margin-top: 0.25rem; display: block;">
                        Minimum <?php echo PASSWORD_MIN_LENGTH; ?> caractères
                    </small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirmer le mot de passe</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <div class="checkbox-group">
                    <input type="checkbox" id="agree_terms" name="agree_terms" required>
                    <label for="agree_terms">
                        J'accepte les <a href="terms.php" target="_blank">conditions d'utilisation</a> et la <a href="privacy.php" target="_blank">politique de confidentialité</a>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-full">
                    Créer mon compte
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Déjà un compte ? <a href="login.php">Se connecter</a></p>
            </div>
        </div>
    </div>
    
    <script src="assets/js/main.js"></script>
</body>
</html>