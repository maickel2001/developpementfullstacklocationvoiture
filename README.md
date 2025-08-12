# 🚗 Site de Location de Voitures

Un site web professionnel et moderne pour la location de voitures, développé en PHP 8.x avec MySQL et Bootstrap 5.

## ✨ Fonctionnalités

### 🏠 Page d'accueil
- **En-tête moderne** avec logo et navigation responsive
- **Bannière hero** avec moteur de recherche avancé
- **Catalogue des voitures** en cartes avec photos et prix
- **Section avantages** avec icônes et descriptions
- **Pied de page** complet avec informations de contact

### 📅 Système de réservation
- **Formulaire complet** : nom, email, dates, opérateur mobile money
- **Sélection de voiture** avec calcul automatique du montant
- **Upload sécurisé** de preuve de paiement (JPG/PNG, max 5 Mo)
- **Validation des données** côté serveur et client
- **Sauvegarde en base** avec gestion des erreurs

### 🔐 Interface d'administration
- **Authentification sécurisée** par mot de passe
- **Tableau de bord** avec statistiques en temps réel
- **Gestion des réservations** : validation/refus
- **Visualisation des preuves** de paiement
- **Interface responsive** et intuitive

### 🛡️ Sécurité et fiabilité
- **Validation serveur** de toutes les entrées
- **Protection contre** l'exécution de fichiers uploadés
- **Vérification des extensions** et tailles de fichiers
- **Nettoyage des données** pour prévenir les injections
- **Gestion des sessions** sécurisée

## 🛠️ Technologies utilisées

- **Backend** : PHP 8.x
- **Base de données** : MySQL 5.7+
- **Frontend** : Bootstrap 5.3.0, HTML5, CSS3, JavaScript
- **Icônes** : Bootstrap Icons 1.10.0
- **Responsive** : Design mobile-first

## 📁 Structure du projet

```
location-voitures/
├── admin/                 # Interface d'administration
│   ├── index.php         # Tableau de bord admin
│   ├── login.php         # Page de connexion
│   └── logout.php        # Déconnexion
├── css/                  # Feuilles de style
│   ├── style.css         # Styles principaux
│   └── admin.css         # Styles admin
├── images/               # Images des voitures
├── uploads/              # Preuves de paiement (créé automatiquement)
├── config.php            # Configuration et connexion DB
├── database.sql          # Structure de la base de données
├── index.php             # Page d'accueil
├── reservation.php       # Page de réservation
├── recherche.php         # Page de recherche
└── README.md             # Ce fichier
```

## 🚀 Installation sur Hostinger

### 1. Préparation de la base de données

1. **Connectez-vous** à votre panneau de contrôle Hostinger
2. **Allez dans** "Bases de données MySQL"
3. **Créez une nouvelle base** :
   - Nom : `location_voitures`
   - Utilisateur : `votre_utilisateur`
   - Mot de passe : `votre_mot_de_passe_securise`
   - Hôte : `localhost`

### 2. Import de la structure

1. **Ouvrez** phpMyAdmin depuis votre panneau Hostinger
2. **Sélectionnez** votre base `location_voitures`
3. **Importez** le fichier `database.sql`
4. **Vérifiez** que les tables `cars` et `bookings` sont créées

### 3. Configuration du site

