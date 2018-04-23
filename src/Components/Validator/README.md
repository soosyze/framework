# ValidatorPipe
La class ValidatorPipe sert à valider le type de données de variable et de gérrer le retour sous forme de message.

## Sommaire
 * Fonctions
 * Initialisation
 * Usage

## Fonctions
### Fonctions de validations
`AlphaNum` Test si la valeur est Alpha numérique [a-zA-Z0-9].
`AlphaNumText` Test si la valeur est alpha numérique et possède des caractères textueles [a-zA-Z0-9 .!?,;:_-].
`Array` Test si la valeur est de type array.
`Between` Test si une valeur est entre 2 valeurs de comparaison.
`Bool` Test si une valeur est de type boolean.
`Date` Test si une valeur est une date.
`DateAfter` Test si une date est antérieur à la date de comparaison.
`DateBefore` Test si une date est postérieur à la date de comparaison.
`DateFormat` Test si une date correspond au format.
`Dir` Test si une valeur est un répértoire existant sur le serveur.
`Equal` Test si 2 valeurs de test sont identiques. 
`Email` Test si une valeur est un mail.
`File` Test si la valeur est un fichier.
`Float` Test si une valeur est de type numerique flotant.
`InArray` Test si une valeur est contenu dans un tableau.
`Int` Test si une valeur est de type entier.
`IP` Test si une valeur est une adresse IP.
`JSON` Test si la valeur et de type json.
`Max` Test si une valeur est plus grande que la valeur de comparaison.
`Min` Test si une valeur est plus petite que la valeur de comparaison.
`Rexeg` Test si une valeur est égale à une expression régulière.
`Required` Test si une valeur est requise.
`Slug` Test si la valeur correspond à une chaine de caractère alapha numérique (underscore et tiret autorisé).
`String` Test si la valeur est une chaine de charactère.
`URL` Test si une valeur est une url.
### Fonctions de filtrages
`Htmlsc` Filtre un input avec la méthode htmlspecialchars
`StripTags` Filtre les balises autorisées dans un input
## Initialisation
Pour initialiser le validateur une simple déclaration de class sufit.
```php
$validator = new FormValidator;
```
## Utilisation
Chaque fonction de validation est révérsible avec le caractère `!` en préfixe.
### Gestion des inputs
```php
/* Déclaration d'un input */
$validator->addInput('keyInput', 'value');

/* Déclaration de plusieurs inputs */
$validator->addInputs([
	'keyInput1' => "Value1",
	'keyInput2' => "Value2"
]);

/* Si un input existe */
$validator->hasInput('keyInput');

/* Retourn la valeur de l'input */
$validator->getInput('keyInput');

/* Retourne un array d'inputs */
$validator->getInputs();
```
### Gestion des règles
```php
/* Déclaration d'une règle */
$validator->addRule('keyInput', 'rules');

/* Déclaration de plusieurs règles */
$validator->addRules([
	'keyInput1' => "rules",
	'keyInput2' => "rules
]);
```
### Gestion des erreurs
```php
/* Retourne si une erreur existe */
$validator->hasError();
```
### Validation
```php
/* Retourne true si les règles sont respectées ou flase en cas d'erreures */
$validator->isValide();
```
## Utilisation règles de validations
### Validation AlphaNum
```php
$validator->addInputs([
	'field_alpha_num'				=>"hello2000",
	'field_not_alpha_num'			=>'hello&2000@',
	'field_alpha_num_require'		=>"hello2000",
	'field_alpha_num_not_require'	=>""
])->addRules([
	'field_alpha_num'				=>'AlphaNum',
	'field_not_alpha_num'			=>'!AlphaNum',
	'field_alpha_num_require'		=>'require|AlphaNum',
	'field_alpha_num_not_require'	=>'!require|AlphaNum',
]);
```