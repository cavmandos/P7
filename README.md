# Projet 7 : BileMo

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/0955485c19e044dbab9f3b6c3c7332d4)](https://app.codacy.com/gh/cavmandos/P7/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade)

Une API pour la vente de mobiles.

## Table des matières

-  [Aperçu](#aperçu)
-  [Description](#description)
-  [Installation](#installation)

## Aperçu

Ce projet est effectué dans le cadre du parcours PHP/Symfony d'Openclassroom.

BileMo est une entreprise offrant toute une sélection de téléphones mobiles haut de gamme.

Vous êtes en charge du développement de la vitrine de téléphones mobiles de l’entreprise BileMo. Le business modèle de BileMo n’est pas de vendre directement ses produits sur le site web, mais de fournir à toutes les plateformes qui le souhaitent l’accès au catalogue via une API (Application Programming Interface). Il s’agit donc de vente exclusivement en B2B (business to business).

Il va falloir exposer un certain nombre d’API pour que les applications des autres plateformes web puissent effectuer des opérations.

## Description

Le premier client a enfin signé un contrat de partenariat avec BileMo ! C’est le branle-bas de combat pour répondre aux besoins de ce premier client qui va permettre de mettre en place l’ensemble des API et de les éprouver tout de suite.

Après une réunion dense avec le client, il a été identifié un certain nombre d’informations. Il doit être possible de :

-  consulter la liste des produits BileMo ;
-  consulter les détails d’un produit BileMo ;
-  consulter la liste des utilisateurs inscrits liés à un client sur le site web ;
-  consulter le détail d’un utilisateur inscrit lié à un client ;
-  ajouter un nouvel utilisateur lié à un client ;
-  supprimer un utilisateur ajouté par un client.

## Installation

Pour exécuter ce projet localement, suivez ces étapes simples :

1.  Avant de commencer, assurez-vous d'avoir les éléments suivants installés sur votre système :

-  [PHP](https://www.php.net/manual/en/install.php)
-  [Composer](https://getcomposer.org/download/)
-  [Symfony CLI](https://symfony.com/download)

2.  Ensuite, clonez le dépôt du projet en utilisant la commande suivante :

```bash 
git clone https://github.com/cavmandos/P7.git
```

3.  Installez les dépendances avec composer :

```bash
composer install
```

4.  Créer sa base de donnée (sans oublier de la relier à son projet dans le fichier .env) :

```bash
php bin/console doctrine:database:create
```

5.  Créer ses tables :

```bash
php bin/console doctrine:schema:update -f
```

6.  Lancer son serveur symfony :

```bash
symfony serve
```

7.  Ajouter le jeu de données :

```bash
php bin/console doctrine:fixtures:load
```

8.  Installer LexikJWT, créer ses clés publique/privée, puis configurer son fichier .env.local

9.  Pour tester l'API et obtenir un token :

"username":"admin@mail.com",
"password":"password"

ou

"username":"store1@mail.com",
"password":"password"