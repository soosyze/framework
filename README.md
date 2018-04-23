# Sommaire
* [Introduction](/README.md#introduction)
* [Requirements](/README.md#requirements)
* [Installation](/README.md#installation)
* [License](/README.md#license)

# Introduction

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

# Requirements

Vous devez avoir la version PHP 5.4 ou plus ainsi que l'extension json activé.

# Installation

## Composer

Ajouter les lignes suivantes à votre composer.json de votre projet

```json
"require": {
    "soosyze/framework": "^1.0"
},
"autoload": {
    "psr-4": {
        "Soosyze\\": "src"
    }
}
```

Ou simplement lancé la commande

```
composer require soosyze/framework
```

# License

Soosyze Framework est sous licence MIT. Voir le fichier de licence pour plus d'informations.