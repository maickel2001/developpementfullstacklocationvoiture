# 🖼️ Images du Site de Location de Voitures

## 📁 Structure des Dossiers

```
images/
├── cars/                     # Images des voitures (800x600px)
│   ├── bmw-serie7.jpg       # BMW Série 7
│   ├── mercedes-classe-s.jpg # Mercedes Classe S
│   ├── audi-a8.jpg          # Audi A8
│   ├── porsche-911.jpg      # Porsche 911
│   ├── lamborghini-huracan.jpg # Lamborghini Huracán
│   ├── range-rover-sport.jpg # Range Rover Sport
│   ├── tesla-model-s.jpg    # Tesla Model S
│   ├── rolls-royce-phantom.jpg # Rolls-Royce Phantom
│   └── bentley-continental-gt.jpg # Bentley Continental GT
├── backgrounds/              # Images d'arrière-plan (1920x1080px)
│   ├── hero-bg.jpg          # Arrière-plan principal
│   └── road-coastal.jpg     # Route côtière
└── fallback/                 # Images de fallback
    └── default-car.jpg       # Voiture par défaut
```

## 🚗 Images des Voitures

### Voitures de Luxe
- **BMW Série 7** : `bmw-serie7.jpg` (86 KB)
- **Mercedes Classe S** : `mercedes-classe-s.jpg` (65 KB)
- **Audi A8** : `audi-a8.jpg` (51 KB)
- **Rolls-Royce Phantom** : `rolls-royce-phantom.jpg` (86 KB)
- **Bentley Continental GT** : `bentley-continental-gt.jpg` (86 KB)

### Voitures Sport
- **Porsche 911** : `porsche-911.jpg` (56 KB)
- **Lamborghini Huracán** : `lamborghini-huracan.jpg` (67 KB)

### Voitures Premium
- **Range Rover Sport** : `range-rover-sport.jpg` (77 KB)
- **Tesla Model S** : `tesla-model-s.jpg` (82 KB)

## 🎨 Images d'Arrière-plan

- **Hero Background** : `hero-bg.jpg` (491 KB) - Route panoramique
- **Route Côtière** : `road-coastal.jpg` (250 KB) - Route au bord de la mer

## 🔧 Configuration de la Base de Données

### Mise à jour des images des voitures
```sql
-- Mise à jour des images dans la table cars
UPDATE cars SET image = 'bmw-serie7.jpg' WHERE titre LIKE '%BMW%';
UPDATE cars SET image = 'mercedes-classe-s.jpg' WHERE titre LIKE '%Mercedes%';
UPDATE cars SET image = 'audi-a8.jpg' WHERE titre LIKE '%Audi%';
UPDATE cars SET image = 'porsche-911.jpg' WHERE titre LIKE '%Porsche%';
UPDATE cars SET image = 'lamborghini-huracan.jpg' WHERE titre LIKE '%Lamborghini%';
UPDATE cars SET image = 'range-rover-sport.jpg' WHERE titre LIKE '%Range Rover%';
UPDATE cars SET image = 'tesla-model-s.jpg' WHERE titre LIKE '%Tesla%';
UPDATE cars SET image = 'rolls-royce-phantom.jpg' WHERE titre LIKE '%Rolls%';
UPDATE cars SET image = 'bentley-continental-gt.jpg' WHERE titre LIKE '%Bentley%';
```

### Ajout de nouvelles voitures avec images
```sql
-- Exemple d'ajout d'une nouvelle voiture
INSERT INTO cars (titre, description, prix_jour, image, disponible) VALUES
('BMW Série 7', 'Berline de luxe ultime avec intérieur premium', 150.00, 'bmw-serie7.jpg', 1),
('Mercedes Classe S', 'Élégance allemande et technologie de pointe', 180.00, 'mercedes-classe-s.jpg', 1),
('Audi A8', 'Technologie et confort au service du luxe', 160.00, 'audi-a8.jpg', 1),
('Porsche 911', 'Performance pure et design iconique', 200.00, 'porsche-911.jpg', 1),
('Lamborghini Huracán', 'Supercar italienne au design agressif', 350.00, 'lamborghini-huracan.jpg', 1),
('Range Rover Sport', 'Luxe et tout-terrain en un', 220.00, 'range-rover-sport.jpg', 1),
('Tesla Model S', 'Électrique premium et performance', 180.00, 'tesla-model-s.jpg', 1),
('Rolls-Royce Phantom', 'L\'excellence automobile britannique', 500.00, 'rolls-royce-phantom.jpg', 1),
('Bentley Continental GT', 'Grand tourisme britannique raffiné', 280.00, 'bentley-continental-gt.jpg', 1);
```

## 📱 Optimisation

### Tailles recommandées
- **Images des voitures** : 800x600px, < 100 KB
- **Images d'arrière-plan** : 1920x1080px, < 500 KB
- **Images de fallback** : 800x600px, < 100 KB

### Formats supportés
- **JPG/JPEG** : Recommandé pour les photos
- **PNG** : Si transparence nécessaire
- **WebP** : Format moderne pour de meilleures performances

## 🚀 Utilisation

### Dans le HTML
```html
<img src="images/cars/bmw-serie7.jpg" 
     alt="BMW Série 7" 
     class="car-image"
     loading="lazy">
```

### Dans le CSS
```css
.hero-section {
    background-image: url('../images/backgrounds/hero-bg.jpg');
    background-size: cover;
    background-position: center;
}
```

### Dans le PHP
```php
<img src="images/<?php echo htmlspecialchars($car['image']); ?>" 
     alt="<?php echo htmlspecialchars($car['titre']); ?>"
     class="car-image"
     onerror="this.src='images/fallback/default-car.jpg'">
```

## ✅ Vérification

- [x] Images des voitures téléchargées
- [x] Images d'arrière-plan téléchargées
- [x] Images de fallback créées
- [x] Tailles optimisées
- [x] Base de données mise à jour
- [x] Structure des dossiers créée

---

**Images prêtes pour un design professionnel ! 🖼️✨**