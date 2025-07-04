Projet Symfony de Zero sans template et --webapp

#L'ORM Doctrine pour interagir avec la base de données
composer require symfony/orm-pack

#Installe API Platform et ses dépendances
composer require api

#Installe le MakerBundle pour générer du code (entités, contrôleurs, etc.)
composer require --dev symfony/maker-bundle

#Installe le bundle de sécurité pour gérer l'authentification et les autorisations
composer require symfony/security-bundle

#Installe le bundle pour l'authentification par token JWT
composer require lexik/jwt-authentication-bundle

#Installe le bundle pour gérer les requêtes Cross-Origin (CORS)
composer require nelmio/cors-bundle

#Installe le SDK PHP officiel de Stripe pour interagir avec leur API
composer require stripe/stripe-php

#Installe le bundle pour créer des données de test (fixtures)
composer require --dev doctrine/doctrine-fixtures-bundle

#Installe le bundle pour gérer les fichiers
composer require vich/uploader-bundle