1. **Modifiez** le fichier `config.php` :
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'location_voitures');
   define('DB_USER', 'votre_utilisateur');
   define('DB_PASS', 'votre_mot_de_passe_securise');
   define('SITE_URL', 'https://votre-domaine.com');
   ```

2. **Changez les identifiants admin** (recommandé) :
   ```php
   define('ADMIN_USERNAME', 'votre_nom_admin');
   define('ADMIN_PASSWORD', 'votre_mot_de_passe_admin_securise');
   ```

### 4. Upload des fichiers

#### Option A : Via File Manager Hostinger
1. **Ouvrez** le File Manager depuis votre panneau
2. **Naviguez** vers le dossier `public_html`
3. **Uploadez** tous les fichiers du projet
4. **Vérifiez** que la structure est correcte

#### Option B : Via FTP
1. **Utilisez** un client FTP (FileZilla, WinSCP)
2. **Connectez-vous** avec vos identifiants Hostinger
3. **Uploadez** vers le dossier `public_html`

### 5. Création des dossiers

1. **Créez** le dossier `uploads/` dans `public_html`
2. **Définissez** les permissions à `755` pour le dossier `uploads/`
3. **Ajoutez** quelques images de voitures dans le dossier `images/`

### 6. Test du site

1. **Visitez** `https://votre-domaine.com`
2. **Testez** la navigation et les fonctionnalités
3. **Accédez** à l'admin via `/admin/`
4. **Testez** une réservation complète

## 🔧 Configuration avancée

### Personnalisation des couleurs
Modifiez les variables CSS dans `css/style.css` :
```css
:root {
    --primary-color: #0d6efd;    /* Couleur principale */
    --secondary-color: #6c757d;  /* Couleur secondaire */
    /* ... autres couleurs */
}
```

### Ajout de voitures
1. **Ajoutez** l'image dans `images/`
2. **Insérez** en base via phpMyAdmin :
   ```sql
   INSERT INTO cars (titre, description, prix_jour, image) 
   VALUES ('Nouvelle Voiture', 'Description...', 35.00, 'image.jpg');
   ```

### Modification des informations de contact
Éditez le fichier `index.php` et modifiez la section footer.

## 📱 Fonctionnalités mobiles

- **Design responsive** optimisé pour tous les écrans
- **Navigation tactile** intuitive
- **Formulaires adaptés** aux mobiles
- **Images optimisées** pour le web

## 🔒 Sécurité

### Mesures implémentées
- **Validation des entrées** côté serveur
- **Protection contre** les injections SQL (PDO)
- **Vérification des fichiers** uploadés
- **Gestion des sessions** sécurisée
- **Nettoyage des données** utilisateur

### Recommandations de sécurité
1. **Changez** les identifiants admin par défaut
2. **Utilisez** HTTPS (SSL) en production
3. **Sauvegardez** régulièrement votre base de données
4. **Mettez à jour** PHP et MySQL régulièrement
5. **Limitez** l'accès au dossier `admin/` si possible

## 🐛 Dépannage

### Problèmes courants

#### Erreur de connexion à la base
- **Vérifiez** les paramètres dans `config.php`
- **Assurez-vous** que la base existe et est accessible
- **Vérifiez** les permissions de l'utilisateur MySQL

#### Images qui ne s'affichent pas
- **Vérifiez** que le dossier `images/` existe
- **Ajoutez** des images de test
- **Vérifiez** les permissions des fichiers

#### Uploads qui ne fonctionnent pas
- **Vérifiez** que le dossier `uploads/` existe
- **Définissez** les permissions à `755`
- **Vérifiez** la limite de taille dans `php.ini`

#### Erreur 500
- **Vérifiez** les logs d'erreur PHP
- **Assurez-vous** que PHP 8.x est activé
- **Vérifiez** la syntaxe des fichiers PHP

## 📞 Support

Pour toute question ou problème :
- **Email** : contact@location-voitures.fr
- **Documentation** : Consultez ce README
- **Logs** : Vérifiez les logs d'erreur de votre hébergeur

## 📄 Licence

Ce projet est fourni "tel quel" pour un usage personnel et commercial. Libre de modification et distribution.

## 🎯 Fonctionnalités futures

- **Système de notifications** par email
- **Gestion des clients** avec historique
- **Système de paiement** en ligne
- **API REST** pour applications mobiles
- **Multi-langues** (FR/EN)
- **Système de réservation** en temps réel

---

**Développé avec ❤️ pour la location de voitures professionnelle**

*Dernière mise à jour : <?php echo date('d/m/Y'); ?>*