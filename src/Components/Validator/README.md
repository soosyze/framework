# ValidatorPipe
La class ValidatorPipe sert � valider le type de donn�es de variable et de g�rrer le retour sous forme de message.

## Sommaire
 * Fonctions
 * Initialisation
 * Usage

## Fonctions
### Fonctions de validations
`AlphaNum` Test si la valeur est Alpha num�rique [a-zA-Z0-9].
`AlphaNumText` Test si la valeur est alpha num�rique et poss�de des caract�res textueles [a-zA-Z0-9 .!?,;:_-].
`Array` Test si la valeur est de type array.
`Between` Test si une valeur est entre 2 valeurs de comparaison.
`Bool` Test si une valeur est de type boolean.
`Date` Test si une valeur est une date.
`DateAfter` Test si une date est ant�rieur � la date de comparaison.
`DateBefore` Test si une date est post�rieur � la date de comparaison.
`DateFormat` Test si une date correspond au format.
`Dir` Test si une valeur est un r�p�rtoire existant sur le serveur.
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
`Rexeg` Test si une valeur est �gale � une expression r�guli�re.
`Required` Test si une valeur est requise.
`Slug` Test si la valeur correspond � une chaine de caract�re alapha num�rique (underscore et tiret autoris�).
`String` Test si la valeur est une chaine de charact�re.
`URL` Test si une valeur est une url.
### Fonctions de filtrages
`Htmlsc` Filtre un input avec la m�thode htmlspecialchars
`StripTags` Filtre les balises autoris�es dans un input
## Initialisation
Pour initialiser le validateur une simple d�claration de class sufit.
```php
$validator = new FormValidator;
```
## Utilisation
Chaque fonction de validation est r�v�rsible avec le caract�re `!` en pr�fixe.
### Gestion des inputs
```php
/* D�claration d'un input */
$validator->addInput('keyInput', 'value');

/* D�claration de plusieurs inputs */
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
### Gestion des r�gles
```php
/* D�claration d'une r�gle */
$validator->addRule('keyInput', 'rules');

/* D�claration de plusieurs r�gles */
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
/* Retourne true si les r�gles sont respect�es ou flase en cas d'erreures */
$validator->isValide();
```
## Utilisation r�gles de validations
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