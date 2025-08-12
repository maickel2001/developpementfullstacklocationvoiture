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

// Traitement de la connexion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (empty($email) || empty($password)) {
        $error = 'Veuillez remplir tous les champs';
    } elseif (!validateEmail($email)) {
        $error = 'Veuillez entrer une adresse email valide';
    } else {
        try {
            $pdo = getDB();
            
            // Vérifier les tentatives de connexion
            $ip = $_SERVER['REMOTE_ADDR'];
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM login_attempts WHERE ip_address = ? AND attempt_time > DATE_SUB(NOW(), INTERVAL ? SECOND)");
            $stmt->execute([$ip, LOGIN_LOCKOUT_TIME]);
            $attempts = $stmt->fetchColumn();
            
            if ($attempts >= LOGIN_MAX_ATTEMPTS) {
                $error = 'Trop de tentatives de connexion. Veuillez réessayer dans ' . (LOGIN_LOCKOUT_TIME / 60) . ' minutes.';
            } else {
                // Rechercher l'utilisateur
                $stmt = $pdo->prepare("SELECT id, email, password, role, active FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
                
                if ($user && password_verify($password, $user['password'])) {
                    if ($user['active']) {
                        // Connexion réussie
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_email'] = $user['email'];
                        $_SESSION['user_role'] = $user['role'];
                        $_SESSION['login_time'] = time();
                        
                        // Supprimer les tentatives échouées
                        $stmt = $pdo->prepare("DELETE FROM login_attempts WHERE ip_address = ?");
                        $stmt->execute([$ip]);
                        
                        // Rediriger selon le rôle
                        if ($user['role'] === 'admin') {
                            header('Location: admin/dashboard.php');
                        } else {
                            header('Location: dashboard.php');
                        }
                        exit;
                    } else {
                        $error = 'Votre compte a été désactivé. Contactez le support.';
                    }
                } else {
                    // Enregistrer la tentative échouée
                    $stmt = $pdo->prepare("INSERT INTO login_attempts (ip_address, email, attempt_time) VALUES (?, ?, NOW())");
                    $stmt->execute([$ip, $email]);
                    
                    $error = 'Email ou mot de passe incorrect';
                }
            }
        } catch (Exception $e) {
            error_log("Erreur de connexion: " . $e->getMessage());
            $error = 'Une erreur est survenue. Veuillez réessayer.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - SMM Platform</title>
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
            max-width: 400px;
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
        
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: auto;
            margin: 0;
        }
        
        .forgot-password {
            color: var(--primary-color);
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .forgot-password:hover {
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
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-logo">SMM Platform</div>
                <p class="auth-subtitle">Connectez-vous à votre compte</p>
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
                <div class="form-group">
                    <label for="email">Adresse email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-options">
                    <div class="checkbox-group">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Se souvenir de moi</label>
                    </div>
                    <a href="forgot-password.php" class="forgot-password">Mot de passe oublié ?</a>
                </div>
                
                <button type="submit" class="btn btn-primary btn-full">
                    Se connecter
                </button>
            </form>
            
            <div class="auth-footer">
                <p>Pas encore de compte ? <a href="register.php">S'inscrire</a></p>
            </div>
        </div>
    </div>
    
    <script src="assets/js/main.js"></script>
</body>
</html>