# Soosyze Framework

[![Build Status](https://travis-ci.org/soosyze/framework.svg?branch=master)](https://travis-ci.org/soosyze/framework)
[![Coverage Status](https://coveralls.io/repos/github/soosyze/framework/badge.svg?branch=master)](https://coveralls.io/github/soosyze/framework?branch=master)
![GitHub](https://img.shields.io/github/license/mashape/apistatus.svg)
![Packagist](https://img.shields.io/packagist/v/soosyze/framework.svg)
![PHP from Packagist](https://img.shields.io/packagist/php-v/soosyze/framework.svg)
![GitHub code size in bytes](https://img.shields.io/github/languages/code-size/soosyze/framework.svg)

Soosyze Framework est un micro-framework MVC object offrant un socle solide de développement

* Découpe des fonctionnalitées en modules,
* Routeur (url),
* Container d'injection de dépendance ou CID (PSR-11),
* Hook et Middleware,
* Autoloader, interchangeable avec l'autoloader de compsoser (PSR-04)
* Controlleur,
* Requête et Réponse (PSR-07)
* Composant d'aide au développement
    * Création de formulaire,
    * Validateur de données,
    * Envoie de Mail,
    * Création de Templace.

# Sommaire

* [Requirements](/README.md#requirements)
* [Installation](/README.md#installation)
* [License](/README.md#license)

# Requirements

* PHP =>5.4, support PHP 5.6, 7.0, 7.1
* La permission d'écrire et lire les fichiers (Si vous utilisez le composant Util),
* L'extension `json` activé (Si vous utilisez le composant Util).


# Installation

## Composer

Vous pouvez utiliser [Composer](https://getcomposer.org/) pour l'installation avec la commande suivante :
```sh
composer require soosyze/framework
```

Ou, si vous utilisez le PHAR (assurez-vous que l'exécutable php.exe est dans votre PATH):
```sh
php composer.phar require soosyze/framework
```

# License

Soosyze Framework est sous licence MIT. Voir le fichier de licence pour plus d'informations.