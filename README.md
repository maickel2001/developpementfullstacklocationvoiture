# SMM Platform - Plateforme de Marketing sur les Réseaux Sociaux

Une plateforme complète et moderne de services de marketing sur les réseaux sociaux, conçue avec une approche "mobile-first" et une esthétique premium inspirée d'Apple.

## 🚀 Fonctionnalités

### Côté Client
- **Inscription/Connexion sécurisée** avec validation email
- **Catalogue de services** organisé par catégories
- **Système de commande** avec calcul automatique des prix
- **Upload sécurisé** des preuves de paiement
- **Suivi des commandes** en temps réel
- **Espace profil** personnalisé
- **Système d'avis** et notation

### Côté Administration
- **Tableau de bord** avec statistiques et graphiques Chart.js
- **Gestion des commandes** avec filtres, recherche et pagination
- **Gestion des services** et catégories
- **Gestion des utilisateurs** et rôles
- **Paramètres du site** configurables (couleurs, emails, etc.)
- **Mode maintenance** activable
- **Logs système** et emails

### Caractéristiques Techniques
- **Design mobile-first** avec interface unifiée
- **Palette de couleurs** personnalisable (couleur primaire #ff7a00)
- **Animations fluides** et transitions douces
- **Responsive design** optimisé pour tous les appareils
- **Sécurité renforcée** (sessions, validation, protection contre les attaques)
- **Emails transactionnels** automatiques
- **Base de données** optimisée avec vues et procédures stockées

## 🛠️ Technologies Utilisées

- **Backend** : PHP 7.4+
- **Base de données** : MySQL 5.7+
- **Frontend** : HTML5, CSS3, JavaScript (ES6+)
- **Graphiques** : Chart.js
- **Sécurité** : Sessions PHP, hachage des mots de passe, protection CSRF
- **Design** : CSS Variables, Flexbox, Grid, Animations CSS

## 📋 Prérequis

- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache/Nginx)
- Extension PHP : PDO, PDO_MySQL, OpenSSL
- Composer (optionnel, pour les dépendances futures)

## 🚀 Installation

### 1. Cloner le projet
```bash
git clone https://github.com/votre-username/smm-platform.git
cd smm-platform
```

### 2. Configuration de la base de données
```bash
# Créer la base de données
mysql -u root -p < database/schema.sql

# Ou importer manuellement le fichier database/schema.sql
```

### 3. Configuration des fichiers
```bash
# Copier et configurer le fichier de base de données
cp config/database.php.example config/database.php
# Éditer config/database.php avec vos paramètres de base de données

# Copier et configurer le fichier de configuration
cp config/config.php.example config/config.php
# Éditer config/config.php avec vos paramètres
```

### 4. Configuration des permissions
```bash
# Créer les dossiers nécessaires
mkdir -p uploads/proofs
mkdir -p logs
chmod 755 uploads/
chmod 755 logs/
chmod 644 config/*.php
```

### 5. Configuration du serveur web

#### Apache (.htaccess)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]

# Sécurité
<Files "config/*.php">
    Order allow,deny
    Deny from all
</Files>

<Files "database/*.sql">
    Order allow,deny
    Deny from all
</Files>
```

#### Nginx
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
}
```

## ⚙️ Configuration

