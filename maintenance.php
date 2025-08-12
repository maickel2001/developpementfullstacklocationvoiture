<?php
require_once 'config/config.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site en maintenance - <?php echo SITE_NAME; ?></title>
    <style>
        :root {
            --primary-color: #ff7a00;
            --background-color: #0f0f0f;
            --surface-color: #1e1e1e;
            --text-primary: #ffffff;
            --text-secondary: #b0b0b0;
            --border-color: #404040;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', Roboto, sans-serif;
            background: linear-gradient(135deg, var(--background-color) 0%, var(--surface-color) 100%);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .maintenance-container {
            text-align: center;
            max-width: 600px;
            background: rgba(30, 30, 30, 0.8);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: 1rem;
            padding: 3rem 2rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .maintenance-icon {
            font-size: 4rem;
            margin-bottom: 2rem;
            display: block;
        }
        
        .maintenance-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--text-primary), var(--primary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .maintenance-message {
            font-size: 1.2rem;
            color: var(--text-secondary);
            margin-bottom: 2rem;
            line-height: 1.7;
        }
        
        .maintenance-info {
            background: rgba(255, 122, 0, 0.1);
            border: 1px solid rgba(255, 122, 0, 0.3);
            border-radius: 0.5rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .maintenance-info h3 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }
        
        .maintenance-info p {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }
        
        .contact-info {
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border-color);
        }
        
        .contact-info h4 {
            color: var(--text-primary);
            margin-bottom: 1rem;
        }
        
        .contact-info p {
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }
        
        .refresh-button {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 1rem;
        }
        
        .refresh-button:hover {
            background: #e66a00;
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .maintenance-container {
                padding: 2rem 1rem;
            }
            
            .maintenance-title {
                font-size: 2rem;
            }
            
            .maintenance-message {
                font-size: 1.1rem;
            }
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        .maintenance-icon {
            animation: pulse 2s infinite;
        }
    </style>
</head>
<body>
    <div class="maintenance-container">
        <span class="maintenance-icon">🔧</span>
        
        <h1 class="maintenance-title">Site en maintenance</h1>
        
        <p class="maintenance-message">
            Nous effectuons actuellement des améliorations sur notre plateforme pour vous offrir une meilleure expérience.
        </p>
        
        <div class="maintenance-info">
            <h3>⏰ Temps estimé</h3>
            <p>La maintenance devrait être terminée dans les prochaines heures. Merci de votre patience.</p>
        </div>
        
        <div class="maintenance-info">
            <h3>🔄 Mise à jour automatique</h3>
            <p>Cette page se rafraîchira automatiquement une fois la maintenance terminée.</p>
        </div>
        
        <div class="contact-info">
            <h4>📧 Besoin d'aide ?</h4>
            <p>Si vous avez une question urgente, contactez-nous :</p>
            <p><strong>Email :</strong> support@smmplatform.com</p>
            <p><strong>Support :</strong> Disponible 24/7</p>
        </div>
        
        <button class="refresh-button" onclick="location.reload()">
            🔄 Actualiser la page
        </button>
    </div>
    
    <script>
        // Vérifier automatiquement si le site est de nouveau disponible
        function checkMaintenanceStatus() {
            fetch('index.php', { method: 'HEAD' })
                .then(response => {
                    if (response.ok) {
                        // Rediriger vers la page principale
                        window.location.href = 'index.php';
                    }
                })
                .catch(error => {
                    console.log('Site toujours en maintenance');
                });
        }
        
        // Vérifier toutes les 30 secondes
        setInterval(checkMaintenanceStatus, 30000);
        
        // Vérifier aussi au clic sur le bouton d'actualisation
        document.querySelector('.refresh-button').addEventListener('click', function() {
            this.textContent = '🔄 Vérification...';
            this.disabled = true;
            
            setTimeout(() => {
                this.textContent = '🔄 Actualiser la page';
                this.disabled = false;
            }, 2000);
        });
    </script>
</body>
</html>