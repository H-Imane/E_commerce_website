# 🛒 My Perfume – Plateforme e-commerce Web

> Projet e-commerce complet (frontend + backend) pour la vente de parfums en ligne.

---

## 📖 Description

My Perfume est une application web e-commerce complète, développée pour proposer une expérience d'achat de parfums en ligne fluide et sécurisée. Le projet s'articule autour de deux espaces distincts : une interface client intuitive permettant de parcourir le catalogue, gérer son panier et son compte, et un back-office administrateur offrant un dashboard, la gestion complète des produits, des commandes et des exports CSV.
---

## 📁 Structure du projet

```
E_commerce_website/
├── frontend/                 # Frontend (PHP + assets)
│   ├── components/           # Composants réutilisables (navbar, product…)
│   ├── admin/               # Pages admin (dashboard, produits, commandes, clients…)
│   ├── assets/
│   │   ├── css/            # Styles (admin, client, dashboard…)
│   │   ├── js/             # Scripts (navbar dynamique, filtres, modals…)
│   │   └── images/         # Images produits, bannières…
│   ├── style.css           # Style principal du site
│   ├── script.js           # Scripts principaux (menu mobile…)
│   ├── index.php           # Accueil
│   ├── products.php        # Collections
│   ├── cart.php            # Panier (frontend-only)
│   ├── login.php           # Connexion utilisateur
│   ├── logout.php          # Déconnexion
│   └── hash.php            # Générateur de hash bcrypt
├── backend/
│   └── api/                # API PHP (produits, auth…)
├── projet/
│   └── queries.txt         # Schéma SQL + données de test
└── README.md
```

---

## 🚀 Installation rapide (XAMPP)

### 1️⃣ Prérequis
- XAMPP (Apache + MySQL)
- Navigateur web

### 2️⃣ Cloner / Télécharger
```bash
git clone https://github.com/[TON_PSEUDO]/E_commerce_website.git
# ou télécharge et dézippe le dossier
```

### 3️⃣ Placer dans XAMPP
- Déplace `E_commerce_website` dans `C:/xampp/htdocs/` (Windows) ou `/opt/lampp/htdocs/` (Linux)

### 4️⃣ Base de données
1. Lance XAMPP → Apache + MySQL
2. Ouvre `http://localhost/phpmyadmin`
3. Crée la base `db_ecommerce`
4. Importe `create_db.sql` ou exécute `projet/queries.txt`

### 5️⃣ Accès
- **Site client** : `http://localhost/E_commerce_website/frontend/`
- **Admin** : `http://localhost/E_commerce_website/frontend/admin/`

---

#
---

## 🔧 Générer un hash bcrypt (admin)

1. Crée `frontend/hash.php` :
```php
<?php
echo password_hash('admin123', PASSWORD_DEFAULT);
```

2. Ouvre `http://localhost/E_commerce_website/frontend/hash.php`  
3. Copie le hash et mets à jour dans phpMyAdmin :
```sql
UPDATE users
SET hashed_password = 'COLLE_ICI_LE_HASH'
WHERE email = 'admin@hora.com';
```

---

## 🛠️ Fonctionnalités

### 🌐 Frontend (client)
- **Accueil** : Hero, collections, présentation
- **Catalogue** : Grille produits, filtres, recherche
- **Panier** : Ajout/suppression, total, responsive
- **Comptes** : Login, logout, session PHP
- **Responsive** : Menu hamburger, mobile-first

### 🛡️ Admin
- **Dashboard** : KPIs, dernières commandes, liens rapides
- **Produits** : CRUD complet, grille, modal, recherche
- **Catégories & Marques** : Gestion des taxonomies
- **Commandes** : Statuts, détails, export CSV
- **Clients** : Liste, détails, statut, export CSV
- **Navbar admin** : Style “topbar” unifié avec le client

---

## 📦 Stack technique

| Couche      | Technologie                     |
|-------------|---------------------------------|
| Frontend    | PHP, HTML5, CSS3, JavaScript (vanilla)+AJAX |
| Backend     | PHP (API JSON)                  |
| Base de données | MySQL (db_ecommerce)        |
| Serveur     | XAMPP (Apache + MySQL)          |
| Styles      | CSS custom + Playfair Display + Inter |
| Images      | Fichiers dans `assets/images/` (nom en BDD) |
| Cookies     | Panier non-auth, token remember |

---


---

## 🚀 Ce que j’ai proposé en plus

1. **Navbar admin “topbar”** unifiée avec le style client
2. **Composants réutilisables** (`navbar.php`, `admin_navbar.php`, `product.php`)
3. **Dashboard admin** avec KPIs et liens rapides
4. **Exports CSV** pour commandes et clients
5. **Modals interactifs** pour détails produits/commandes/clients
6. **Recherche + filtres** en temps réel (JavaScript)
7. **Documentation complète** (README, instructions d’installation)
8. **Gestion authentification** (login, logout, session)

---

## 📄 Licence

Projet réalisé dans le cadre d’un TP/formation.  
Tu peux réutiliser et adapter ce code librement.

---

## 🤝 Contribuer

1. Fork le projet
2. Crée une branche (`git checkout -b feature/AmazingFeature`)
3. Commit (`git commit -m 'Add AmazingFeature'`)
4. Push (`git push origin feature/AmazingFeature`)
5. Ouvre une Pull Request
 
---

**👉 Pour toute question ou suggestion, n’hésite pas à ouvrir une issue !**
