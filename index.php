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
    <title><?php echo SITE_NAME; ?> - Location de voitures</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-car-front"></i> <?php echo SITE_NAME; ?>
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
                        <a class="nav-link" href="reservation.php">Réserver</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin/">Admin</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section avec moteur de recherche -->
    <section class="hero-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h1 class="display-4 fw-bold text-white mb-4">
                        Trouvez votre voiture de location idéale
                    </h1>
                    <p class="lead text-white mb-5">
                        Large gamme de véhicules à des prix compétitifs pour tous vos déplacements
                    </p>
                    
                    <!-- Formulaire de recherche -->
                    <div class="search-form bg-white p-4 rounded-3 shadow">
                        <form action="recherche.php" method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label for="ville" class="form-label">Ville</label>
                                <input type="text" class="form-control" id="ville" name="ville" placeholder="Votre ville" required>
                            </div>
                            <div class="col-md-3">
                                <label for="date_debut" class="form-label">Date début</label>
                                <input type="date" class="form-control" id="date_debut" name="date_debut" required>
                            </div>
                            <div class="col-md-3">
                                <label for="date_fin" class="form-label">Date fin</label>
                                <input type="date" class="form-control" id="date_fin" name="date_fin" required>
                            </div>
                            <div class="col-md-3">
                                <label for="type_voiture" class="form-label">Type</label>
                                <select class="form-select" id="type_voiture" name="type_voiture">
                                    <option value="">Tous types</option>
                                    <option value="berline">Berline</option>
                                    <option value="suv">SUV</option>
                                    <option value="compacte">Compacte</option>
                                    <option value="luxe">Luxe</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg px-5">
                                    <i class="bi bi-search"></i> Rechercher
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section des voitures -->
    <section class="py-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h2 class="display-6 fw-bold">Nos véhicules disponibles</h2>
                    <p class="lead text-muted">Choisissez parmi notre sélection de voitures de qualité</p>
                </div>
            </div>
            
            <div class="row g-4">
                <?php foreach ($cars as $car): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-img-top-container">
                            <img src="images/<?php echo htmlspecialchars($car['image']); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($car['titre']); ?>"
                                 onerror="this.src='images/default-car.jpg'">
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold"><?php echo htmlspecialchars($car['titre']); ?></h5>
                            <p class="card-text text-muted"><?php echo htmlspecialchars($car['description']); ?></p>
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="h4 text-primary mb-0"><?php echo number_format($car['prix_jour'], 2); ?> €</span>
                                    <small class="text-muted">/jour</small>
                                </div>
                                <a href="reservation.php?car_id=<?php echo $car['id']; ?>" 
                                   class="btn btn-primary w-100">
                                    <i class="bi bi-calendar-check"></i> Réserver maintenant
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Section avantages -->
    <section class="bg-light py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4 text-center">
                    <div class="feature-icon bg-primary bg-gradient text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                        <i class="bi bi-shield-check fs-1"></i>
                    </div>
                    <h4>Assurance incluse</h4>
                    <p class="text-muted">Toutes nos locations incluent une assurance complète pour votre tranquillité d'esprit.</p>
                </div>
                <div class="col-md-4 text-center">
                    <div class="feature-icon bg-primary bg-gradient text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                        <i class="bi bi-clock fs-1"></i>
                    </div>
                    <h4>Service 24/7</h4>
                    <p class="text-muted">Notre équipe est disponible 24h/24 et 7j/7 pour vous assister.</p>
                </div>
                <div class="col-md-4 text-center">
                    <div class="feature-icon bg-primary bg-gradient text-white rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center">
                        <i class="bi bi-star fs-1"></i>
                    </div>
                    <h4>Qualité garantie</h4>
                    <p class="text-muted">Tous nos véhicules sont régulièrement entretenus et contrôlés.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <h5 class="mb-3"><?php echo SITE_NAME; ?></h5>
                    <p class="text-muted">Votre partenaire de confiance pour la location de véhicules. Qualité, sécurité et service client au rendez-vous.</p>
                </div>
                <div class="col-lg-4">
                    <h5 class="mb-3">Contact</h5>
                    <ul class="list-unstyled text-muted">
                        <li><i class="bi bi-geo-alt"></i> 123 Rue de la Location, 75001 Paris</li>
                        <li><i class="bi bi-telephone"></i> +33 1 23 45 67 89</li>
                        <li><i class="bi bi-envelope"></i> contact@location-voitures.fr</li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h5 class="mb-3">Horaires</h5>
                    <ul class="list-unstyled text-muted">
                        <li>Lundi - Vendredi : 8h - 20h</li>
                        <li>Samedi : 9h - 18h</li>
                        <li>Dimanche : 10h - 16h</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="row">
                <div class="col-md-6">
                    <p class="text-muted mb-0">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Tous droits réservés.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <a href="#" class="text-muted text-decoration-none me-3">Mentions légales</a>
                    <a href="#" class="text-muted text-decoration-none me-3">CGV</a>
                    <a href="#" class="text-muted text-decoration-none">Politique de confidentialité</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
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
    </script>
</body>
</html>