### Base de données (config/database.php)
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'smm_platform');
define('DB_USER', 'votre_utilisateur');
define('DB_PASS', 'votre_mot_de_passe');
```

### Site (config/config.php)
```php
define('SITE_NAME', 'SMM Platform');
define('SITE_URL', 'https://votre-domaine.com');
define('SITE_EMAIL', 'support@votre-domaine.com');
define('PRIMARY_COLOR', '#ff7a00');
```

### Email SMTP
```php
define('SMTP_HOST', 'smtp.votre-domaine.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'votre-email@domaine.com');
define('SMTP_PASS', 'votre-mot-de-passe');
```

## 🔐 Comptes par défaut

### Administrateur
- **Email** : admin@smmplatform.com
- **Mot de passe** : admin123

⚠️ **Important** : Changez immédiatement le mot de passe de l'administrateur après l'installation !

## 📱 Utilisation

### 1. Accès client
- Visitez votre site
- Créez un compte ou connectez-vous
- Parcourez les services disponibles
- Passez une commande
- Uploadez la preuve de paiement
- Suivez l'état de votre commande

### 2. Accès administrateur
- Connectez-vous avec le compte admin
- Accédez au tableau de bord
- Gérez les commandes, services et utilisateurs
- Configurez les paramètres du site
- Activez/désactivez le mode maintenance

## 🎨 Personnalisation

### Couleurs
La couleur primaire peut être modifiée via l'interface d'administration ou en éditant `config/config.php`.

### Thème
Le design utilise des variables CSS personnalisables dans `assets/css/style.css`.

### Services
Ajoutez vos propres services via l'interface d'administration ou en modifiant directement la base de données.

## 🔒 Sécurité

- **Sessions sécurisées** avec timeout configurable
- **Protection contre les attaques** par force brute
- **Validation des entrées** et protection XSS
- **Hachage sécurisé** des mots de passe
- **Uploads sécurisés** avec validation des types de fichiers
- **Logs de sécurité** pour le monitoring

## 📊 Maintenance

### Nettoyage automatique des logs
```sql
-- Nettoyer les logs de plus de 30 jours
CALL CleanupOldLogs(30);
```

### Sauvegarde de la base de données
```bash
mysqldump -u username -p smm_platform > backup_$(date +%Y%m%d_%H%M%S).sql
```

### Mise à jour
1. Sauvegardez votre base de données
2. Sauvegardez vos fichiers personnalisés
3. Remplacez les fichiers du projet
4. Exécutez les scripts de migration si nécessaire
5. Testez le fonctionnement

## 🐛 Dépannage

### Problèmes courants

#### Erreur de connexion à la base de données
- Vérifiez les paramètres dans `config/database.php`
- Assurez-vous que MySQL est démarré
- Vérifiez les permissions de l'utilisateur

#### Problèmes d'upload
- Vérifiez les permissions du dossier `uploads/`
- Vérifiez la taille maximale dans `php.ini`
- Vérifiez les types de fichiers autorisés

#### Emails non envoyés
- Vérifiez la configuration SMTP
- Vérifiez les logs d'erreur
- Testez avec un service SMTP externe (Gmail, SendGrid, etc.)

### Logs
- **Erreurs PHP** : `/var/log/apache2/error.log` ou `/var/log/nginx/error.log`
- **Logs système** : Table `system_logs` dans la base de données
- **Logs emails** : Table `email_logs` dans la base de données

## 📈 Performance

### Optimisations recommandées
- **Cache** : Activez le cache PHP OPcache
- **Base de données** : Optimisez les requêtes et ajoutez des index
- **Images** : Compressez et optimisez les images
- **CDN** : Utilisez un CDN pour les ressources statiques
- **Compression** : Activez la compression GZIP

## 🤝 Contribution

1. Fork le projet
2. Créez une branche pour votre fonctionnalité
3. Committez vos changements
4. Poussez vers la branche
5. Ouvrez une Pull Request

## 📄 Licence

Ce projet est sous licence MIT. Voir le fichier `LICENSE` pour plus de détails.

## 📞 Support

- **Email** : support@smmplatform.com
- **Documentation** : [Wiki du projet](https://github.com/votre-username/smm-platform/wiki)
- **Issues** : [GitHub Issues](https://github.com/votre-username/smm-platform/issues)

## 🙏 Remerciements

- Design inspiré des principes d'Apple
- Icônes et emojis pour l'interface
- Communauté open source PHP
- Contributeurs et testeurs

---

**SMM Platform** - Votre partenaire de confiance pour le marketing sur les réseaux sociaux 🚀