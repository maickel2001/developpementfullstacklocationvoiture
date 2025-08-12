<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$categories = [];
$services = [];
$selected_category = $_GET['category'] ?? 'all';
$search_query = $_GET['search'] ?? '';

try {
    $pdo = getDB();
    
    // Récupérer les catégories
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE active = 1 ORDER BY sort_order, name");
    $stmt->execute();
    $categories = $stmt->fetchAll();
    
    // Construire la requête des services
    $where_conditions = ["s.active = 1"];
    $params = [];
    
    if ($selected_category !== 'all') {
        $where_conditions[] = "s.category_id = ?";
        $params[] = $selected_category;
    }
    
    if (!empty($search_query)) {
        $where_conditions[] = "(s.name LIKE ? OR s.description LIKE ?)";
        $search_param = "%{$search_query}%";
        $params[] = $search_param;
        $params[] = $search_param;
    }
    
    $where_clause = implode(" AND ", $where_conditions);
    
    $stmt = $pdo->prepare("
        SELECT s.*, c.name as category_name, c.icon as category_icon
        FROM services s 
        JOIN categories c ON s.category_id = c.id 
        WHERE {$where_clause}
        ORDER BY c.sort_order, s.popularity DESC, s.name
    ");
    $stmt->execute($params);
    $services = $stmt->fetchAll();
    
} catch (Exception $e) {
    error_log("Erreur chargement services: " . $e->getMessage());
    $error = 'Erreur lors du chargement des services.';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nos Services - SMM Platform</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .services-container {
            min-height: 100vh;
            background: var(--background-color);
            padding: 2rem 0;
        }
        
        .services-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .services-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 1rem;
        }
        
        .services-subtitle {
            color: var(--text-secondary);
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .filters-section {
            background: var(--card-color);
            border-radius: var(--radius-lg);
            padding: 2rem;
            margin-bottom: 3rem;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-lg);
        }
        
        .filters-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
        }
        
        .filters-content {
            display: flex;
            gap: 2rem;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .category-filter {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .category-btn {
            padding: 0.75rem 1.5rem;
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            color: var(--text-secondary);
            cursor: pointer;
            transition: var(--transition-fast);
            font-size: 0.9rem;
            text-decoration: none;
        }
        
        .category-btn:hover,
        .category-btn.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .search-filter {
            flex: 1;
            min-width: 300px;
        }
        
        .search-input {
            width: 100%;
            padding: 0.75rem;
            background: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            color: var(--text-primary);
            font-size: 1rem;
            transition: var(--transition-fast);
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(255, 122, 0, 0.1);
        }
        
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .service-card {
            background: var(--card-color);
            border-radius: var(--radius-lg);
            padding: 2rem;
            border: 1px solid var(--border-color);
            transition: var(--transition-normal);
            position: relative;
            overflow: hidden;
        }
        
        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--primary-hover));
            transform: scaleX(0);
            transition: var(--transition-normal);
        }
        
        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
            border-color: var(--primary-color);
        }
        
        .service-card:hover::before {
            transform: scaleX(1);
        }
        
        .service-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .service-icon {
            font-size: 2.5rem;
            color: var(--primary-color);
        }
        
        .service-info h3 {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }
        
        .service-category {
            color: var(--text-muted);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .service-description {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        
        .service-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .detail-item {
            text-align: center;
            padding: 1rem;
            background: var(--surface-color);
            border-radius: var(--radius-md);
        }
        
        .detail-label {
            color: var(--text-muted);
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }
        
        .detail-value {
            color: var(--text-primary);
            font-weight: 600;
        }
        
        .service-price {
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .price-amount {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .price-unit {
            color: var(--text-muted);
            font-size: 1rem;
        }
        
        .service-actions {
            display: flex;
            gap: 1rem;
        }
        
        .btn-order {
            flex: 1;
        }
        
        .btn-details {
            flex: 1;
        }
        
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-secondary);
        }
        
        .empty-state .icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        .empty-state h3 {
            color: var(--text-primary);
            margin-bottom: 1rem;
        }
        
        .stats-section {
            background: var(--card-color);
            border-radius: var(--radius-lg);
            padding: 2rem;
            margin-bottom: 3rem;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-lg);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        @media (max-width: 768px) {
            .filters-content {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-filter {
                min-width: auto;
            }
            
            .services-grid {
                grid-template-columns: 1fr;
            }
            
            .service-details {
                grid-template-columns: 1fr;
            }
            
            .service-actions {
                flex-direction: column;
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
                        <li><a href="services.php" class="active">Services</a></li>
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

    <div class="services-container">
        <div class="container">
            <div class="services-header">
                <h1 class="services-title">🚀 Nos Services SMM</h1>
                <p class="services-subtitle">
                    Découvrez notre gamme complète de services de marketing sur les réseaux sociaux. 
                    Qualité professionnelle, prix compétitifs et résultats garantis.
                </p>
            </div>

            <!-- Statistiques -->
            <div class="stats-section">
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo count($categories); ?>+</div>
                        <div class="stat-label">Catégories de services</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo count($services); ?>+</div>
                        <div class="stat-label">Services disponibles</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Support client</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Satisfaction garantie</div>
                    </div>
                </div>
            </div>

            <!-- Filtres -->
            <div class="filters-section">
                <h2 class="filters-title">🔍 Filtrer les services</h2>
                
                <form method="GET" action="" class="filters-content">
                    <div class="category-filter">
                        <a href="?search=<?php echo urlencode($search_query); ?>" 
                           class="category-btn <?php echo $selected_category === 'all' ? 'active' : ''; ?>">
                            Tous
                        </a>
                        <?php foreach ($categories as $category): ?>
                            <a href="?category=<?php echo $category['id']; ?>&search=<?php echo urlencode($search_query); ?>" 
                               class="category-btn <?php echo $selected_category == $category['id'] ? 'active' : ''; ?>">
                                <?php echo $category['icon']; ?> <?php echo htmlspecialchars($category['name']); ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="search-filter">
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search_query); ?>" 
                               class="search-input" placeholder="Rechercher un service...">
                    </div>
                </form>
            </div>

            <!-- Grille des services -->
            <?php if (empty($services)): ?>
                <div class="empty-state">
                    <div class="icon">🔍</div>
                    <h3>Aucun service trouvé</h3>
                    <p>
                        <?php if (!empty($search_query)): ?>
                            Aucun service ne correspond à votre recherche "<?php echo htmlspecialchars($search_query); ?>".
                        <?php else: ?>
                            Aucun service disponible dans cette catégorie pour le moment.
                        <?php endif; ?>
                    </p>
                    <a href="services.php" class="btn btn-outline">Voir tous les services</a>
                </div>
            <?php else: ?>
                <div class="services-grid">
                    <?php foreach ($services as $service): ?>
                        <div class="service-card">
                            <div class="service-header">
                                <div class="service-icon"><?php echo $service['category_icon']; ?></div>
                                <div class="service-info">
                                    <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                                    <div class="service-category">
                                        <span><?php echo $service['category_icon']; ?></span>
                                        <?php echo htmlspecialchars($service['category_name']); ?>
                                    </div>
                                </div>
                            </div>
                            
                            <p class="service-description">
                                <?php echo htmlspecialchars($service['description']); ?>
                            </p>
                            
                            <div class="service-details">
                                <div class="detail-item">
                                    <div class="detail-label">Quantité min</div>
                                    <div class="detail-value"><?php echo number_format($service['min_quantity']); ?></div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Livraison</div>
                                    <div class="detail-value"><?php echo htmlspecialchars($service['delivery_time']); ?></div>
                                </div>
                            </div>
                            
                            <div class="service-price">
                                <div class="price-amount"><?php echo formatPrice($service['price']); ?></div>
                                <div class="price-unit">
                                    <?php 
                                    if ($service['price_per_unit'] === 'per_1000') {
                                        echo '/1000';
                                    } elseif ($service['price_per_unit'] === 'per_unit') {
                                        echo '/unité';
                                    } else {
                                        echo 'fixe';
                                    }
                                    ?>
                                </div>
                            </div>
                            
                            <div class="service-actions">
                                <?php if (isLoggedIn()): ?>
                                    <a href="order.php?service=<?php echo $service['id']; ?>" 
                                       class="btn btn-primary btn-order">
                                        🛒 Commander
                                    </a>
                                <?php else: ?>
                                    <a href="login.php" class="btn btn-primary btn-order">
                                        🔐 Se connecter
                                    </a>
                                <?php endif; ?>
                                
                                <button class="btn btn-secondary btn-details" 
                                        onclick="showServiceDetails(<?php echo htmlspecialchars(json_encode($service)); ?>)">
                                    ℹ️ Détails
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal des détails du service -->
    <div id="serviceModal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="modalContent"></div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script>
        // Recherche en temps réel
        document.querySelector('.search-input').addEventListener('input', function() {
            const searchValue = this.value.trim();
            const currentUrl = new URL(window.location);
            
            if (searchValue) {
                currentUrl.searchParams.set('search', searchValue);
            } else {
                currentUrl.searchParams.delete('search');
            }
            
            // Rediriger avec la nouvelle recherche
            window.location.href = currentUrl.toString();
        });
        
        // Modal des détails du service
        function showServiceDetails(service) {
            const modal = document.getElementById('serviceModal');
            const modalContent = document.getElementById('modalContent');
            
            modalContent.innerHTML = `
                <h2>${service.name}</h2>
                <p><strong>Catégorie :</strong> ${service.category_name}</p>
                <p><strong>Description :</strong> ${service.description}</p>
                <p><strong>Prix :</strong> ${service.price}€ 
                    ${service.price_per_unit === 'per_1000' ? '/1000' : 
                      service.price_per_unit === 'per_unit' ? '/unité' : '(fixe)'}</p>
                <p><strong>Quantité minimale :</strong> ${service.min_quantity.toLocaleString()}</p>
                <p><strong>Quantité maximale :</strong> ${service.max_quantity.toLocaleString()}</p>
                <p><strong>Temps de livraison :</strong> ${service.delivery_time}</p>
                <p><strong>Popularité :</strong> ${service.popularity}/100</p>
            `;
            
            modal.style.display = 'block';
        }
        
        // Fermer la modal
        document.querySelector('.close').addEventListener('click', function() {
            document.getElementById('serviceModal').style.display = 'none';
        });
        
        // Fermer la modal en cliquant à l'extérieur
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('serviceModal');
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
        
        // Animation des cartes au scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };
        
        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);
        
        // Observer toutes les cartes de service
        document.querySelectorAll('.service-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });
    </script>
    
    <style>
        /* Styles pour la modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        .modal-content {
            background-color: var(--card-color);
            margin: 15% auto;
            padding: 2rem;
            border-radius: var(--radius-lg);
            width: 90%;
            max-width: 600px;
            position: relative;
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-xl);
        }
        
        .close {
            color: var(--text-muted);
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            position: absolute;
            top: 1rem;
            right: 1.5rem;
        }
        
        .close:hover {
            color: var(--text-primary);
        }
        
        .modal-content h2 {
            color: var(--text-primary);
            margin-bottom: 1rem;
        }
        
        .modal-content p {
            color: var(--text-secondary);
            margin-bottom: 0.5rem;
        }
        
        .modal-content strong {
            color: var(--text-primary);
        }
    </style>
</body>
</html>