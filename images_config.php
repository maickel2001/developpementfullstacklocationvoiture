<?php
/**
 * 🖼️ Configuration des Images du Site de Location de Voitures
 * Fichier de configuration centralisé pour la gestion des images
 */

// Configuration des images du site
$images_config = [
    'cars' => [
        'bmw-serie7' => [
            'name' => 'BMW Série 7',
            'file' => 'bmw-serie7.jpg',
            'category' => 'luxe',
            'price_range' => 'premium'
        ],
        'mercedes-classe-s' => [
            'name' => 'Mercedes Classe S',
            'file' => 'mercedes-classe-s.jpg',
            'category' => 'luxe',
            'price_range' => 'premium'
        ],
        'audi-a8' => [
            'name' => 'Audi A8',
            'file' => 'audi-a8.jpg',
            'category' => 'luxe',
            'price_range' => 'premium'
        ],
        'porsche-911' => [
            'name' => 'Porsche 911',
            'file' => 'porsche-911.jpg',
            'category' => 'sport',
            'price_range' => 'luxury'
        ],
        'lamborghini-huracan' => [
            'name' => 'Lamborghini Huracán',
            'file' => 'lamborghini-huracan.jpg',
            'category' => 'sport',
            'price_range' => 'ultra-luxury'
        ],
        'range-rover-sport' => [
            'name' => 'Range Rover Sport',
            'file' => 'range-rover-sport.jpg',
            'category' => 'suv',
            'price_range' => 'premium'
        ],
        'tesla-model-s' => [
            'name' => 'Tesla Model S',
            'file' => 'tesla-model-s.jpg',
            'category' => 'electrique',
            'price_range' => 'premium'
        ],
        'rolls-royce-phantom' => [
            'name' => 'Rolls-Royce Phantom',
            'file' => 'rolls-royce-phantom.jpg',
            'category' => 'luxe',
            'price_range' => 'ultra-luxury'
        ],
        'bentley-continental-gt' => [
            'name' => 'Bentley Continental GT',
            'file' => 'bentley-continental-gt.jpg',
            'category' => 'luxe',
            'price_range' => 'ultra-luxury'
        ]
    ],
    'backgrounds' => [
        'hero-bg' => [
            'name' => 'Arrière-plan principal',
            'file' => 'hero-bg.jpg',
            'usage' => 'hero-section'
        ],
        'road-coastal' => [
            'name' => 'Route côtière',
            'file' => 'road-coastal.jpg',
            'usage' => 'background-alternative'
        ]
    ],
    'fallback' => [
        'default-car' => [
            'name' => 'Voiture par défaut',
            'file' => 'default-car.jpg',
            'usage' => 'error-fallback'
        ]
    ]
];

// Configuration des tailles d'images
$image_sizes = [
    'cars' => [
        'thumbnail' => [300, 200],
        'medium' => [600, 400],
        'large' => [800, 600],
        'full' => [1200, 800]
    ],
    'backgrounds' => [
        'mobile' => [768, 1024],
        'tablet' => [1024, 768],
        'desktop' => [1920, 1080],
        'full' => [2560, 1440]
    ]
];

// Configuration des formats supportés
$supported_formats = ['jpg', 'jpeg', 'png', 'webp'];

// Configuration des dossiers
$image_directories = [
    'cars' => 'images/cars/',
    'backgrounds' => 'images/backgrounds/',
    'fallback' => 'images/fallback/',
    'uploads' => 'uploads/'
];

/**
 * 🚀 Fonctions utilitaires pour la gestion des images
 */

/**
 * Obtenir le chemin complet d'une image
 * @param string $type Type d'image (cars, backgrounds, fallback)
 * @param string $name Nom de l'image
 * @return string Chemin complet de l'image
 */
function get_image_path($type, $name) {
    global $images_config, $image_directories;
    
    if (isset($images_config[$type][$name])) {
        return $image_directories[$type] . $images_config[$type][$name]['file'];
    }
    
    // Fallback vers l'image par défaut
    return $image_directories['fallback'] . 'default-car.jpg';
}

/**
 * Vérifier si une image existe
 * @param string $type Type d'image
 * @param string $name Nom de l'image
 * @return bool True si l'image existe
 */
function image_exists($type, $name) {
    $path = get_image_path($type, $name);
    return file_exists($path);
}

/**
 * Obtenir les informations d'une image
 * @param string $type Type d'image
 * @param string $name Nom de l'image
 * @return array|null Informations de l'image ou null si non trouvée
 */
function get_image_info($type, $name) {
    global $images_config;
    
    if (isset($images_config[$type][$name])) {
        $info = $images_config[$type][$name];
        $info['path'] = get_image_path($type, $name);
        $info['exists'] = image_exists($type, $name);
        
        if ($info['exists']) {
            $info['size'] = filesize($info['path']);
            $info['dimensions'] = getimagesize($info['path']);
        }
        
        return $info;
    }
    
    return null;
}

/**
 * Obtenir toutes les images d'un type donné
 * @param string $type Type d'image
 * @return array Liste des images disponibles
 */
function get_images_by_type($type) {
    global $images_config;
    
    if (isset($images_config[$type])) {
        $images = [];
        foreach ($images_config[$type] as $key => $config) {
            $images[$key] = get_image_info($type, $key);
        }
        return $images;
    }
    
    return [];
}

