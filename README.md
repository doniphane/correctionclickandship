

---

# Projet Symfony – Démarrage from scratch

Ce projet a été initialisé **sans template** (`--webapp` non utilisé), avec Symfony et les principaux bundles pour développer une API robuste et sécurisée.

## Stack technique installée

* **Symfony** (installé manuellement, sans template)
* **Doctrine ORM** : Gestion de la base de données
* **API Platform** : Création rapide d’API REST et GraphQL
* **MakerBundle** : Génération rapide d’entités, contrôleurs, etc.
* **SecurityBundle** : Gestion des utilisateurs, authentification & autorisation
* **JWT Auth** : Authentification sécurisée par token JSON Web Token
* **CORS (NelmioCorsBundle)** : Acceptation des requêtes cross-origin (API front/back séparés)
* **Stripe SDK** : Paiements en ligne avec Stripe
* **Fixtures** : Création de données de test pour le développement
* **VichUploaderBundle** : Upload et gestion de fichiers (images, documents...)

---

## Installation des dépendances

Ouvre ton terminal à la racine du projet et exécute les commandes suivantes :

```bash
# Doctrine ORM : Interagir avec la base de données
composer require symfony/orm-pack

# API Platform : Créer facilement une API REST/GraphQL
composer require api

# MakerBundle : Générer du code (entités, contrôleurs, etc.)
composer require --dev symfony/maker-bundle

# SecurityBundle : Authentification et gestion des rôles
composer require symfony/security-bundle

# LexikJWT : Authentification par JWT (token sécurisé)
composer require lexik/jwt-authentication-bundle

# NelmioCorsBundle : Gérer le CORS (Cross-Origin Resource Sharing)
composer require nelmio/cors-bundle

# Stripe PHP SDK : Paiements Stripe
composer require stripe/stripe-php

# Fixtures : Générer des données de test en base
composer require --dev doctrine/doctrine-fixtures-bundle

# VichUploaderBundle : Upload et gestion de fichiers
composer require vich/uploader-bundle
```

---

## Résumé de l’architecture

* **Pas de template web** : tout a été initialisé manuellement pour un contrôle total.
* **API-first** : Le projet est principalement orienté API, idéal pour un front React/Vue ou une architecture SPA/mobile.
* **Sécurité** : Authentification JWT, gestion des rôles, CORS strict.
* **Gestion avancée** : Upload de fichiers, paiements Stripe, et génération de données de test.

---

## Pour démarrer

1. **Cloner ce dépôt**
2. Installer les dépendances :

   ```bash
   composer install
   ```
3. Configurer `.env` (BDD, clés Stripe, JWT, etc.)
4. **Générer la paire de clés JWT** :

   ```bash
   php bin/console lexik:jwt:generate-keypair
   ```
5. Créer la base de données :

   ```bash
   php bin/console doctrine:database:create
   ```
6. Lancer les migrations :

   ```bash
   php bin/console doctrine:migrations:migrate
   ```
7. (Optionnel) Générer des fixtures :

   ```bash
   php bin/console doctrine:fixtures:load
   ```

---

## Notes

* **Aucune dépendance frontend** incluse (pas de Twig, pas de React, pas de Vue dans ce repo).
* Ce projet est prévu pour être utilisé en back pur (API), mais reste extensible.

---


