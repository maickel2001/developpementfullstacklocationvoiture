<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Vérifier si l'utilisateur est connecté
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';
$order = null;
$order_id = $_GET['order_id'] ?? '';

if (empty($order_id)) {
    header('Location: dashboard.php');
    exit;
}

try {
    $pdo = getDB();
    
    // Récupérer les détails de la commande
    $stmt = $pdo->prepare("
        SELECT o.*, s.name as service_name, c.name as category_name, u.email as user_email
        FROM orders o
        JOIN services s ON o.service_id = s.id
        JOIN categories c ON s.category_id = c.id
        JOIN users u ON o.user_id = u.id
        WHERE o.id = ? AND o.user_id = ?
    ");
    $stmt->execute([$order_id, $_SESSION['user_id']]);
    $order = $stmt->fetch();
    
    if (!$order) {
        header('Location: dashboard.php');
        exit;
    }
    
    if ($order['status'] !== 'pending') {
        header('Location: dashboard.php');
        exit;
    }
    
} catch (Exception $e) {
    error_log("Erreur chargement commande: " . $e->getMessage());
    $error = 'Erreur lors du chargement de la commande.';
}

// Traitement de l'upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['payment_proof'];
        $file_name = $file['name'];
        $file_size = $file['size'];
        $file_tmp = $file['tmp_name'];
        $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Validation du fichier
        $allowed_types = ['jpg', 'jpeg', 'png', 'pdf'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file_type, $allowed_types)) {
            $error = 'Type de fichier non autorisé. Utilisez JPG, PNG ou PDF.';
        } elseif ($file_size > $max_size) {
            $error = 'Fichier trop volumineux. Taille maximale : 5MB.';
        } else {
            try {
                // Créer le dossier d'upload s'il n'existe pas
                $upload_dir = 'uploads/proofs/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                // Générer un nom de fichier unique
                $new_filename = 'proof_' . $order_id . '_' . time() . '.' . $file_type;
                $upload_path = $upload_dir . $new_filename;
                
                // Déplacer le fichier
                if (move_uploaded_file($file_tmp, $upload_path)) {
                    // Mettre à jour la commande
                    $stmt = $pdo->prepare("
                        UPDATE orders 
                        SET payment_proof_path = ?, 
                            payment_proof_original_name = ?, 
                            payment_proof_size = ?, 
                            payment_proof_type = ?,
                            status = 'processing',
                            updated_at = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([$upload_path, $file_name, $file_size, $file_type, $order_id]);
                    
                    // Envoyer email de confirmation
                    sendPaymentProofEmail($order['user_email'], $order['order_number']);
                    
                    $success = 'Preuve de paiement reçue avec succès ! Votre commande est maintenant en cours de traitement.';
                    
                    // Rediriger vers le tableau de bord après 3 secondes
                    header("refresh:3;url=dashboard.php");
                } else {
                    $error = 'Erreur lors de l\'upload du fichier.';
                }
            } catch (Exception $e) {
                error_log("Erreur upload: " . $e->getMessage());
                $error = 'Erreur lors de l\'upload. Veuillez réessayer.';
            }
        }
    } else {
        $error = 'Veuillez sélectionner un fichier.';
    }
}

