<?php
require_once 'config.php';

$message = '';
$messageType = '';
$selectedCar = null;

// Récupération de la voiture sélectionnée si ID fourni
if (isset($_GET['car_id']) && is_numeric($_GET['car_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ? AND disponible = 1");
    $stmt->execute([$_GET['car_id']]);
    $selectedCar = $stmt->fetch();
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = cleanInput($_POST['nom']);
    $email = cleanInput($_POST['email']);
    $voiture_id = (int)$_POST['voiture_id'];
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'];
    $operateur = $_POST['operateur'];
    
    // Validation des données
    $errors = [];
    
    if (empty($nom)) $errors[] = "Le nom est requis";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email invalide";
    if (empty($voiture_id)) $errors[] = "Veuillez sélectionner une voiture";
    if (empty($date_debut)) $errors[] = "Date de début requise";
    if (empty($date_fin)) $errors[] = "Date de fin requise";
    if (empty($operateur)) $errors[] = "Veuillez sélectionner un opérateur";
    
    // Validation des dates
    if (!validateDates($date_debut, $date_fin)) {
        $errors[] = "Les dates sélectionnées sont invalides";
    }
    
    // Validation du fichier
    if (!isset($_FILES['preuve_paiement']) || $_FILES['preuve_paiement']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = "La preuve de paiement est requise";
    } elseif (!validateFile($_FILES['preuve_paiement'])) {
        $errors[] = "Le fichier de preuve de paiement est invalide";
    }
    
    if (empty($errors)) {
        try {
            // Calcul du montant
            $stmt = $pdo->prepare("SELECT prix_jour FROM cars WHERE id = ?");
            $stmt->execute([$voiture_id]);
            $car = $stmt->fetch();
            
            $debut = new DateTime($date_debut);
            $fin = new DateTime($date_fin);
            $interval = $debut->diff($fin);
            $nombre_jours = $interval->days + 1;
            $montant = $car['prix_jour'] * $nombre_jours;
            
            // Traitement du fichier uploadé
            $file = $_FILES['preuve_paiement'];
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $filename = uniqid() . '_' . time() . '.' . $extension;
            $upload_path = UPLOAD_DIR . $filename;
            
            if (!is_dir(UPLOAD_DIR)) {
                mkdir(UPLOAD_DIR, 0755, true);
            }
            
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                // Insertion en base
                $stmt = $pdo->prepare("
                    INSERT INTO bookings (nom_client, email, voiture_id, date_debut, date_fin, montant, operateur, fichier_preuve, statut)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'en_attente')
                ");
                
                $stmt->execute([
                    $nom, $email, $voiture_id, $date_debut, $date_fin, $montant, $operateur, $filename
                ]);
                
                $message = "Réservation effectuée avec succès ! Votre demande est en cours de validation.";
                $messageType = "success";
                
                // Réinitialisation du formulaire
                $_POST = [];
                $selectedCar = null;
                
            } else {
                $errors[] = "Erreur lors de l'upload du fichier";
            }
            
        } catch (Exception $e) {
            $errors[] = "Erreur lors de la réservation : " . $e->getMessage();
        }
    }
    
    if (!empty($errors)) {
        $message = implode("<br>", $errors);
        $messageType = "danger";
    }
}

// Récupération de toutes les voitures disponibles
$stmt = $pdo->query("SELECT * FROM cars WHERE disponible = 1 ORDER BY titre");
$cars = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réserver une voiture - <?php echo SITE_NAME; ?></title>
    
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
                        <a class="nav-link active" href="reservation.php">Réserver</a>
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
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">
                            <i class="bi bi-calendar-check"></i> Réserver une voiture
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($message): ?>
                        <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                            <?php echo $message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST" enctype="multipart/form-data" id="reservationForm">
                            <!-- Informations personnelles -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">
                                        <i class="bi bi-person"></i> Informations personnelles
                                    </h5>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="nom" class="form-label">Nom complet *</label>
                                    <input type="text" class="form-control" id="nom" name="nom" 
                                           value="<?php echo isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : ''; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email *</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                                </div>
                            </div>
                            
                            <!-- Sélection de la voiture -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">
                                        <i class="bi bi-car-front"></i> Sélection de la voiture
                                    </h5>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="voiture_id" class="form-label">Choisir une voiture *</label>
                                    <select class="form-select" id="voiture_id" name="voiture_id" required>
                                        <option value="">Sélectionnez une voiture</option>
                                        <?php foreach ($cars as $car): ?>
                                        <option value="<?php echo $car['id']; ?>" 
                                                data-prix="<?php echo $car['prix_jour']; ?>"
                                                <?php echo (isset($_POST['voiture_id']) && $_POST['voiture_id'] == $car['id']) || 
                                                          ($selectedCar && $selectedCar['id'] == $car['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($car['titre']); ?> - <?php echo number_format($car['prix_jour'], 2); ?> €/jour
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Dates de location -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">
                                        <i class="bi bi-calendar"></i> Période de location
                                    </h5>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="date_debut" class="form-label">Date de début *</label>
                                    <input type="date" class="form-control" id="date_debut" name="date_debut" 
                                           value="<?php echo isset($_POST['date_debut']) ? $_POST['date_debut'] : ''; ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="date_fin" class="form-label">Date de fin *</label>
                                    <input type="date" class="form-control" id="date_fin" name="date_fin" 
                                           value="<?php echo isset($_POST['date_fin']) ? $_POST['date_fin'] : ''; ?>" required>
                                </div>
                            </div>
                            
                            <!-- Opérateur de paiement -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">
                                        <i class="bi bi-phone"></i> Paiement mobile money
                                    </h5>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Opérateur *</label>
                                    <div class="d-flex gap-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="operateur" id="mtn" value="MTN" 
                                                   <?php echo (isset($_POST['operateur']) && $_POST['operateur'] == 'MTN') ? 'checked' : ''; ?> required>
                                            <label class="form-check-label" for="mtn">
                                                <span class="badge bg-warning text-dark">MTN Mobile Money</span>
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="operateur" id="moov" value="Moov" 
                                                   <?php echo (isset($_POST['operateur']) && $_POST['operateur'] == 'Moov') ? 'checked' : ''; ?> required>
                                            <label class="form-check-label" for="moov">
                                                <span class="badge bg-info text-dark">Moov Money</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Preuve de paiement -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3">
                                        <i class="bi bi-upload"></i> Preuve de paiement
                                    </h5>
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="preuve_paiement" class="form-label">Capture d'écran du paiement *</label>
                                    <input type="file" class="form-control" id="preuve_paiement" name="preuve_paiement" 
                                           accept=".jpg,.jpeg,.png" required>
                                    <div class="form-text">
                                        Formats acceptés : JPG, JPEG, PNG. Taille maximale : 5 Mo.
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Résumé et montant -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        <h6 class="mb-2"><i class="bi bi-info-circle"></i> Résumé de votre réservation</h6>
                                        <div id="resumeReservation">
                                            <p class="mb-1">Sélectionnez une voiture et des dates pour voir le montant</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Boutons -->
                            <div class="row">
                                <div class="col-12 d-flex gap-3">
                                    <a href="index.php" class="btn btn-outline-secondary">
                                        <i class="bi bi-arrow-left"></i> Retour
                                    </a>
                                    <button type="submit" class="btn btn-primary flex-fill">
                                        <i class="bi bi-check-circle"></i> Confirmer la réservation
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const voitureSelect = document.getElementById('voiture_id');
            const dateDebut = document.getElementById('date_debut');
            const dateFin = document.getElementById('date_fin');
            const resumeDiv = document.getElementById('resumeReservation');
            
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
                updateResume();
            });
            
            dateFin.addEventListener('change', updateResume);
            voitureSelect.addEventListener('change', updateResume);
            
            function updateResume() {
                const voiture = voitureSelect.options[voitureSelect.selectedIndex];
                const prix = voiture.dataset.prix;
                const debut = dateDebut.value;
                const fin = dateFin.value;
                
                if (prix && debut && fin) {
                    const debutDate = new Date(debut);
                    const finDate = new Date(fin);
                    const diffTime = Math.abs(finDate - debutDate);
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
                    const montant = (parseFloat(prix) * diffDays).toFixed(2);
                    
                    resumeDiv.innerHTML = `
                        <p class="mb-1"><strong>Voiture :</strong> ${voiture.text}</p>
                        <p class="mb-1"><strong>Période :</strong> Du ${debut} au ${fin} (${diffDays} jour(s))</p>
                        <p class="mb-0"><strong>Montant total :</strong> <span class="text-primary fw-bold">${montant} €</span></p>
                    `;
                } else {
                    resumeDiv.innerHTML = '<p class="mb-1">Sélectionnez une voiture et des dates pour voir le montant</p>';
                }
            }
            
            // Initialisation
            updateResume();
        });
    </script>
</body>
</html>