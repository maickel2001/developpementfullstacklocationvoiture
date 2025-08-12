# ♿ Guide de Test d'Accessibilité

## 🎯 Objectifs des tests d'accessibilité

### 1. Standards de référence
- **WCAG 2.1** : Niveau AA minimum
- **Section 508** : Accessibilité fédérale US
- **RGAA** : Référentiel Général d'Amélioration de l'Accessibilité
- **EN 301 549** : Standard européen

### 2. Principes fondamentaux
- **Perceptible** : Information présentable de manière perceptible
- **Utilisable** : Interface utilisable par tous
- **Compréhensible** : Information et interface compréhensibles
- **Robuste** : Contenu interprétable par les technologies

## 🛠️ Outils de test d'accessibilité

### 1. Outils gratuits
- **axe DevTools** : Extension navigateur
- **WAVE** : Web Accessibility Evaluation Tool
- **Lighthouse** : Audit d'accessibilité intégré
- **Colour Contrast Analyser** : Test des contrastes

### 2. Outils payants
- **Deque axe** : Tests automatisés
- **SiteImprove** : Tests complets
- **Accessibility Insights** : Tests Microsoft
- **Tenon.io** : API de test

## 📋 Tests d'accessibilité à effectuer

### 1. Tests de navigation au clavier

#### 1.1 Navigation par tabulation
```javascript
// Test de navigation au clavier
describe('Keyboard Navigation', () => {
    it('should be navigable by keyboard', () => {
        // Vérifier l'ordre de tabulation
        const focusableElements = document.querySelectorAll(
            'a, button, input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        
        expect(focusableElements.length).toBeGreaterThan(0);
        
        // Vérifier que le premier élément peut recevoir le focus
        focusableElements[0].focus();
        expect(document.activeElement).toBe(focusableElements[0]);
    });
    
    it('should have logical tab order', () => {
        // L'ordre de tabulation doit être logique
        const expectedOrder = [
            '#search-input',
            '#search-button',
            '.nav-link',
            '.card-link'
        ];
        
        expectedOrder.forEach((selector, index) => {
            const element = document.querySelector(selector);
            if (element) {
                expect(element.tabIndex).toBe(index + 1);
            }
        });
    });
});
```

#### 1.2 Indicateurs de focus
```css
/* Test des indicateurs de focus */
:focus {
    outline: 2px solid #007bff;
    outline-offset: 2px;
}

/* Focus visible pour tous les éléments interactifs */
.btn:focus,
.form-control:focus,
.nav-link:focus {
    outline: 2px solid #007bff;
    outline-offset: 2px;
    box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
}
```

### 2. Tests d'alternatives textuelles

#### 2.1 Images avec alt
```html
<!-- Test des alternatives textuelles -->
<img src="car.jpg" alt="Toyota Corolla - Berline compacte bleue" />

<!-- Images décoratives -->
<img src="decoration.jpg" alt="" role="presentation" />

<!-- Images complexes -->
<img src="chart.jpg" alt="Graphique des ventes 2024" longdesc="chart-description.html" />
```

#### 2.2 Test des alternatives
```javascript
// Test des alternatives textuelles
describe('Text Alternatives', () => {
    it('should have alt text for images', () => {
        const images = document.querySelectorAll('img');
        
        images.forEach(img => {
            // Les images doivent avoir un attribut alt
            expect(img.hasAttribute('alt')).toBe(true);
            
            // Les images décoratives peuvent avoir alt=""
            if (img.role === 'presentation') {
                expect(img.alt).toBe('');
            } else {
                // Les images informatives doivent avoir un alt descriptif
                expect(img.alt.trim()).not.toBe('');
            }
        });
    });
    
    it('should have descriptive alt text', () => {
        const informativeImages = document.querySelectorAll('img:not([role="presentation"])');
        
        informativeImages.forEach(img => {
            const alt = img.alt.trim();
            expect(alt.length).toBeGreaterThan(0);
            expect(alt).not.toBe('image');
            expect(alt).not.toBe('photo');
        });
    });
});
```

### 3. Tests de contraste des couleurs

#### 3.1 Ratios de contraste
```css
/* Test des contrastes WCAG 2.1 AA */
/* Texte normal : ratio minimum 4.5:1 */
.text-normal {
    color: #333333; /* Contraste 12.6:1 sur blanc */
}

/* Texte large : ratio minimum 3:1 */
.text-large {
    color: #666666; /* Contraste 4.5:1 sur blanc */
    font-size: 18px;
    font-weight: bold;
}

/* Liens : ratio minimum 4.5:1 */
a {
    color: #0056b3; /* Contraste 7:1 sur blanc */
}

a:hover {
    color: #003d82; /* Contraste 12.6:1 sur blanc */
}
```