// Fonction pour envoyer l'email de confirmation
function sendPaymentProofEmail($user_email, $order_number) {
    try {
        $subject = "Preuve de paiement reçue - SMM Platform";
        
        $message = "
        <html>
        <head>
            <title>Preuve de paiement reçue</title>
        </head>
        <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
            <div style='max-width: 600px; margin: 0 auto; padding: 20px;'>
                <h1 style='color: #ff7a00; text-align: center;'>Preuve de paiement reçue !</h1>
                
                <p>Bonjour,</p>
                
                <p>Nous avons bien reçu votre preuve de paiement pour la commande <strong>{$order_number}</strong>.</p>
                
                <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                    <h3 style='color: #28a745; margin-top: 0;'>✅ Statut de votre commande</h3>
                    <p><strong>Statut :</strong> En cours de traitement</p>
                    <p><strong>Prochaine étape :</strong> Nous traitons votre commande et vous tiendrons informé de son avancement.</p>
                </div>
                
                <p>Vous pouvez suivre l'état de votre commande depuis votre <a href='" . SITE_URL . "/dashboard.php'>tableau de bord</a>.</p>
                
                <p>Merci de votre confiance !</p>
                
                <hr style='border: none; border-top: 1px solid #eee; margin: 30px 0;'>
                
                <p style='font-size: 14px; color: #666;'>
                    Cet email a été envoyé automatiquement. Merci de ne pas y répondre.
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
        
        if (defined('SMTP_BCC') && SMTP_BCC) {
            $headers[] = 'Bcc: ' . SMTP_BCC;
        }
        
        return mail($user_email, $subject, $message, implode("\r\n", $headers));
    } catch (Exception $e) {
        error_log("Erreur envoi email preuve: " . $e->getMessage());
        return false;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement - SMM Platform</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .payment-container {
            min-height: 100vh;
            background: var(--background-color);
            padding: 2rem 0;
        }
        
        .payment-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .payment-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }
        
        .payment-subtitle {
            color: var(--text-secondary);
            font-size: 1.1rem;
        }
        
        .payment-content {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .order-summary {
            background: var(--card-color);
            border-radius: var(--radius-lg);
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-lg);
        }
        
        .summary-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }
        
        .summary-value {
            color: var(--text-primary);
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .summary-total {
            color: var(--primary-color);
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .payment-form {
            background: var(--card-color);
            border-radius: var(--radius-lg);
            padding: 2rem;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-lg);
        }
        
        .form-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .upload-area {
            border: 2px dashed var(--border-color);
            border-radius: var(--radius-lg);
            padding: 3rem 2rem;
            text-align: center;
            margin-bottom: 2rem;
            transition: var(--transition-fast);
            cursor: pointer;
        }
        
        .upload-area:hover {
            border-color: var(--primary-color);
            background: rgba(255, 122, 0, 0.05);
        }
        
        .upload-area.dragover {
            border-color: var(--primary-color);
            background: rgba(255, 122, 0, 0.1);
        }
        
        .upload-icon {
            font-size: 3rem;
            color: var(--text-muted);
            margin-bottom: 1rem;
        }
        
        .upload-text {
            color: var(--text-secondary);
            margin-bottom: 1rem;
        }
        
        .upload-info {
            color: var(--text-muted);
            font-size: 0.9rem;
        }
        
        .file-input {
            display: none;
        }
        
        .selected-file {
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 1rem;
            margin-bottom: 1.5rem;
            display: none;
        }
        
        .file-name {
            color: var(--text-primary);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .file-size {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }
        
        .remove-file {
            color: var(--error-color);
            cursor: pointer;
            text-decoration: underline;
            font-size: 0.9rem;
        }
        
        .payment-instructions {
            background: rgba(255, 122, 0, 0.1);
            border: 1px solid rgba(255, 122, 0, 0.3);
            border-radius: var(--radius-md);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .instructions-title {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .instructions-list {
            list-style: none;
            padding: 0;
        }
        
        .instructions-list li {
            padding: 0.5rem 0;
            color: var(--text-secondary);
            position: relative;
            padding-left: 1.5rem;
        }
        
        .instructions-list li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: var(--primary-color);
            font-weight: bold;
        }
        
        @media (max-width: 768px) {
            .summary-grid {
                grid-template-columns: 1fr;
            }
            
            .payment-header {
                margin-bottom: 2rem;
            }
            
            .payment-title {
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
                        <li><a href="dashboard.php">Mon compte</a></li>
                        <li><a href="logout.php">Déconnexion</a></li>
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

    <div class="payment-container">
        <div class="container">
            <div class="payment-header">
                <h1 class="payment-title">💳 Paiement de votre commande</h1>
                <p class="payment-subtitle">Uploadez votre preuve de paiement pour finaliser votre commande</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error" style="margin-bottom: 2rem;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success" style="margin-bottom: 2rem;">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <div class="payment-content">
                <!-- Résumé de la commande -->
                <div class="order-summary">
                    <h2 class="summary-title">
                        📋 Résumé de votre commande
                    </h2>
                    
                    <div class="summary-grid">
                        <div class="summary-item">
                            <div class="summary-label">N° Commande</div>
                            <div class="summary-value"><?php echo htmlspecialchars($order['order_number']); ?></div>
                        </div>
                        
                        <div class="summary-item">
                            <div class="summary-label">Service</div>
                            <div class="summary-value"><?php echo htmlspecialchars($order['service_name']); ?></div>
                        </div>
                        
                        <div class="summary-item">
                            <div class="summary-label">Quantité</div>
                            <div class="summary-value"><?php echo number_format($order['quantity']); ?></div>
                        </div>
                        
                        <div class="summary-item">
                            <div class="summary-label">Montant total</div>
                            <div class="summary-value summary-total"><?php echo formatPrice($order['total_amount']); ?></div>
                        </div>
                    </div>
                </div>

                <!-- Instructions de paiement -->
                <div class="payment-instructions">
                    <h3 class="instructions-title">📝 Instructions de paiement</h3>
                    <ul class="instructions-list">
                        <li>Effectuez le virement ou le paiement du montant de <?php echo formatPrice($order['total_amount']); ?></li>
                        <li>Prenez une capture d'écran ou photo de votre preuve de paiement</li>
                        <li>Uploadez le fichier ci-dessous (JPG, PNG ou PDF)</li>
                        <li>Votre commande sera traitée dès réception de la preuve</li>
                    </ul>
                </div>

                <!-- Formulaire d'upload -->
                <div class="payment-form">
                    <h2 class="form-title">
                        📤 Upload de la preuve de paiement
                    </h2>
                    
                    <form method="POST" action="" enctype="multipart/form-data" id="uploadForm">
                        <div class="upload-area" id="uploadArea">
                            <div class="upload-icon">📁</div>
                            <div class="upload-text">
                                Cliquez ici ou glissez-déposez votre fichier
                            </div>
                            <div class="upload-info">
                                Formats acceptés : JPG, PNG, PDF • Taille max : 5MB
                            </div>
                            <input type="file" name="payment_proof" id="paymentProof" class="file-input" 
                                   accept=".jpg,.jpeg,.png,.pdf" required>
                        </div>
                        
                        <div class="selected-file" id="selectedFile">
                            <div class="file-name" id="fileName"></div>
                            <div class="file-size" id="fileSize"></div>
                            <div class="remove-file" onclick="removeFile()">Supprimer le fichier</div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-full">
                            ✅ Confirmer et envoyer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script>
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('paymentProof');
        const selectedFile = document.getElementById('selectedFile');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        
        // Gestion du clic sur la zone d'upload
        uploadArea.addEventListener('click', () => {
            fileInput.click();
        });
        
        // Gestion de la sélection de fichier
        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                displaySelectedFile(file);
            }
        });
        
        // Gestion du drag & drop
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });
        
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const file = e.dataTransfer.files[0];
            if (file) {
                fileInput.files = e.dataTransfer.files;
                displaySelectedFile(file);
            }
        });
        
        function displaySelectedFile(file) {
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            selectedFile.style.display = 'block';
            uploadArea.style.display = 'none';
        }
        
        function removeFile() {
            fileInput.value = '';
            selectedFile.style.display = 'none';
            uploadArea.style.display = 'block';
        }
        
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
        
        // Validation du formulaire
        document.getElementById('uploadForm').addEventListener('submit', function(e) {
            if (!fileInput.files[0]) {
                e.preventDefault();
                alert('Veuillez sélectionner un fichier.');
            }
        });
    </script>
</body>
</html>