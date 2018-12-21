# Soosyze Framework

[![Build Status](https://travis-ci.org/soosyze/framework.svg?branch=master)](https://travis-ci.org/soosyze/framework)
[![Coverage Status](https://coveralls.io/repos/github/soosyze/framework/badge.svg?branch=master)](https://coveralls.io/github/soosyze/framework?branch=master)
![GitHub](https://img.shields.io/github/license/mashape/apistatus.svg)
![Packagist](https://img.shields.io/packagist/v/soosyze/framework.svg)
![PHP from Packagist](https://img.shields.io/packagist/php-v/soosyze/framework.svg)
![GitHub code size in bytes](https://img.shields.io/github/languages/code-size/soosyze/framework.svg)

Soosyze Framework est un micro-framework MVC object offrant un socle solide de développement

* ![PSR-2](https://img.shields.io/badge/PSR-2-yellow.svg) L'écriture du code est standardisé,
* ![PSR-4](https://img.shields.io/badge/PSR-4-yellow.svg) Autoloader, interchangeable avec l'autoloader de compsoser,
* ![PSR-7](https://img.shields.io/badge/PSR-7-yellow.svg) Requête et Réponse,
* ![PSR-11](https://img.shields.io/badge/PSR-11-yellow.svg) Container d'injection de dépendance ou CID,
* ![PSR-17](https://img.shields.io/badge/PSR-17-yellow.svg) Fabriques Http implémentées sans les interfaces qui contraignent les implémentations à PHP7,
* Découpe des fonctionnalitées en modules,
* Routeur (url),
* Hook et Middleware,
* Controlleur,
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