#### 3.2 Test des contrastes
```javascript
// Test des contrastes de couleurs
describe('Color Contrast', () => {
    it('should meet WCAG AA standards', () => {
        // Test des contrastes de texte
        const textElements = document.querySelectorAll('p, h1, h2, h3, h4, h5, h6, span, div');
        
        textElements.forEach(element => {
            const computedStyle = window.getComputedStyle(element);
            const color = computedStyle.color;
            const backgroundColor = computedStyle.backgroundColor;
            
            // Calculer le ratio de contraste
            const contrastRatio = calculateContrastRatio(color, backgroundColor);
            
            // Vérifier le ratio minimum selon la taille du texte
            const fontSize = parseInt(computedStyle.fontSize);
            const fontWeight = computedStyle.fontWeight;
            
            let minRatio;
            if (fontSize >= 18 || (fontSize >= 14 && fontWeight >= 700)) {
                minRatio = 3.0; // Texte large
            } else {
                minRatio = 4.5; // Texte normal
            }
            
            expect(contrastRatio).toBeGreaterThanOrEqual(minRatio);
        });
    });
});

// Fonction de calcul du ratio de contraste
function calculateContrastRatio(color1, color2) {
    // Implémentation du calcul WCAG
    // Retourne le ratio de contraste
    return 4.5; // Exemple
}
```

### 4. Tests de structure sémantique

#### 4.1 Hiérarchie des titres
```html
<!-- Test de la hiérarchie des titres -->
<h1>Location de Voitures</h1>
<h2>Nos Véhicules</h2>
<h3>Toyota Corolla</h3>
<h2>Réserver</h2>
<h3>Informations personnelles</h3>
```

#### 4.2 Test de la structure
```javascript
// Test de la structure sémantique
describe('Semantic Structure', () => {
    it('should have proper heading hierarchy', () => {
        const headings = document.querySelectorAll('h1, h2, h3, h4, h5, h6');
        let previousLevel = 0;
        
        headings.forEach(heading => {
            const currentLevel = parseInt(heading.tagName.charAt(1));
            
            // Vérifier que les niveaux sont logiques
            expect(currentLevel - previousLevel).toBeLessThanOrEqual(1);
            previousLevel = currentLevel;
        });
    });
    
    it('should have only one h1', () => {
        const h1Elements = document.querySelectorAll('h1');
        expect(h1Elements.length).toBe(1);
    });
});
```

### 5. Tests de formulaires

#### 5.1 Labels et associations
```html
<!-- Test des labels de formulaires -->
<div class="form-group">
    <label for="nom">Nom complet *</label>
    <input type="text" id="nom" name="nom" required />
</div>

<div class="form-group">
    <label for="email">Email *</label>
    <input type="email" id="email" name="email" required />
</div>

<!-- Groupes de champs -->
<fieldset>
    <legend>Opérateur de paiement</legend>
    <input type="radio" id="mtn" name="operateur" value="MTN" />
    <label for="mtn">MTN Mobile Money</label>
    
    <input type="radio" id="moov" name="operateur" value="Moov" />
    <label for="moov">Moov Money</label>
</fieldset>
```

#### 5.2 Test des formulaires
```javascript
// Test de l'accessibilité des formulaires
describe('Form Accessibility', () => {
    it('should have proper labels', () => {
        const inputs = document.querySelectorAll('input, select, textarea');
        
        inputs.forEach(input => {
            // Vérifier que chaque input a un label
            const label = document.querySelector(`label[for="${input.id}"]`);
            expect(label).toBeTruthy();
            
            // Vérifier que le label est descriptif
            if (label) {
                expect(label.textContent.trim()).not.toBe('');
            }
        });
    });
    
    it('should have proper error messages', () => {
        const requiredInputs = document.querySelectorAll('[required]');
        
        requiredInputs.forEach(input => {
            // Vérifier que les champs requis ont des messages d'erreur
            const errorMessage = input.getAttribute('aria-describedby');
            if (errorMessage) {
                const errorElement = document.getElementById(errorMessage);
                expect(errorElement).toBeTruthy();
            }
        });
    });
});
```

### 6. Tests de navigation

#### 6.1 Menu de navigation
```html
<!-- Test de la navigation accessible -->
<nav aria-label="Navigation principale">
    <ul>
        <li><a href="index.php" aria-current="page">Accueil</a></li>
        <li><a href="reservation.php">Réserver</a></li>
        <li><a href="admin/">Administration</a></li>
    </ul>
</nav>

<!-- Menu mobile -->
<button class="navbar-toggler" 
        aria-expanded="false" 
        aria-controls="navbarNav" 
        aria-label="Basculer la navigation">
    <span class="navbar-toggler-icon"></span>
</button>
```

