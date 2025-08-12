<?php
require_once 'config.php';

// Récupération des voitures disponibles
$stmt = $pdo->query("SELECT * FROM cars WHERE disponible = 1 ORDER BY prix_jour ASC");
$cars = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Location de voitures premium</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar" id="navbar">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="bi bi-car-front"></i>
                <?php echo SITE_NAME; ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#vehicles">Véhicules</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="reservation.php">Réserver</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin/">Admin</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <!-- Vidéo d'arrière-plan -->
        <video class="hero-video" autoplay muted loop playsinline>
            <source src="videos/car-driving.mp4" type="video/mp4">
            <source src="videos/car-driving.webm" type="video/webm">
        </video>
        
        <!-- Overlay -->
        <div class="hero-overlay"></div>
        
        <!-- Contenu principal -->
        <div class="hero-content">
            <h1 class="hero-title">
                Location de Voitures Premium
            </h1>
            
            <p class="hero-subtitle">
                Découvrez notre collection exclusive de véhicules haut de gamme pour tous vos déplacements professionnels et personnels
            </p>
            
            <!-- Formulaire de recherche -->
            <div class="search-form">
                <form action="recherche.php" method="GET">
                    <div class="row g-4">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="ville" class="form-label">
                                    <i class="bi bi-geo-alt"></i> Destination
                                </label>
                                <input type="text" class="form-control" id="ville" name="ville" 
                                       placeholder="Où allez-vous ?" required>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_debut" class="form-label">
                                    <i class="bi bi-calendar-event"></i> Date début
                                </label>
                                <input type="date" class="form-control" id="date_debut" name="date_debut" required>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_fin" class="form-label">
                                    <i class="bi bi-calendar-check"></i> Date fin
                                </label>
                                <input type="date" class="form-control" id="date_fin" name="date_fin" required>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="type_voiture" class="form-label">
                                    <i class="bi bi-car-front"></i> Type
                                </label>
                                <select class="form-select" id="type_voiture" name="type_voiture">
                                    <option value="">Tous types</option>
                                    <option value="berline">Berline</option>
                                    <option value="suv">SUV</option>
                                    <option value="luxe">Luxe</option>
                                    <option value="sport">Sport</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-search">
                            <i class="bi bi-search"></i> Trouver ma voiture
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Section des voitures -->
    <section class="section cars-section" id="vehicles">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Notre Collection Premium</h2>
                <p class="section-subtitle">
                    Des véhicules d'exception sélectionnés pour leur confort, leur sécurité et leur performance
                </p>
            </div>
            
            <div class="row g-4">
                <?php foreach ($cars as $index => $car): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="car-card">
                        <div class="car-image-container">
                            <img src="images/<?php echo htmlspecialchars($car['image']); ?>" 
                                 class="car-image" 
                                 alt="<?php echo htmlspecialchars($car['titre']); ?>"
                                 onerror="this.src='images/default-car.jpg'">
                            
                            <div class="price-badge">
                                <?php echo number_format($car['prix_jour'], 0); ?> €/jour
                            </div>
                        </div>
                        
                        <div class="car-content">
                            <h5 class="car-title"><?php echo htmlspecialchars($car['titre']); ?></h5>
                            <p class="car-description"><?php echo htmlspecialchars($car['description']); ?></p>
                            
                            <div class="car-features">
                                <span class="feature-tag">
                                    <i class="bi bi-speedometer2"></i> Performant
                                </span>
                                <span class="feature-tag">
                                    <i class="bi bi-shield-check"></i> Sécurisé
                                </span>
                                <span class="feature-tag">
                                    <i class="bi bi-star"></i> Premium
                                </span>
                            </div>
                            
                            <a href="reservation.php?car_id=<?php echo $car['id']; ?>" 
                               class="btn btn-reserve">
                                <i class="bi bi-calendar-check"></i> Réserver maintenant
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Section des avantages -->
    <section class="section features-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">Pourquoi nous choisir ?</h2>
                <p class="section-subtitle">
                    Une expérience de location exceptionnelle avec des services premium
                </p>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h4 class="feature-title">Assurance Premium</h4>
                        <p class="feature-description">
                            Toutes nos locations incluent une assurance complète et des garanties étendues 
                            pour votre tranquillité d'esprit.
                        </p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-clock"></i>
                        </div>
                        <h4 class="feature-title">Service 24/7</h4>
                        <p class="feature-description">
                            Notre équipe d'experts est disponible 24h/24 et 7j/7 pour vous assister 
                            et répondre à tous vos besoins.
                        </p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="bi bi-star"></i>
                        </div>
                        <h4 class="feature-title">Qualité Exceptionnelle</h4>
                        <p class="feature-description">
                            Tous nos véhicules sont rigoureusement entretenus et contrôlés 
                            pour garantir une expérience de conduite parfaite.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section statistiques -->
    <section class="section stats-section">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number">500+</div>
                        <div class="stat-label">Clients satisfaits</div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number">50+</div>
                        <div class="stat-label">Véhicules disponibles</div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number">99%</div>
                        <div class="stat-label">Taux de satisfaction</div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="stat-card">
                        <div class="stat-number">24h</div>
                        <div class="stat-label">Support disponible</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer" id="contact">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h5><?php echo SITE_NAME; ?></h5>
                    <p>
                        Votre partenaire de confiance pour la location de véhicules premium. 
                        Qualité, sécurité et service client au rendez-vous.
                    </p>
                    
                    <div class="social-links">
                        <a href="#" class="social-link"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="social-link"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="social-link"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="social-link"><i class="bi bi-linkedin"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <h5>Contact</h5>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-geo-alt"></i> 123 Avenue des Champs-Élysées, 75008 Paris</li>
                        <li><i class="bi bi-telephone"></i> +33 1 23 45 67 89</li>
                        <li><i class="bi bi-envelope"></i> contact@location-voitures.fr</li>
                        <li><i class="bi bi-clock"></i> 24h/24 - 7j/7</li>
                    </ul>
                </div>
                
                <div class="col-lg-4">
                    <h5>Services</h5>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-check-circle"></i> Location courte durée</li>
                        <li><i class="bi bi-check-circle"></i> Location longue durée</li>
                        <li><i class="bi bi-check-circle"></i> Service avec chauffeur</li>
                        <li><i class="bi bi-check-circle"></i> Livraison à domicile</li>
                    </ul>
                </div>
            </div>
            
            <hr class="my-4" style="border-color: var(--secondary);">
            
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Tous droits réservés.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="me-3">Mentions légales</a>
                    <a href="#" class="me-3">CGV</a>
                    <a href="#">Politique de confidentialité</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Navigation scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Validation des dates
        document.addEventListener('DOMContentLoaded', function() {
            const dateDebut = document.getElementById('date_debut');
            const dateFin = document.getElementById('date_fin');
            
            // Date minimum = aujourd'hui
            const today = new Date().toISOString().split('T')[0];
            dateDebut.min = today;
            dateFin.min = today;
            
            // Validation date fin > date début
            dateDebut.addEventListener('change', function() {
                dateFin.min = this.value;
                if (dateFin.value && dateFin.value <= this.value) {
                    dateFin.value = '';
                }
            });
        });

        // Mobile menu toggle
        const navbarToggler = document.querySelector('.navbar-toggler');
        const navbarNav = document.querySelector('.navbar-nav');
        
        navbarToggler.addEventListener('click', function() {
            navbarNav.classList.toggle('show');
        });

        // Smooth scroll pour les liens d'ancrage
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Animation des cartes au scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observer les cartes de voitures
        document.querySelectorAll('.car-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });

        // Observer les cartes de fonctionnalités
        document.querySelectorAll('.feature-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });
    </script>
</body>
</html>