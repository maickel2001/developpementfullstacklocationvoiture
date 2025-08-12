<?php
require_once 'config.php';

$results = [];
$searchPerformed = false;
$searchParams = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET)) {
    $searchPerformed = true;
    
    // Récupération des paramètres de recherche
    $ville = isset($_GET['ville']) ? cleanInput($_GET['ville']) : '';
    $date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : '';
    $date_fin = isset($_GET['date_fin']) ? $_GET['date_fin'] : '';
    $type_voiture = isset($_GET['type_voiture']) ? cleanInput($_GET['type_voiture']) : '';
    
    $searchParams = [
        'ville' => $ville,
        'date_debut' => $date_debut,
        'date_fin' => $date_fin,
        'type_voiture' => $type_voiture
    ];
    
    // Construction de la requête SQL
    $sql = "SELECT * FROM cars WHERE disponible = 1";
    $params = [];
    
    if (!empty($ville)) {
        $sql .= " AND (titre LIKE ? OR description LIKE ?)";
        $params[] = "%$ville%";
        $params[] = "%$ville%";
    }
    
    if (!empty($type_voiture)) {
        $sql .= " AND (titre LIKE ? OR description LIKE ?)";
        $params[] = "%$type_voiture%";
        $params[] = "%$type_voiture%";
    }
    
    $sql .= " ORDER BY prix_jour ASC";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll();
    } catch (Exception $e) {
        $error = "Erreur lors de la recherche : " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultats de recherche - <?php echo SITE_NAME; ?></title>
    
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
                        <a class="nav-link" href="index.php">Accueil</a>
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

    <!-- Contenu principal -->
    <div class="container mt-5 pt-5">
        <!-- En-tête de recherche -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h2 mb-2">
                            <i class="bi bi-search"></i> Résultats de recherche
                        </h1>
                        <?php if ($searchPerformed): ?>
                        <p class="text-muted mb-0">
                            <?php echo count($results); ?> voiture(s) trouvée(s)
                            <?php if (!empty($searchParams['ville'])): ?>
                            pour "<?php echo htmlspecialchars($searchParams['ville']); ?>"
                            <?php endif; ?>
                        </p>
                        <?php endif; ?>
                    </div>
                    <a href="index.php" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left"></i> Nouvelle recherche
                    </a>
                </div>
            </div>
        </div>

        <!-- Paramètres de recherche utilisés -->
        <?php if ($searchPerformed && !empty(array_filter($searchParams))): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="card bg-light border-0">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="bi bi-funnel"></i> Critères de recherche
                        </h6>
                        <div class="row">
                            <?php if (!empty($searchParams['ville'])): ?>
                            <div class="col-md-3 mb-2">
                                <strong>Ville :</strong> <?php echo htmlspecialchars($searchParams['ville']); ?>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($searchParams['date_debut'])): ?>
                            <div class="col-md-3 mb-2">
                                <strong>Date début :</strong> <?php echo date('d/m/Y', strtotime($searchParams['date_debut'])); ?>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($searchParams['date_fin'])): ?>
                            <div class="col-md-3 mb-2">
                                <strong>Date fin :</strong> <?php echo date('d/m/Y', strtotime($searchParams['date_fin'])); ?>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($searchParams['type_voiture'])): ?>
                            <div class="col-md-3 mb-2">
                                <strong>Type :</strong> <?php echo htmlspecialchars($searchParams['type_voiture']); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Résultats de recherche -->
        <?php if ($searchPerformed): ?>
            <?php if (empty($results)): ?>
            <div class="row">
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-search display-1 text-muted"></i>
                        <h3 class="mt-3">Aucun résultat trouvé</h3>
                        <p class="text-muted">
                            Aucune voiture ne correspond à vos critères de recherche.
                            <br>Essayez de modifier vos paramètres ou consultez notre catalogue complet.
                        </p>
                        <a href="index.php" class="btn btn-primary">
                            <i class="bi bi-house"></i> Voir toutes les voitures
                        </a>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="row g-4">
                <?php foreach ($results as $car): ?>
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
            <?php endif; ?>
        <?php else: ?>
        <!-- Page d'accueil de recherche -->
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="bi bi-search display-1 text-primary"></i>
                    <h3 class="mt-3">Recherche de voitures</h3>
                    <p class="text-muted">
                        Utilisez le formulaire de recherche sur la page d'accueil pour trouver votre voiture idéale.
                    </p>
                    <a href="index.php" class="btn btn-primary">
                        <i class="bi bi-house"></i> Retour à l'accueil
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5 mt-5">
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
</body>
</html>