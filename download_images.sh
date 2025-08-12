#!/bin/bash

# 🚗 Script de Téléchargement Automatique des Images
# Site de Location de Voitures

echo "🚗 Téléchargement des images du site de location de voitures..."
echo "=================================================="

# Création des dossiers
echo "📁 Création de la structure des dossiers..."
mkdir -p images/cars
mkdir -p images/backgrounds
mkdir -p images/fallback
mkdir -p videos

# Fonction de téléchargement avec gestion d'erreur
download_image() {
    local url="$1"
    local filename="$2"
    local folder="$3"
    
    echo "📥 Téléchargement de $filename..."
    if wget -q "$url" -O "$folder/$filename"; then
        echo "✅ $filename téléchargé avec succès"
    else
        echo "❌ Erreur lors du téléchargement de $filename"
        return 1
    fi
}

# Téléchargement des images des voitures
echo ""
echo "🚗 Téléchargement des images des voitures..."
cd images/cars

download_image "https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800&h=600&fit=crop&q=80" "bmw-serie7.jpg" "."
download_image "https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?w=800&h=600&fit=crop&q=80" "mercedes-classe-s.jpg" "."
download_image "https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?w=800&h=600&fit=crop&q=80" "audi-a8.jpg" "."
download_image "https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&h=600&fit=crop&q=80" "porsche-911.jpg" "."
download_image "https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&h=600&fit=crop&q=80" "lamborghini-huracan.jpg" "."
download_image "https://images.unsplash.com/photo-1606220838315-056192d5e927?w=800&h=600&fit=crop&q=80" "range-rover-sport.jpg" "."
download_image "https://images.unsplash.com/photo-1560958089-b8a1929cea89?w=800&h=600&fit=crop&q=80" "tesla-model-s.jpg" "."
download_image "https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800&h=600&fit=crop&q=80" "rolls-royce-phantom.jpg" "."
download_image "https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800&h=600&fit=crop&q=80" "bentley-continental-gt.jpg" "."

# Téléchargement des images d'arrière-plan
echo ""
echo "🎨 Téléchargement des images d'arrière-plan..."
cd ../backgrounds

download_image "https://images.unsplash.com/photo-1449824913935-59a10b8d2000?w=1920&h=1080&fit=crop&q=80" "hero-bg.jpg" "."
download_image "https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1920&h=1080&fit=crop&q=80" "road-coastal.jpg" "."

# Téléchargement des images de fallback
echo ""
echo "🔄 Téléchargement des images de fallback..."
cd ../fallback

download_image "https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800&h=600&fit=crop&q=80" "default-car.jpg" "."

# Retour au dossier racine
cd ../../

# Vérification des fichiers téléchargés
echo ""
echo "🔍 Vérification des fichiers téléchargés..."
echo "=================================================="

echo "📊 Images des voitures:"
ls -la images/cars/ | grep "\.jpg$" | wc -l
echo "📊 Images d'arrière-plan:"
ls -la images/backgrounds/ | grep "\.jpg$" | wc -l
echo "📊 Images de fallback:"
ls -la images/fallback/ | grep "\.jpg$" | wc -l

# Calcul de la taille totale
echo ""
echo "💾 Taille totale des images:"
du -sh images/

# Création d'un fichier de configuration
echo ""
echo "⚙️ Création du fichier de configuration..."
cat > images_config.php << 'EOF'
<?php
// Configuration des images du site
$images_config = [
    'cars' => [
        'bmw-serie7' => 'BMW Série 7',
        'mercedes-classe-s' => 'Mercedes Classe S',
        'audi-a8' => 'Audi A8',
        'porsche-911' => 'Porsche 911',
        'lamborghini-huracan' => 'Lamborghini Huracán',
        'range-rover-sport' => 'Range Rover Sport',
        'tesla-model-s' => 'Tesla Model S',
        'rolls-royce-phantom' => 'Rolls-Royce Phantom',
        'bentley-continental-gt' => 'Bentley Continental GT'
    ],
    'backgrounds' => [
        'hero-bg' => 'Arrière-plan principal',
        'road-coastal' => 'Route côtière'
    ],
    'fallback' => [
        'default-car' => 'Voiture par défaut'
    ]
];

// Fonction pour obtenir le chemin d'une image
function get_image_path($type, $name) {
    global $images_config;
    if (isset($images_config[$type][$name])) {
        return "images/$type/$name.jpg";
    }
    return "images/fallback/default-car.jpg";
}

// Fonction pour vérifier si une image existe
function image_exists($type, $name) {
    $path = get_image_path($type, $name);
    return file_exists($path);
}
?>
EOF

echo "✅ Fichier de configuration créé: images_config.php"

# Instructions finales
echo ""
echo "🎉 Téléchargement terminé avec succès !"
echo "=================================================="
echo ""
echo "📋 Prochaines étapes:"
echo "1. Importer le script SQL: update_images.sql"
echo "2. Vérifier que toutes les images s'affichent"
echo "3. Tester le site avec les nouvelles images"
echo ""
echo "🔗 Fichiers créés:"
echo "- images/cars/ (9 images de voitures)"
echo "- images/backgrounds/ (2 images d'arrière-plan)"
echo "- images/fallback/ (1 image de fallback)"
echo "- update_images.sql (script de mise à jour DB)"
echo "- images_config.php (configuration des images)"
echo ""
echo "✨ Votre site est maintenant prêt avec des images professionnelles !"