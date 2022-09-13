# Soosyze Framework

[![Build Status](https://github.com/soosyze/framework/workflows/Tests/badge.svg?branch=master)](https://github.com/soosyze/framework/actions?query=branch:master "Tests")
[![Coverage Status](https://coveralls.io/repos/github/soosyze/framework/badge.svg?branch=master)](https://coveralls.io/github/soosyze/framework?branch=master "Coveralls")
[![GitHub](https://img.shields.io/github/license/mashape/apistatus.svg)](https://github.com/soosyze/framework/blob/master/LICENSE "LICENSE")
[![Packagist](https://img.shields.io/packagist/v/soosyze/framework.svg)](https://packagist.org/packages/soosyze/framework "Packagist")
[![PHP from Packagist](https://img.shields.io/packagist/php-v/soosyze/framework.svg)](#version-php)
![GitHub code size in bytes](https://img.shields.io/github/repo-size/soosyze/framework.svg)

Soosyze Framework est un micro-framework MVC object offrant un socle solide de développement

* [![PSR-2](https://img.shields.io/badge/PSR-2-yellow.svg)](https://www.php-fig.org/psr/psr-2 "Coding Style Guide") L'écriture du code est standardisée,
* [![PSR-4](https://img.shields.io/badge/PSR-4-yellow.svg)](https://www.php-fig.org/psr/psr-4 "Autoloading Standard") Autoloader, interchangeable avec l'autoloader de Composer,
* [![PSR-7](https://img.shields.io/badge/PSR-7-yellow.svg)](https://www.php-fig.org/psr/psr-7 "HTTP Message Interface") Composant Http (Resquest, Response, Message, Stream...),
  * À passé les tests https://github.com/php-http/psr7-integration-tests à 100%, rendant l'implémentation interopérable avec d'autres bibliothèques psr-7.
* [![PSR-11](https://img.shields.io/badge/PSR-11-yellow.svg)](https://www.php-fig.org/psr/psr-11 "Container Interface") Container d'injection de dépendance ou CID,
* [![PSR-17](https://img.shields.io/badge/PSR-17-yellow.svg)](https://www.php-fig.org/psr/psr-17 "HTTP Factories") Fabriques Http implémentées sans les interfaces qui contraignent les implémentations à PHP7,
* [![PSR-18](https://img.shields.io/badge/PSR-18-yellow.svg)](https://www.php-fig.org/psr/psr-18 "HTTP Client") Client Http implémentées sans les interfaces qui contraignent les implémentations à PHP7,
* Découpe des fonctionnalitées en modules,
* Routeur (url),
* Hook et Middleware,
* Contrôleur,
* Composant d'aide au développement
  * Création de formulaire,
  * Validateur de données,
  * Moteur de Template,
  * Création de Pagination.

# Sommaire

* [Requirements](#requirements)
* [Installation](#installation)
* [License](#license)

# Requirements

## Version PHP

| Version PHP     | SoosyzeFramework 3.x |
|-----------------|----------------------|
| <= 7.1          | ✗ Non supporté       |
| 7.2 / 7.3 / 7.4 | ✓ Supporté           |
| 8.0 / 8.1       | ✓ Supporté           |

## Extensions

* `curl` si vous utilisez le composant HttpClient.
* `date` si vous utilisez le composant Validator.
* `fileinfo` si vous utilisez le composant Validator.
* `filter` si vous utilisez le composant Validator.
* `gd` si vous utilisez le composant Validator.
* `json` si vous utilisez les composants Config ou Util.
* `mbstring` si vous utilisez le composant Util.
* `session` si vous utilisez les composants Validator ou FormBuilder.

Ces extensions sont généralement actives par défauts.

## Permission des fichiers et répértoire

La permission d'écrire et lire les fichiers.

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