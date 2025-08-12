<?php
session_start();
require_once '../config.php';

// Vérification de l'authentification
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && isset($_POST['booking_id'])) {
        $booking_id = (int)$_POST['booking_id'];
        $action = $_POST['action'];
        
        try {
            if ($action === 'valider') {
                $stmt = $pdo->prepare("UPDATE bookings SET statut = 'validee' WHERE id = ?");
                $stmt->execute([$booking_id]);
                $message = "Réservation validée avec succès !";
                $messageType = "success";
            } elseif ($action === 'refuser') {
                $stmt = $pdo->prepare("UPDATE bookings SET statut = 'refusee' WHERE id = ?");
                $stmt->execute([$booking_id]);
                $message = "Réservation refusée.";
                $messageType = "warning";
            }
        } catch (Exception $e) {
            $message = "Erreur lors de la mise à jour : " . $e->getMessage();
            $messageType = "danger";
        }
    }
}

// Récupération des réservations avec informations des voitures
$stmt = $pdo->query("
    SELECT b.*, c.titre as voiture_titre, c.prix_jour
    FROM bookings b
    JOIN cars c ON b.voiture_id = c.id
    ORDER BY b.created_at DESC
");
$bookings = $stmt->fetchAll();

// Statistiques
$totalBookings = count($bookings);
$enAttente = count(array_filter($bookings, fn($b) => $b['statut'] === 'en_attente'));
$validees = count(array_filter($bookings, fn($b) => $b['statut'] === 'validee'));
$refusees = count(array_filter($bookings, fn($b) => $b['statut'] === 'refusee'));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administration - <?php echo SITE_NAME; ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="../css/admin.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation admin -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-shield-lock"></i> Administration
            </a>
            <div class="navbar-nav ms-auto">
                <span class="navbar-text me-3">
                    <i class="bi bi-person-circle"></i> Admin
                </span>
                <a class="nav-link" href="logout.php">
                    <i class="bi bi-box-arrow-right"></i> Déconnexion
                </a>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Messages -->
        <?php if (isset($message)): ?>
        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- En-tête -->
        <div class="row mb-4">
            <div class="col-12">
                <h1 class="h2 mb-3">
                    <i class="bi bi-dashboard"></i> Tableau de bord
                </h1>
                <p class="text-muted">Gestion des réservations et suivi des demandes</p>
            </div>
        </div>

        <!-- Statistiques -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0"><?php echo $totalBookings; ?></h4>
                                <small>Total réservations</small>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-calendar-check fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0"><?php echo $enAttente; ?></h4>
                                <small>En attente</small>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-clock fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0"><?php echo $validees; ?></h4>
                                <small>Validées</small>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-check-circle fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0"><?php echo $refusees; ?></h4>
                                <small>Refusées</small>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-x-circle fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des réservations -->
        <div class="card shadow">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="bi bi-list-ul"></i> Liste des réservations
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Voiture</th>
                                <th>Période</th>
                                <th>Montant</th>
                                <th>Opérateur</th>
                                <th>Statut</th>
                                <th>Preuve</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-secondary">#<?php echo $booking['id']; ?></span>
                                </td>
                                <td>
                                    <div>
                                        <strong><?php echo htmlspecialchars($booking['nom_client']); ?></strong><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($booking['email']); ?></small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong><?php echo htmlspecialchars($booking['voiture_titre']); ?></strong><br>
                                        <small class="text-muted"><?php echo number_format($booking['prix_jour'], 2); ?> €/jour</small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong><?php echo date('d/m/Y', strtotime($booking['date_debut'])); ?></strong><br>
                                        <small class="text-muted">au <?php echo date('d/m/Y', strtotime($booking['date_fin'])); ?></small>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold text-primary"><?php echo number_format($booking['montant'], 2); ?> €</span>
                                </td>
                                <td>
                                    <?php if ($booking['operateur'] === 'MTN'): ?>
                                        <span class="badge bg-warning text-dark">MTN</span>
                                    <?php else: ?>
                                        <span class="badge bg-info text-dark">Moov</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = '';
                                    $statusText = '';
                                    switch ($booking['statut']) {
                                        case 'en_attente':
                                            $statusClass = 'bg-warning text-dark';
                                            $statusText = 'En attente';
                                            break;
                                        case 'validee':
                                            $statusClass = 'bg-success';
                                            $statusText = 'Validée';
                                            break;
                                        case 'refusee':
                                            $statusClass = 'bg-danger';
                                            $statusText = 'Refusée';
                                            break;
                                    }
                                    ?>
                                    <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#preuveModal<?php echo $booking['id']; ?>">
                                        <i class="bi bi-eye"></i> Voir
                                    </button>
                                </td>
                                <td>
                                    <?php if ($booking['statut'] === 'en_attente'): ?>
                                    <div class="btn-group btn-group-sm">
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                            <input type="hidden" name="action" value="valider">
                                            <button type="submit" class="btn btn-success" 
                                                    onclick="return confirm('Valider cette réservation ?')">
                                                <i class="bi bi-check"></i>
                                            </button>
                                        </form>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                            <input type="hidden" name="action" value="refuser">
                                            <button type="submit" class="btn btn-danger" 
                                                    onclick="return confirm('Refuser cette réservation ?')">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </form>
                                    </div>
                                    <?php else: ?>
                                    <small class="text-muted">Traité</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            
                            <!-- Modal pour afficher la preuve de paiement -->
                            <div class="modal fade" id="preuveModal<?php echo $booking['id']; ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Preuve de paiement - <?php echo htmlspecialchars($booking['nom_client']); ?></h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <img src="../uploads/<?php echo htmlspecialchars($booking['fichier_preuve']); ?>" 
                                                 class="img-fluid" 
                                                 alt="Preuve de paiement"
                                                 onerror="this.src='../images/no-image.png'">
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                            <a href="../uploads/<?php echo htmlspecialchars($booking['fichier_preuve']); ?>" 
                                               class="btn btn-primary" target="_blank">
                                                <i class="bi bi-download"></i> Télécharger
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Lien retour -->
        <div class="text-center mt-4">
            <a href="../index.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Retour au site
            </a>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>