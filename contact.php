<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$error = '';
$success = '';

// Traitement du formulaire de contact
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    
    // Validation
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'Veuillez remplir tous les champs.';
    } elseif (!validateEmail($email)) {
        $error = 'Veuillez entrer une adresse email valide.';
    } elseif (strlen($message) < 10) {
        $error = 'Votre message doit contenir au moins 10 caractères.';
    } else {
        try {
            // Envoyer l'email
            if (sendContactEmail($name, $email, $subject, $message)) {
                $success = 'Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.';
                
                // Réinitialiser le formulaire
                $name = $email = $subject = $message = '';
            } else {
                $error = 'Erreur lors de l\'envoi du message. Veuillez réessayer.';
            }
        } catch (Exception $e) {
            error_log("Erreur envoi contact: " . $e->getMessage());
            $error = 'Une erreur est survenue. Veuillez réessayer.';
        }
    }
}

// Fonction pour envoyer l'email de contact
function sendContactEmail($name, $email, $subject, $message) {
    try {
        $email_subject = "Nouveau message de contact - SMM Platform";
        
        $email_message = "
        <html>
        <head>
            <title>Nouveau message de contact</title>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h1 style='color: #ff7a00; text-align: center;'>Nouveau message de contact</h1>
                
                <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                    <h3 style='color: #333; margin-top: 0;'>Informations du contact</h3>
                    <p><strong>Nom :</strong> {$name}</p>
                    <p><strong>Email :</strong> {$email}</p>
                    <p><strong>Sujet :</strong> {$subject}</p>
                </div>
                
                <div style='background: #ffffff; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef;'>
                    <h3 style='color: #333; margin-top: 0;'>Message</h3>
                    <p style='white-space: pre-wrap;'>{$message}</p>
                </div>
                
                <hr style='border: none; border-top: 1px solid #eee; margin: 30px 0;'>
                
                <p style='font-size: 14px; color: #666;'>
                    Ce message a été envoyé depuis le formulaire de contact de SMM Platform.
                </p>
            </div>
        </body>
        </html>";
        
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . SMTP_FROM,
            'Reply-To: ' . $email,
            'X-Mailer: PHP/' . phpversion()
        ];
        
        if (defined('SMTP_BCC') && SMTP_BCC) {
            $headers[] = 'Bcc: ' . SMTP_BCC;
        }
        
        // Envoyer à l'admin
        $admin_sent = mail(SITE_EMAIL, $email_subject, $email_message, implode("\r\n", $headers));
        
        // Envoyer une confirmation au client
        $confirmation_subject = "Confirmation de votre message - SMM Platform";
        $confirmation_message = "
        <html>
        <head>
            <title>Confirmation de votre message</title>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h1 style='color: #ff7a00; text-align: center;'>Message reçu !</h1>
                
                <p>Bonjour {$name},</p>
                
                <p>Nous avons bien reçu votre message et nous vous en remercions.</p>
                
                <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                    <h3 style='color: #28a745; margin-top: 0;'>✅ Votre message</h3>
                    <p><strong>Sujet :</strong> {$subject}</p>
                    <p><strong>Contenu :</strong></p>
                    <p style='white-space: pre-wrap; background: white; padding: 15px; border-radius: 5px; border: 1px solid #e9ecef;'>{$message}</p>
                </div>
                
                <p>Notre équipe va examiner votre demande et vous répondre dans les plus brefs délais.</p>
                
                <p>En attendant, vous pouvez consulter notre <a href='" . SITE_URL . "/services.php'>catalogue de services</a> ou visiter notre <a href='" . SITE_URL . "/index.php'>page d'accueil</a>.</p>
                
                <p>Merci de votre confiance !</p>
                
                <hr style='border: none; border-top: 1px solid #eee; margin: 30px 0;'>
                
                <p style='font-size: 14px; color: #666;'>
                    Cet email est une confirmation automatique. Merci de ne pas y répondre.
                </p>
            </div>
        </body>
        </html>";
        
        $client_sent = mail($email, $confirmation_subject, $confirmation_message, implode("\r\n", $headers));
        
        return $admin_sent && $client_sent;
    } catch (Exception $e) {
        error_log("Erreur envoi email contact: " . $e->getMessage());
        return false;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - SMM Platform</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .contact-container {
            min-height: 100vh;
            background: var(--background-color);
            padding: 2rem 0;
        }
        
        .contact-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .contact-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }
        
        .contact-subtitle {
            color: var(--text-secondary);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .contact-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            align-items: start;
        }
        
        .contact-info {
            background: var(--card-color);
            border-radius: var(--radius-lg);
            padding: 2rem;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-lg);
        }
        
        .info-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .contact-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: var(--surface-color);
            border-radius: var(--radius-md);
            transition: var(--transition-fast);
        }
        
        .contact-item:hover {
            transform: translateX(5px);
            background: var(--card-color);
        }
        
        .contact-icon {
            font-size: 1.5rem;
            color: var(--primary-color);
            width: 40px;
            text-align: center;
        }
        
        .contact-details h4 {
            color: var(--text-primary);
            margin-bottom: 0.25rem;
            font-size: 1rem;
        }
        
        .contact-details p {
            color: var(--text-secondary);
            margin: 0;
            font-size: 0.9rem;
        }
        
        .contact-form {
            background: var(--card-color);
            border-radius: var(--radius-lg);
            padding: 2rem;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-lg);
        }
        
        .form-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
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
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 0.75rem;
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            color: var(--text-primary);
            font-size: 1rem;
            transition: var(--transition-fast);
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(255, 122, 0, 0.1);
        }
        
        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: var(--text-muted);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        
        .faq-section {
            background: var(--card-color);
            border-radius: var(--radius-lg);
            padding: 2rem;
            margin-top: 3rem;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-lg);
        }
        
        .faq-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .faq-item {
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1.5rem;
        }
        
        .faq-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        
        .faq-question {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition-fast);
        }
        
        .faq-question:hover {
            color: var(--primary-color);
        }
        
        .faq-answer {
            color: var(--text-secondary);
            line-height: 1.6;
            margin-left: 1.5rem;
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
        
        @media (max-width: 768px) {
            .contact-content {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .contact-header {
                margin-bottom: 2rem;
            }
            
            .contact-title {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>SMM Platform</h1>
                </div>
                <nav class="nav">
                    <ul class="nav-list">
                        <li><a href="index.php">Accueil</a></li>
                        <li><a href="services.php">Services</a></li>
                        <li><a href="contact.php" class="active">Contact</a></li>
                        <?php if (isLoggedIn()): ?>
                            <li><a href="dashboard.php">Mon compte</a></li>
                            <li><a href="logout.php">Déconnexion</a></li>
                        <?php else: ?>
                            <li><a href="login.php" class="btn btn-secondary">Connexion</a></li>
                            <li><a href="register.php" class="btn btn-primary">Inscription</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
                <button class="mobile-menu-toggle" aria-label="Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>
    </header>

    <div class="contact-container">
        <div class="container">
            <div class="contact-header">
                <h1 class="contact-title">📞 Contactez-nous</h1>
                <p class="contact-subtitle">
                    Une question ? Un projet ? N'hésitez pas à nous contacter. 
                    Notre équipe est là pour vous accompagner.
                </p>
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

            <div class="contact-content">
                <!-- Informations de contact -->
                <div class="contact-info">
                    <h2 class="info-title">
                        📋 Nos coordonnées
                    </h2>
                    
                    <div class="contact-item">
                        <div class="contact-icon">📧</div>
                        <div class="contact-details">
                            <h4>Email</h4>
                            <p>support@smmplatform.com</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">🌐</div>
                        <div class="contact-details">
                            <h4>Site web</h4>
                            <p>www.smmplatform.com</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">⏰</div>
                        <div class="contact-details">
                            <h4>Horaires de support</h4>
                            <p>24h/24 et 7j/7</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">💬</div>
                        <div class="contact-details">
                            <h4>Réponse garantie</h4>
                            <p>Sous 24h maximum</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">🌍</div>
                        <div class="contact-details">
                            <h4>Zone géographique</h4>
                            <p>Monde entier</p>
                        </div>
                    </div>
                    
                    <div class="contact-item">
                        <div class="contact-icon">🔒</div>
                        <div class="contact-details">
                            <h4>Sécurité</h4>
                            <p>Données protégées</p>
                        </div>
                    </div>
                </div>

                <!-- Formulaire de contact -->
                <div class="contact-form">
                    <h2 class="form-title">
                        ✉️ Envoyez-nous un message
                    </h2>
                    
                    <form method="POST" action="">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Prénom *</label>
                                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email *</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="subject">Sujet *</label>
                            <select id="subject" name="subject" required>
                                <option value="">Sélectionnez un sujet</option>
                                <option value="Question générale" <?php echo ($subject ?? '') === 'Question générale' ? 'selected' : ''; ?>>Question générale</option>
                                <option value="Demande de devis" <?php echo ($subject ?? '') === 'Demande de devis' ? 'selected' : ''; ?>>Demande de devis</option>
                                <option value="Support technique" <?php echo ($subject ?? '') === 'Support technique' ? 'selected' : ''; ?>>Support technique</option>
                                <option value="Partenariat" <?php echo ($subject ?? '') === 'Partenariat' ? 'selected' : ''; ?>>Partenariat</option>
                                <option value="Autre" <?php echo ($subject ?? '') === 'Autre' ? 'selected' : ''; ?>>Autre</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message *</label>
                            <textarea id="message" name="message" rows="6" placeholder="Décrivez votre demande en détail..." required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-full">
                            📤 Envoyer le message
                        </button>
                    </form>
                </div>
            </div>

            <!-- Section FAQ -->
            <div class="faq-section">
                <h2 class="faq-title">❓ Questions fréquentes</h2>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>🔍</span> Comment fonctionne le processus de commande ?
                    </div>
                    <div class="faq-answer">
                        Le processus est simple : sélectionnez un service, indiquez la quantité et le lien, 
                        passez la commande, uploadez votre preuve de paiement, et nous traitons votre demande.
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>⏱️</span> Combien de temps dure la livraison ?
                    </div>
                    <div class="faq-answer">
                        Les délais varient selon le service choisi. Ils sont indiqués sur chaque fiche de service 
                        et vont généralement de quelques heures à quelques jours.
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>💳</span> Quels moyens de paiement acceptez-vous ?
                    </div>
                    <div class="faq-answer">
                        Nous acceptons les virements bancaires, PayPal, et autres moyens de paiement sécurisés. 
                        Les détails vous seront communiqués lors de la commande.
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>🛡️</span> Mes données sont-elles sécurisées ?
                    </div>
                    <div class="faq-answer">
                        Absolument ! Nous utilisons des protocoles de sécurité avancés et ne partageons 
                        jamais vos informations personnelles avec des tiers.
                    </div>
                </div>
                
                <div class="faq-item">
                    <div class="faq-question">
                        <span>🔄</span> Que se passe-t-il si je ne suis pas satisfait ?
                    </div>
                    <div class="faq-answer">
                        Nous offrons une garantie de satisfaction. Si vous n'êtes pas satisfait du résultat, 
                        nous ajusterons le service ou vous rembourserons selon nos conditions.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script>
        // Animation des éléments au scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, observerOptions);
        
        // Observer les éléments à animer
        document.querySelectorAll('.contact-item, .faq-item').forEach(el => {
            observer.observe(el);
        });
        
        // Validation en temps réel
        document.querySelectorAll('input, textarea, select').forEach(field => {
            field.addEventListener('blur', function() {
                validateField(this);
            });
        });
        
        function validateField(field) {
            const value = field.value.trim();
            
            if (field.hasAttribute('required') && !value) {
                field.style.borderColor = 'var(--error-color)';
            } else if (field.type === 'email' && value && !isValidEmail(value)) {
                field.style.borderColor = 'var(--error-color)';
            } else {
                field.style.borderColor = 'var(--border-color)';
            }
        }
        
        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
    </script>
</body>
</html>