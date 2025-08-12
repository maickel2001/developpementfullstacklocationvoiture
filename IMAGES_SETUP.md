# 🖼️ Configuration des Images - Guide Pratique

## 📥 Téléchargement des Images de Voitures

### 1. Images Principales (800x600px)

#### Voitures de Luxe
- **BMW Série 7** : [Télécharger](https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800&h=600&fit=crop&q=80)
- **Mercedes Classe S** : [Télécharger](https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?w=800&h=600&fit=crop&q=80)
- **Audi A8** : [Télécharger](https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?w=800&h=600&fit=crop&q=80)
- **Porsche 911** : [Télécharger](https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&h=600&fit=crop&q=80)

#### Voitures Sport
- **Ferrari F8** : [Télécharger](https://images.unsplash.com/photo-1563720353473-9674aa7b8b10?w=800&h=600&fit=crop&q=80)
- **Lamborghini Huracán** : [Télécharger](https://images.unsplash.com/photo-1544636331-e26879cd4d9b?w=800&h=600&fit=crop&q=80)
- **Porsche Cayenne** : [Télécharger](https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800&h=600&fit=crop&q=80)

#### Voitures Premium
- **Range Rover Sport** : [Télécharger](https://images.unsplash.com/photo-1606220838315-056192d5e927?w=800&h=600&fit=crop&q=80)
- **Tesla Model S** : [Télécharger](https://images.unsplash.com/photo-1560958089-b8a1929cea89?w=800&h=600&fit=crop&q=80)
- **Rolls-Royce Phantom** : [Télécharger](https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?w=800&h=600&fit=crop&q=80)

### 2. Images d'Arrière-plan (1920x1080px)

#### Hero Background
- **Route de montagne** : [Télécharger](https://images.unsplash.com/photo-1449824913935-59a10b8d2000?w=1920&h=1080&fit=crop&q=80)
- **Route côtière** : [Télécharger](https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1920&h=1080&fit=crop&q=80)
- **Route urbaine** : [Télécharger](https://images.unsplash.com/photo-1449824913935-59a10b8d2000?w=1920&h=1080&fit=crop&q=80)

### 3. Images de Fallback
- **Voiture par défaut** : [Télécharger](https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800&h=600&fit=crop&q=80)

## 🚀 Installation Rapide

### 1. Créer les dossiers
```bash
mkdir -p images/cars
mkdir -p images/backgrounds
mkdir -p images/fallback
```

### 2. Télécharger et renommer
```bash
# Exemple de téléchargement
wget "https://images.unsplash.com/photo-1555215695-3004980ad54e?w=800&h=600&fit=crop&q=80" -O "images/cars/bmw-serie7.jpg"
wget "https://images.unsplash.com/photo-1618843479313-40f8afb4b4d8?w=800&h=600&fit=crop&q=80" -O "images/cars/mercedes-classe-s.jpg"
wget "https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?w=800&h=600&fit=crop&q=80" -O "images/cars/audi-a8.jpg"
```

### 3. Mettre à jour la base de données
```sql
UPDATE cars SET image = 'bmw-serie7.jpg' WHERE titre LIKE '%BMW%';
UPDATE cars SET image = 'mercedes-classe-s.jpg' WHERE titre LIKE '%Mercedes%';
UPDATE cars SET image = 'audi-a8.jpg' WHERE titre LIKE '%Audi%';
```

## 📱 Optimisation Mobile

### 1. Images Responsives
```html
<picture>
    <source media="(min-width: 1200px)" srcset="image-large.jpg">
    <source media="(min-width: 768px)" srcset="image-medium.jpg">
    <img src="image-small.jpg" alt="Description" class="car-image">
</picture>
```

### 2. Lazy Loading
```html
<img src="image.jpg" alt="Description" loading="lazy" class="car-image">
```

## ✅ Vérification

- [ ] Images téléchargées
- [ ] Images renommées
- [ ] Base de données mise à jour
- [ ] Images s'affichent correctement
- [ ] Responsive testé
- [ ] Performance optimisée

---

**Images de qualité = Design professionnel ! 🖼️✨**