/**
 * Obtenir les images des voitures par catégorie
 * @param string $category Catégorie (luxe, sport, suv, electrique)
 * @return array Images filtrées par catégorie
 */
function get_cars_by_category($category) {
    global $images_config;
    
    $filtered_cars = [];
    foreach ($images_config['cars'] as $key => $config) {
        if ($config['category'] === $category) {
            $filtered_cars[$key] = get_image_info('cars', $key);
        }
    }
    
    return $filtered_cars;
}

/**
 * Obtenir les images des voitures par gamme de prix
 * @param string $price_range Gamme de prix (premium, luxury, ultra-luxury)
 * @return array Images filtrées par gamme de prix
 */
function get_cars_by_price_range($price_range) {
    global $images_config;
    
    $filtered_cars = [];
    foreach ($images_config['cars'] as $key => $config) {
        if ($config['price_range'] === $price_range) {
            $filtered_cars[$key] = get_image_info('cars', $key);
        }
    }
    
    return $filtered_cars;
}

/**
 * Générer une balise img optimisée
 * @param string $type Type d'image
 * @param string $name Nom de l'image
 * @param array $attributes Attributs HTML supplémentaires
 * @return string Balise img HTML
 */
function generate_image_tag($type, $name, $attributes = []) {
    $info = get_image_info($type, $name);
    
    if (!$info) {
        return '';
    }
    
    $default_attributes = [
        'src' => $info['path'],
        'alt' => $info['name'],
        'class' => 'img-fluid',
        'loading' => 'lazy'
    ];
    
    // Fusionner les attributs par défaut avec ceux fournis
    $final_attributes = array_merge($default_attributes, $attributes);
    
    // Construire la balise HTML
    $html = '<img';
    foreach ($final_attributes as $attr => $value) {
        $html .= ' ' . $attr . '="' . htmlspecialchars($value) . '"';
    }
    $html .= '>';
    
    return $html;
}

/**
 * Générer une balise img avec fallback
 * @param string $type Type d'image
 * @param string $name Nom de l'image
 * @param array $attributes Attributs HTML supplémentaires
 * @return string Balise img HTML avec fallback
 */
function generate_image_tag_with_fallback($type, $name, $attributes = []) {
    $info = get_image_info($type, $name);
    
    if (!$info || !$info['exists']) {
        // Utiliser l'image de fallback
        $fallback_info = get_image_info('fallback', 'default-car');
        $attributes['src'] = $fallback_info['path'];
        $attributes['alt'] = $fallback_info['name'];
    } else {
        $attributes['src'] = $info['path'];
        $attributes['alt'] = $info['name'];
    }
    
    $attributes['class'] = $attributes['class'] ?? 'img-fluid';
    $attributes['loading'] = $attributes['loading'] ?? 'lazy';
    
    // Ajouter l'attribut onerror pour le fallback JavaScript
    $attributes['onerror'] = "this.src='" . get_image_path('fallback', 'default-car') . "'";
    
    // Construire la balise HTML
    $html = '<img';
    foreach ($attributes as $attr => $value) {
        $html .= ' ' . $attr . '="' . htmlspecialchars($value) . '"';
    }
    $html .= '>';
    
    return $html;
}

/**
 * Obtenir les statistiques des images
 * @return array Statistiques des images
 */
function get_images_statistics() {
    global $images_config, $image_directories;
    
    $stats = [
        'total' => 0,
        'by_type' => [],
        'total_size' => 0,
        'missing' => 0
    ];
    
    foreach ($images_config as $type => $images) {
        $stats['by_type'][$type] = [
            'count' => 0,
            'size' => 0,
            'missing' => 0
        ];
        
        foreach ($images as $name => $config) {
            $stats['total']++;
            $stats['by_type'][$type]['count']++;
            
            $path = get_image_path($type, $name);
            if (file_exists($path)) {
                $size = filesize($path);
                $stats['total_size'] += $size;
                $stats['by_type'][$type]['size'] += $size;
            } else {
                $stats['missing']++;
                $stats['by_type'][$type]['missing']++;
            }
        }
    }
    
    return $stats;
}

/**
 * Vérifier l'intégrité des images
 * @return array Rapport d'intégrité
 */
function check_images_integrity() {
    global $images_config;
    
    $report = [
        'status' => 'ok',
        'issues' => [],
        'warnings' => []
    ];
    
    foreach ($images_config as $type => $images) {
        foreach ($images as $name => $config) {
            $path = get_image_path($type, $name);
            
            if (!file_exists($path)) {
                $report['status'] = 'error';
                $report['issues'][] = "Image manquante: $type/$name ($path)";
            } else {
                // Vérifier la taille du fichier
                $size = filesize($path);
                if ($size === 0) {
                    $report['status'] = 'error';
                    $report['issues'][] = "Image vide: $type/$name";
                } elseif ($size < 1024) {
                    $report['warnings'][] = "Image très petite: $type/$name ($size bytes)";
                }
                
                // Vérifier que c'est bien une image
                $image_info = getimagesize($path);
                if ($image_info === false) {
                    $report['status'] = 'error';
                    $report['issues'][] = "Fichier non-image: $type/$name";
                }
            }
        }
    }
    
    return $report;
}

// Initialisation : vérifier l'intégrité au chargement
if (function_exists('check_images_integrity')) {
    $images_integrity = check_images_integrity();
    if ($images_integrity['status'] === 'error') {
        error_log('Images integrity check failed: ' . implode(', ', $images_integrity['issues']));
    }
}
?>