#### 6.2 Test de la navigation
```javascript
// Test de la navigation accessible
describe('Navigation Accessibility', () => {
    it('should have proper navigation landmarks', () => {
        // Vérifier la présence de landmarks
        expect(document.querySelector('nav')).toBeTruthy();
        expect(document.querySelector('main')).toBeTruthy();
        expect(document.querySelector('footer')).toBeTruthy();
    });
    
    it('should have skip links', () => {
        // Vérifier la présence de liens de saut
        const skipLinks = document.querySelectorAll('a[href^="#"]');
        let hasSkipLink = false;
        
        skipLinks.forEach(link => {
            if (link.textContent.toLowerCase().includes('skip') || 
                link.textContent.toLowerCase().includes('passer')) {
                hasSkipLink = true;
            }
        });
        
        expect(hasSkipLink).toBe(true);
    });
});
```

## 📊 Rapport d'accessibilité

### 1. Template de rapport
```markdown
# Rapport de Test d'Accessibilité
Date: [DATE]
Testeur: [NOM]
Version: [VERSION]
Standards: WCAG 2.1 AA

## Résumé exécutif
- Critères testés: X/50
- Critères conformes: X
- Critères non conformes: X
- Niveau d'accessibilité: [A/AA/AAA]

## Résultats par critère

### 1.1 Images (Critère 1.1)
- **Statut**: ✅ Conforme
- **Description**: Toutes les images ont des alternatives textuelles
- **Commentaire**: Aucun problème détecté

### 1.2 Titres (Critère 1.3)
- **Statut**: ❌ Non conforme
- **Description**: Hiérarchie des titres incorrecte
- **Impact**: Navigation difficile pour les lecteurs d'écran
- **Recommandation**: Corriger l'ordre des titres

## Problèmes identifiés

### 1. [PROBLÈME]
- **Critère WCAG**: [NUMÉRO]
- **Sévérité**: [CRITIQUE/ÉLEVÉE/MOYENNE/FAIBLE]
- **Description**: [DESCRIPTION]
- **Impact**: [IMPACT]
- **Solution**: [SOLUTION]
- **Priorité**: [PRIORITÉ]

## Recommandations
1. [RECOMMANDATION 1]
2. [RECOMMANDATION 2]
3. [RECOMMANDATION 3]
```

### 2. Métriques d'accessibilité
```yaml
# Métriques d'accessibilité
accessibility_metrics:
  wcag_compliance:
    level_a: 95%
    level_aa: 90%
    level_aaa: 85%
  
  criteria:
    perceivable: 92%
    operable: 88%
    understandable: 95%
    robust: 90%
  
  specific_tests:
    keyboard_navigation: 100%
    screen_reader: 95%
    color_contrast: 88%
    form_labels: 100%
    image_alt: 100%
    heading_structure: 85%
```

## 🚨 Gestion des problèmes d'accessibilité

### 1. Processus de correction
1. **Identification** : Problème détecté
2. **Analyse** : Impact sur l'accessibilité
3. **Correction** : Solution développée
4. **Test** : Validation avec outils et utilisateurs
5. **Déploiement** : Mise en production
6. **Vérification** : Test post-correction

### 2. Priorisation
- **P0** : Problèmes critiques (site inaccessible)
- **P1** : Problèmes majeurs (navigation impossible)
- **P2** : Problèmes moyens (contenu difficile d'accès)
- **P3** : Problèmes mineurs (améliorations)

### 3. Solutions communes
```html
<!-- Solutions d'accessibilité -->

<!-- Skip link -->
<a href="#main-content" class="skip-link">Passer au contenu principal</a>

<!-- ARIA labels -->
<button aria-label="Fermer la modal" class="btn-close">×</button>

<!-- Live regions -->
<div aria-live="polite" aria-atomic="true" id="notifications">
    <!-- Notifications dynamiques -->
</div>

<!-- Landmarks -->
<main id="main-content" role="main">
    <!-- Contenu principal -->
</main>

<aside role="complementary" aria-label="Informations complémentaires">
    <!-- Contenu secondaire -->
</aside>
```

## 📋 Checklist de test d'accessibilité

- [ ] Tests de navigation au clavier effectués
- [ ] Tests d'alternatives textuelles effectués
- [ ] Tests de contraste des couleurs effectués
- [ ] Tests de structure sémantique effectués
- [ ] Tests de formulaires effectués
- [ ] Tests de navigation effectués
- [ ] Tests avec lecteurs d'écran effectués
- [ ] Tests de zoom effectués
- [ ] Tests de contraste effectués
- [ ] Problèmes documentés
- [ ] Corrections implémentées
- [ ] Tests de validation effectués
- [ ] Rapport final rédigé

## 🎯 Objectifs d'accessibilité

### Niveau actuel
- **WCAG 2.1 A** : 95%
- **WCAG 2.1 AA** : 90%
- **WCAG 2.1 AAA** : 85%
- **Navigation clavier** : 100%
- **Lecteurs d'écran** : 95%

### Objectifs cibles
- **WCAG 2.1 A** : 100%
- **WCAG 2.1 AA** : 100%
- **WCAG 2.1 AAA** : 95%
- **Navigation clavier** : 100%
- **Lecteurs d'écran** : 100%

---

**L'accessibilité est un droit, pas une option ! ♿**