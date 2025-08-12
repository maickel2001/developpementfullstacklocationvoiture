<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Vérifier le mode maintenance
if (isMaintenanceMode()) {
    header('Location: maintenance.php');
    exit;
}

// Récupérer les services populaires
$popularServices = getPopularServices();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMM Platform - Services de Marketing sur les Réseaux Sociaux</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
                        <li><a href="#services">Services</a></li>
                        <li><a href="#about">À propos</a></li>
                        <li><a href="#contact">Contact</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
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

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h2 class="hero-title">Boostez votre présence sur les réseaux sociaux</h2>
                <p class="hero-subtitle">Services professionnels de marketing sur les réseaux sociaux pour augmenter vos followers, likes et engagement</p>
                <div class="hero-actions">
                    <a href="#services" class="btn btn-primary btn-large">Voir nos services</a>
                    <a href="#about" class="btn btn-outline btn-large">En savoir plus</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Reassurance Banner -->
    <div class="reassurance-banner">
        <div class="container">
            <p>✅ Satisfait ou remboursé sous 7 jours selon conditions</p>
        </div>
    </div>

    <!-- Services Section -->
    <section id="services" class="services">
        <div class="container">
            <h2 class="section-title">Nos Services</h2>
            <div class="services-grid">
                <?php foreach ($popularServices as $service): ?>
                <div class="service-card">
                    <div class="service-icon">
                        <span class="icon">📈</span>
                    </div>
                    <h3 class="service-title"><?php echo htmlspecialchars($service['name']); ?></h3>
                    <p class="service-description"><?php echo htmlspecialchars($service['description']); ?></p>
                    <div class="service-price">
                        <span class="price">À partir de <?php echo number_format($service['price'], 2); ?>€</span>
                        <span class="per">/1000</span>
                    </div>
                    <a href="order.php?service=<?php echo $service['id']; ?>" class="btn btn-primary btn-full">Commander</a>
                </div>
                <?php endforeach; ?>
            </div>
            <div class="services-cta">
                <a href="services.php" class="btn btn-outline btn-large">Voir tous nos services</a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about">
        <div class="container">
            <div class="about-content">
                <div class="about-text">
                    <h2 class="section-title">Pourquoi choisir SMM Platform ?</h2>
                    <ul class="features-list">
                        <li>✅ Services de qualité premium</li>
                        <li>✅ Livraison rapide et sécurisée</li>
                        <li>✅ Support client 24/7</li>
                        <li>✅ Paiement sécurisé</li>
                        <li>✅ Satisfaction garantie</li>
                    </ul>
                </div>
                <div class="about-image">
                    <div class="image-placeholder">
                        <span class="icon">🚀</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact">
        <div class="container">
            <h2 class="section-title">Contactez-nous</h2>
            <div class="contact-content">
                <div class="contact-info">
                    <div class="contact-item">
                        <span class="icon">📧</span>
                        <div>
                            <h4>Email</h4>
                            <p>support@smmplatform.com</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <span class="icon">💬</span>
                        <div>
                            <h4>Support</h4>
                            <p>Disponible 24/7</p>
                        </div>
                    </div>
                </div>
                <div class="contact-form">
                    <form action="contact.php" method="POST">
                        <div class="form-group">
                            <input type="text" name="name" placeholder="Votre nom" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" placeholder="Votre email" required>
                        </div>
                        <div class="form-group">
                            <textarea name="message" placeholder="Votre message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-full">Envoyer</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>SMM Platform</h3>
                    <p>Votre partenaire de confiance pour le marketing sur les réseaux sociaux</p>
                </div>
                <div class="footer-section">
                    <h4>Liens rapides</h4>
                    <ul>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#about">À propos</a></li>
                        <li><a href="#contact">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Support</h4>
                    <ul>
                        <li><a href="terms.php">Conditions d'utilisation</a></li>
                        <li><a href="privacy.php">Politique de confidentialité</a></li>
                        <li><a href="refund.php">Politique de remboursement</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> SMM Platform. Tous droits réservés.</p>
            </div>
        </div>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>