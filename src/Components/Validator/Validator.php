<?php

/**
 * Soosyze Framework http://soosyze.com
 * 
 * @package Soosyze\Components\Validator
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator;

/**
 * Valide des valeurs à partir de tests chaînés.
 * 
 * @author Mathieu NOËL
 */
class Validator
{
    /**
     * Règles de validations.
     * 
     * @var array 
     */
    protected $rules = [];

    /**
     * Champs à tester.
     * 
     * @var array 
     */
    protected $inputs = [];

    /**
     * Valeurs de retour.
     * 
     * @var array 
     */
    protected $return = [];

    /**
     * Clé unique des champs.
     * 
     * @var string[] 
     */
    protected $keyUniqueReturn = [];

    /**
     * Tests personnalisés par l'utilisateur.
     * 
     * @var callable[]
     */
    protected $test = [];

    /**
     * Lance les fonctions de validation à partir du type de test.
     * Exemple : L'appel avec le mot clé 'int' lancera la fonction valideInt.
     *
     * @param string $name Nom de la fonction de validation.
     * @param array $arguments Arguments de tests.
     *
     * @return mixed
     *
     * @throws \BadMethodCallException La fonction n'existe pas.
     */
    public function __call( $name, $arguments )
    {
        $func = 'valid' . $name;
        if( isset($this->test[ $func ]) )
        {
            return call_user_func_array($this->test[ $func ], $arguments);
        }
        else if( method_exists($this, $func) )
        {
            return call_user_func_array([ $this, $func ], $arguments);
        }
        throw new \BadMethodCallException('The '
        . htmlspecialchars($name)
        . ' function does not exist.'
        );
    }

    /**
     * Ajoute un champ à tester.
     * 
     * @param string $key Nom du champ.
     * @param mixed $value Valeur du champ.
     * 
     * @return $this
     */
    public function addInput( $key, $value )
    {
        $this->inputs[ $key ] = $value;
        if( $diff                 = array_diff_key($this->rules, $this->inputs) )
        {
            foreach( $diff as $key => $value )
            {
                $this->inputs[ $key ] = '';
            }
        }
        return $this;
    }

    /**
     * Rajoute une règle de validation.
     * 
     * @codeCoverageIgnore add
     * 
     * @param string $key Nom de du champ.
     * @param string $rule Règles à suivre.
     * 
     * @return $this
     */
    public function addRule( $key, $rule )
    {
        $this->rules[ $key ] = $rule;
        return $this;
    }

    /**
     * Ajoute un test personnalisé.
     * 
     * @param string $key Clé du test.
     * @param callable $callback Function de test.
     * 
     * @return $this
     */
    public function addTest( $key, callable $callback )
    {
        $this->test[ 'valid' . $key ] = $callback;
        return $this;
    }

    /**
     * Retourne une erreur à partir de son nom.
     * 
     * @codeCoverageIgnore getter
     * 
     * @param string $key Nom de l'erreur.
     * 
     * @return string
     */
    public function getError( $key )
    {
        return $this->return[ $key ];
    }

    /**
     * Retourne toutes les erreurs.
     * 
     * @codeCoverageIgnore getter
     * 
     * @return array
     */
    public function getErrors()
    {
        return $this->return;
    }

    /**
     * Retourn un champ.
     * 
     * @codeCoverageIgnore getter
     * 
     * @param string $key Nom du champ.
     * 
     * @return array Paramêtres du champ.
     */
    public function getInput( $key )
    {
        return $this->inputs[ $key ];
    }

    /**
     * Retourne les champs.
     * 
     * @codeCoverageIgnore getter
     * 
     * @return array Paramêtres des champs.
     */
    public function getInputs()
    {
        return $this->inputs;
    }

    /**
     * La liste de la concaténation des noms de champs et erreurs.
     * 
     * @codeCoverageIgnore getter
     * 
     * @return array
     */
    public function getKeyErrors()
    {
        return array_keys($this->return);
    }

    /**
     * La liste des noms de champ pour lesquels il y a une erreur.
     * 
     * @codeCoverageIgnore getter
     * 
     * @return array
     */
    public function getKeyUniqueErrors()
    {
        return $this->keyUniqueReturn;
    }

    /**
     * Si une erreur existe.
     * 
     * @codeCoverageIgnore has
     * 
     * @param string $key Nom de l'erreur.
     * 
     * @return bool
     */
    public function hasError( $key )
    {
        return isset($this->return[ $key ]);
    }

    /**
     * Si il y a eu des erreurs.
     * 
     * @codeCoverageIgnore has
     * 
     * @return bool
     */
    public function hasErrors()
    {
        return !empty($this->return);
    }

    /**
     * Si le champ existe.
     * 
     * @codeCoverageIgnore has
     * 
     * @param string $key Nom du champ.
     * 
     * @return bool
     */
    public function hasInput( $key )
    {
        return isset($this->inputs[ $key ]);
    }

    /**
     * Si le champ est requis.
     * 
     * @codeCoverageIgnore is
     * 
     * @param string $key Nom du champ.
     * 
     * @return bool
     */
    public function isRequire( $key )
    {
        return strstr($this->rules[ $key ], 'require');
    }

    /**
     * Lance les tests
     *
     * @return bool Si le test à réussit.
     */
    public function isValid()
    {
        /* Parcours les règles */
        foreach( $this->rules as $key => $test )
        {
            /* Si la valeur n'est pas vide. */
            if( $this->inputs[ $key ] === '' )
            {
                if( preg_match('/!required|bool/', $this->rules[ $key ]) )
                {
                    continue;
                }
                $this->addReturn("$key.required", $this->inputs[ $key ], 'la valeur de %s n\'est pas valide ');
            }

            /* Pour chaque règle cherche les fonctions séparées par un pipe. */
            foreach( explode('|', $test) as $func )
            {
                $arg = substr(strrchr($func, ":"), 1);

                /* Retire le caractère de négation de la fonction. */
                $function = $func[ 0 ] == '!'
                    ? substr(strrchr($func, "!"), 1)
                    : $func;

                /* Si la fonction à des arguments. */
                if( $arg !== false )
                {
                    /* Sépare le nom de la fonction un argument. */
                    $function = strstr($function, ":", true);


                    $keyMsg = "$key.$function";

                    /* Si l'argument fait référence à un autre champ. */
                    if( $arg[ 0 ] == '@' )
                    {
                        $keyArg = substr(strrchr($arg, "@"), 1);
                        $arg    = $this->inputs[ $keyArg ];
                    }

                    $this->$function($keyMsg, $this->inputs[ $key ], $arg, $func[ 0 ] != '!');
                }
                else
                {
                    $keyMsg = "$key.$function";

                    $this->$function($keyMsg, $this->inputs[ $key ], $func[ 0 ] != '!');
                }
            }
        }

        return empty($this->return);
    }

    /**
     * Ajoute les champs à tester.
     * 
     * @param array $fields Liste des champs.
     *
     * @return $this
     */
    public function setInputs( array $fields )
    {
        $this->inputs = $fields;
        /* Si les régles contiennes plus de champs que les champs reçut */
        if( $diff         = array_diff_key($this->rules, $this->inputs) )
        {
            foreach( $diff as $key => $value )
            {
                $this->inputs[ $key ] = '';
            }
        }
        return $this;
    }

    /**
     * Modifie un message d'erreur.
     * 
     * @param array $msg Liste des messages.
     * 
     * @return $this
     */
    public function setMessages( array $msg )
    {
        foreach( $msg as $key => $value )
        {
            $this->return[ $key ] = $value;
        }
        return $this;
    }

    /**
     * Ajoute les règles de validation.
     * 
     * @codeCoverageIgnore setter
     * 
     * @param array $rules Règles de validation.
     * 
     * @return $this
     */
    public function setRules( array $rules )
    {
        $this->rules = $rules;
        return $this;
    }

    /**
     * Teste si une valeur est comprise entre 2 valeurs numériques.
     * 
     * @param string $key Identifiant de la valeur.
     * @param string $value Valeur à tester.
     * @param int|float $lengthValue Valeur de la taille.
     * @param int|float $min Valeur minimum.
     * @param int|float $max Valeur maximum.
     * @param bool $not Inverse le test.
     */
    protected function sizeBetween( $key, $value, $lengthValue, $min, $max,
        $not = true )
    {
        if( !($lengthValue <= $max && $lengthValue >= $min) && $not )
        {
            $this->addReturn($key, $value, 'La valeur de %s est trop grande.');
        }
        else if( $lengthValue <= $max && $lengthValue >= $min && !$not )
        {
            $this->addReturn($key, $value, 'La valeur de %s est trop petite.');
        }
    }

    /**
     * Test si une valeur est plus grande que la valeur de comparaison.
     * 
     * @param string $key Identifiant de la valeur.
     * @param string $value Valeur à tester utilisé pour le retour.
     * @param string $lengthValue Taille de la valeur.
     * @param string $max Valeur de comparraison.
     * @param bool $not Inverse le test.
     */
    protected function sizeMax( $key, $value, $lengthValue, $max, $not = true )
    {
        if( ($lengthValue > $max) && $not )
        {
            $this->addReturn($key, $value, 'La valeur de %s est trop grande.');
        }
        else if( !($lengthValue > $max) && !$not )
        {
            $this->addReturn($key, $value, 'La valeur de %s est trop petite.');
        }
    }

    /**
     * Test si une valeur est plus petite que la valeur de comparaison.
     * 
     * @param string $key Identifiant de la valeur.
     * @param string $value Valeur à tester utilisé pour le retour.
     * @param string $lengthValue Taille de la valeur.
     * @param string $min Valeur de comparraison.
     * @param bool $not Inverse le test.
     */
    protected function sizeMin( $key, $value, $lengthValue, $min, $not = true )
    {
        if( $lengthValue < $min && $not )
        {
            $this->addReturn($key, $value, 'La valeur de %s est trop petite.');
        }
        else if( !($lengthValue < $min) && !$not )
        {
            $this->addReturn($key, $value, 'La valeur de %s est trop grande.');
        }
    }

    /**
     * Test si la valeur est Alpha numérique [a-zA-Z0-9].
     * 
     * @param string $key Identifiant de la valeur.
     * @param string $value Valeur à tester.
     * @param bool $not Inverse le test.
     */
    protected function validAlphaNum( $key, $value, $not = true )
    {
        if( !ctype_alnum($value) && $not )
        {
            $this->addReturn($key, $value, 'La valeur de %s n\'est pas alpha numérique.');
        }
        else if( ctype_alnum($value) && !$not )
        {
            $this->addReturn($key, $value, 'La valeur de %s ne doit pas être alpha numérique.');
        }
    }

    /**
     * Test si la valeur est alpha numérique et possède des caractères textuelles [a-zA-Z0-9 .!?,;:_-].
     * 
     * @param string $key Identifiant de la valeur.
     * @param string $value Valeur à tester.
     * @param bool $not Inverse le test.
     */
    protected function validAlphaNumText( $key, $value, $not = true )
    {
        if( !preg_match('/^[a-zA-Z0-9 .!?,;:_-]*$/', $value) && $not )
        {
            $this->addReturn($key, $value, 'La valeur de %s ne correspond pas à la règle de validation.');
        }
        else if( preg_match('/^[a-zA-Z0-9 .!?,;:_-]*$/', $value) && !$not )
        {
            $this->addReturn($key, $value, 'La valeur de %s ne doit pas correspondre à la règle de validation.');
        }
    }

    /**
     * Test si la valeur est de type array.
     * 
     * @param string $key Identifiant de la valeur.
     * @param array $value Valeur à tester.
     * @param bool $not Inverse le test.
     */
    protected function validArray( $key, $value, $not = true )
    {
        if( !is_array($value) && $not )
        {
            $this->addReturn($key, $value, 'La valeur de %s n\'est pas un array.');
        }
        else if( is_array($value) && !$not )
        {
            $this->addReturn($key, $value, 'La valeur de %s ne doit pas être un array.');
        }
    }

    /**
     * Test si une valeur est entre 2 valeurs de comparaison.
     * 
     * @param string $key Identifiant de la valeur.
     * @param string $value Valeur à tester.
     * @param string $between Liste de 2 valeurs de comparaison séparées par une virgule.
     * @param bool $not Inverse le test.
     *
     * @throws \InvalidArgumentException Les valeurs between sont mal renseignées.
     * @throws \InvalidArgumentException La valeur min de between n'est pas numérique.
     * @throws \InvalidArgumentException La valeur max de between n'est pas numérique.
     * @throws \InvalidArgumentException La valeur min de between est supérieur de la valeur max de between.
     * @throws \InvalidArgumentException La fonction between ne peut pas tester pas ce type de valeur.
     */
    public function validBetween( $key, $value, $between, $not = true )
    {
        $betweenExplode = explode(",", $between);

        if( !isset($betweenExplode[ 0 ]) || !isset($betweenExplode[ 1 ]) )
        {
            throw new \InvalidArgumentException('Between values are invalid.');
        }

        $min = $betweenExplode[ 0 ];
        $max = $betweenExplode[ 1 ];

        if( !is_numeric($min) )
        {
            throw new \InvalidArgumentException('The minimum value of between must be numeric.');
        }
        else if( !is_numeric($max) )
        {
            throw new \InvalidArgumentException('The maximum value of entry must be numeric.');
        }
        else if( $min > $max )
        {
            throw new \InvalidArgumentException('The minimum value must not be greater than the maximum value.');
        }

        if( is_int($value) || is_float($value) )
        {
            $this->sizeBetween($key, $value, $value, $min, $max, $not);
        }
        else if( is_string($value) )
        {
            $this->sizeBetween($key, $value, strlen($value), $min, $max, $not);
        }
        else if( is_array($value) )
        {
            $this->sizeBetween($key, $value, count($value), $min, $max, $not);
        }
        else
        {
            throw new \InvalidArgumentException('The between function can not test this type of value.');
        }
    }

    /**
     * Test si une valeur est de type boolean.
     * 
     * @param string $key Identifiant de la valeur.
     * @param bool $value Valeur à tester.
     * @param bool $not Inverse le test.
     */
    protected function validBool( $key, $value, $not = true )
    {
        if( !filter_var($value, FILTER_VALIDATE_BOOLEAN) && $not )
        {
            $this->addReturn($key, $value, 'La valeur de %s n\'est pas un boolean.');
        }
        else if( filter_var($value, FILTER_VALIDATE_BOOLEAN) && !$not )
        {
            $this->addReturn($key, $value, 'La valeur de %s ne doit pas être un boolean.');
        }
    }

    /**
     * Test si une valeur est une date.
     * 
     * @param string $key Identifiant de la valeur.
     * @param string $value Valeur à tester.
     * @param bool $not Inverse le test.
     */
    protected function validDate( $key, $value, $not = true )
    {
        if( !strtotime($value) && $not )
        {
            $this->addReturn($key, $value, 'La valeur de %s n\'est pas une date.');
        }
        else if( strtotime($value) && !$not )
        {
            $this->addReturn($key, $value, 'La valeur de %s ne doit pas être une date.');
        }
    }

    /**
     * Test si une date est antérieur à la date de comparaison.
     * 
     * @param string $key Identifiant de la valeur.
     * @param string $value Date à tester.
     * @param string $dateAfter Date de comparaison.
     * @param bool $not Inverse le test.
     *
     * @return int 1 erreur de date.
     */
    protected function validDateAfter( $key, $value, $dateAfter, $not = true )
    {
        $this->validDate($key, $value);
        $this->validDate($key, $dateAfter);

        if( $this->hasError($key) )
        {
            return 1;
        }

        if( !($value < $dateAfter) && $not )
        {
            $this->addReturn($key, $value, 'La date de %s est supérieur à la date de comparaison.');
        }
        else if( ($value < $dateAfter) && !$not )
        {
            $this->addReturn($key, $value, 'La date de %s ne doit pas être supérieur à la date de comparaison.');
        }
    }

    /**
     * Test si une date est postérieur à la date de comparaison.
     * 
     * @param string $key Identifiant de la valeur.
     * @param string $value Date à tester.
     * @param string $dateBefore Date de comparaison.
     * @param bool $not Inverse le test
     *
     * @return int 1 erreur de date.
     */
    protected function validDateBefore( $key, $value, $dateBefore, $not = true )
    {
        $this->validDate($key, $value);
        $this->validDate($key, $dateBefore);

        if( $this->hasError($key) )
        {
            return 1;
        }

        if( !($value > $dateBefore) && $not )
        {
            $this->addReturn($key, $value, 'La date de %s est inférieur à la date de comparaison.');
        }
        else if( ($value > $dateBefore) && !$not )
        {
            $this->addReturn($key, $value, 'La date de %s ne doit pas être inferieur à la date de comparaison.');
        }
    }

    /**
     * Test si une date correspond au format.
     * 
     * @see http://php.net/manual/fr/datetime.createfromformat.php
     * 
     * @param string $key Identifiant de la valeur.
     * @param string $value Valeur à tester.
     * @param string $format Format de la date (ex: Y-m-d).
     * @param bool $not Inverse le test.
     *
     * @return int 1 erreur de date.
     */
    protected function validDateFormat( $key, $value, $format, $not = true )
    {
        $this->validDate($key, $value);

        if( $this->hasError($key) )
        {
            return 1;
        }

        $dateFormat  = date_parse_from_format($format, $value);
        $errorFormat = $dateFormat[ 'error_count' ] === 0 && $dateFormat[ 'warning_count' ] === 0;

        if( !$errorFormat && $not )
        {
            $this->addReturn($key, $value, 'La valeur de %s n\'est pas au format requis.');
        }
        else if( $errorFormat && !$not )
        {
            $this->addReturn($key, $value, 'La valeur de %s ne doit pas être du format requis.');
        }
    }

    /**
     * Test si une valeur est un répértoire existant sur le serveur.
     * 
     * @param string $key Identifiant de la valeur.
     * @param string $value Valeur à tester.
     * @param bool $not Inverse le test.
     */
    protected function validDir( $key, $value, $not = true )
    {
        if( !is_dir($value) && $not )
        {
            $this->addReturn($key, $value, 'Le chemin de %s n\'est pas valide.');
        }
        else if( is_dir($value) && !$not )
        {
            $this->addReturn($key, $value, 'Le chemin de %s n\'est pas valide.');
        }
    }

    /**
     * Test si une valeur est un email.
     * 
     * @param string $key Identifiant de la valeur.
     * @param string $value Valeur à tester.
     * @param bool $not Inverse le test.
     */
    protected function validEmail( $key, $value, $not = true )
    {
        if( !filter_var($value, FILTER_VALIDATE_EMAIL) && $not )
        {
            $this->addReturn($key, $value, 'La valeur de %s n\'est pas une adresse email.');
        }
        else if( filter_var($value, FILTER_VALIDATE_EMAIL) && !$not )
        {
            $this->addReturn($key, $value, 'La valeur de %s est une adresse email.');
        }
    }

    /**
     * Test si 2 valeurs sont identiques.
     * 
     * @param string $key Identifiant de la valeur.
     * @param scalar $value Valeur à tester.
     * @param scalar $equal Valeur de comparaison.
     * @param bool $not Inverse le test.
     */
    protected function validEqual( $key, $value, $equal, $not = true )
    {
        if( $value !== $equal && $not )
        {
            $this->addReturn($key, $value, 'la valeur de %s n\'est pas valide.');
        }
        else if( $value === $equal && !$not )
        {
            $this->addReturn($key, $value, 'la valeur de %s n\'est pas valide.');
        }
    }

    /**
     * Test si la valeur est un fichier.
     * 
     * @param string $key Identifiant de la valeur.
     * @param string $value Valeur à tester.
     * @param bool $not Inverse le test.
     */
    protected function validFile( $key, $value, $not = true )
    {
        if( !is_file($value) && $not )
        {
            $this->addReturn($key, $value, '%s n\'est pas un fichier.');
        }
        else if( is_file($value) && !$not )
        {
            $this->addReturn($key, $value, '%s ne doit pas être un fichier.');
        }
    }

    /**
     * Test si une valeur est de type numérique flottant.
     * 
     * @param string $key Identifiant de la valeur.
     * @param float $value Valeur à tester.
     * @param bool $not Inverse le test.
     */
    protected function validFloat( $key, $value, $not = true )
    {
        if( !is_float($value) && $not )
        {
            $this->addReturn($key, $value, 'La valeur de %s n\'est pas un nombre flottant.');
        }
        else if( is_float($value) && !$not )
        {
            $this->addReturn($key, $value, 'La valeur de %s ne doit être un nombre flottant.');
        }
    }

    /**
     * Filtre une valeur avec la méthode htmlspecialchars.
     * 
     * @param string $keyStr Identifiant de la valeur.
     */
    protected function validHtmlsc( $keyStr )
    {
        $key = strstr($keyStr, ".", true);
        if( !is_string($this->inputs[ $key ]) )
        {
            throw new \InvalidArgumentException('The '
            . htmlspecialchars($key)
            . ' field does not exist');
        }
        $this->inputs[ $key ] = htmlspecialchars($this->inputs[ $key ]);
    }

    /**
     * Test si une valeur est contenu dans un tableau.
     * 
     * @param string $key Identifiant de la valeur.
     * @param scalar $value Valeur à tester.
     * @param array $list Tableau de comparaison.
     * @param bool $not Inverse le test.
     */
    protected function validInArray( $key, $value, $list, $not = true )
    {
        $listExplode = explode(",", $list);
        if( !in_array($value, $listExplode) && $not )
        {
            $this->addReturn($key, $value, 'La valeur %s n\'est pas dans la liste.');
        }
        else if( in_array($value, $listExplode) && !$not )
        {
            $this->addReturn($key, $value, 'La valeur de %s ne doit pas être dans la liste.');
        }
    }

    /**
     * Test si une valeur est de type entier.
     * 
     * @param string $key Identifiant de la valeur.
     * @param int $value Valeur à tester.
     * @param bool $not Inverse le test.
     */
    protected function validInt( $key, $value, $not = true )
    {
        if( !filter_var($value, FILTER_VALIDATE_INT) && $not )
        {
            $this->addReturn($key, $value, 'La valeur de %s n\'est pas un nombre entier.');
        }
        else if( filter_var($value, FILTER_VALIDATE_INT) && !$not )
        {
            $this->addReturn($key, $value, 'La valeur de %s ne doit être un nombre entier.');
        }
    }

    /**
     * Test si une valeur est une adresse IP.
     * 
     * @param string $key Identifiant de la valeur.
     * @param float $value Valeur à tester.
     * @param bool $not Inverse le test.
     */
    protected function validIp( $key, $value, $not = true )
    {

        if( !filter_var($value, FILTER_VALIDATE_IP) && $not )
        {
            $this->addReturn($key, $value, 'La valeur de %s n\'est pas une adresse IP.');
        }
        else if( filter_var($value, FILTER_VALIDATE_IP) && !$not )
        {
            $this->addReturn($key, $value, 'La valeur de %s ne doit être une adresse IP.');
        }
    }

    /**
     * Test si la valeur et de type JSON.
     * 
     * @param string $key Identifiant de la valeur.
     * @param string $value Valeur à tester.
     * @param bool $not Inverse le test.
     */
    protected function validJson( $key, $value, $not = true )
    {
        json_decode($value);
        if( json_last_error() != JSON_ERROR_NONE && $not )
        {
            $this->addReturn($key, $value, 'La valeur de %s n\'est pas au format JSON.');
        }
        else if( json_last_error() == JSON_ERROR_NONE && !$not )
        {
            $this->addReturn($key, $value, 'La valeur de %s ne doit pas être au format JSON.');
        }
    }

    /**
     * Test si une valeur est plus grande que la valeur de comparaison.
     * 
     * @param string $key Identifiant de la valeur.
     * @param int|float|string|array $value Valeur à tester.
     * @param int|float $max Valeur de comparraison.
     * @param bool $not Inverse le test.
     *
     * @throws \InvalidArgumentException La valeur max n'est pas numérique.
     * @throws \InvalidArgumentException La fonction max ne peut pas tester pas ce type de valeur.
     */
    public function validMax( $key, $value, $max, $not = true )
    {
        if( !is_numeric($max) )
        {
            throw new \InvalidArgumentException('The max value must be numeric.');
        }

        if( is_int($value) || is_float($value) )
        {
            $this->sizeMax($key, $value, $value, $max, $not);
        }
        else if( is_string($value) )
        {
            $this->sizeMax($key, $value, strlen($value), $max, $not);
        }
        else if( is_array($value) )
        {
            $this->sizeMax($key, $value, count($value), $max, $not);
        }
        else
        {
            throw new \InvalidArgumentException('The max function can not test this type of value.');
        }
    }

    /**
     * Test si une valeur est plus petite que la valeur de comparaison.
     * 
     * @param string $key Identifiant de la valeur.
     * @param int|float|string|array $value Valeur à tester.
     * @param int|float $min Valeur de comparraison.
     * @param bool $not Inverse le test.
     *
     * @throws \InvalidArgumentException La valeur min n'est pas numérique.
     * @throws \InvalidArgumentException La fonction min ne peut pas tester pas ce type de valeur.
     */
    protected function validMin( $key, $value, $min, $not = true )
    {
        if( !is_numeric($min) )
        {
            throw new \InvalidArgumentException('The min value must be numeric.');
        }

        if( is_int($value) || is_float($value) )
        {
            $this->sizeMin($key, $value, $value, $min, $not);
        }
        else if( is_string($value) )
        {
            $this->sizeMin($key, $value, strlen($value), $min, $not);
        }
        else if( is_array($value) )
        {
            $this->sizeMin($key, $value, count($value), $min, $not);
        }
        else
        {
            throw new \InvalidArgumentException('The min function can not test this type of value.');
        }
    }

    /**
     * Test si une valeur est égale à une expression régulière.
     * 
     * @param string $key Identifiant de la valeur.
     * @param scalar $value Valeur à tester.
     * @param string $regex Expression régulière.
     * @param bool $not Inverse le test.
     */
    protected function validRegex( $key, $value, $regex, $not = true )
    {
        if( !preg_match($regex, $value) && $not )
        {
            $this->addReturn($key, $value, 'La valeur de %s n\'est pas valide (regex).');
        }
        else if( preg_match($regex, $value) && !$not )
        {
            $this->addReturn($key, $value, 'La valeur de %s est valide (regex).');
        }
    }

    /**
     * Test si une valeur est requise.
     * 
     * @param string $key Identifiant de la valeur.
     * @param mixed $value Valeur à tester.
     */
    protected function validRequired( $key, $value )
    {
        if( empty($value) )
        {
            $this->addReturn($key, $value, 'La valeur %s est requise.');
        }
    }

    /**
     * Test si la valeur correspond à une chaine de caractères alpha numérique (underscore et tiret autorisé).
     *  
     * @param string $key Identifiant de la valeur.
     * @param string $value Valeur à tester.
     * @param bool $not Inverse le test.
     */
    protected function validSlug( $key, $value, $not = true )
    {
        if( !preg_match('/^[a-zA-Z0-9_-]*$/', $value) && $not )
        {
            $this->addReturn($key, $value, 'La valeur de %s ne correspond pas à la règle de validation.');
        }
        else if( preg_match('/^[a-zA-Z0-9_-]*$/', $value) && !$not )
        {
            $this->addReturn($key, $value, 'La valeur de %s ne doit pas correspondre à la règle de validation.');
        }
    }

    /**
     * Test si la valeur est une chaine de caractères.
     * 
     * @param string $key Identifiant de la valeur.
     * @param string $value Valeur à tester.
     * @param bool $not Inverse le test.
     */
    protected function validString( $key, $value, $not = true )
    {
        if( !is_string($value) && $not )
        {
            $this->addReturn($key, $value, 'La valeur de %s n\'est pas une chaine de caractères.');
        }
        else if( is_string($value) && !$not )
        {
            $this->addReturn($key, $value, 'La valeur de %s ne doit pas être une chaine de caractères.');
        }
    }

    /**
     * Filtre les balises autorisées dans une valeur.
     * 
     * @param string $keyStr Identifiant de la valeur.
     * @param string $value Valeur à filtrer.
     * @param string $tags Liste des balise HTML autorisés.
     */
    protected function validStripTags( $keyStr, $value,
        $tags = '<h1><h2><h3><h4><h5><h6>'
    . '<p><span><b><i><u><a>'
    . '<table><thead><tbody><tfoot><tr><th><td>'
    . '<ul><ol><li><dl><dt><dd><img><br><hr>'
    )
    {
        if( !is_string($value) )
        {
            throw new \InvalidArgumentException('The value of the '
            . htmlspecialchars($keyStr)
            . ' field is not a string');
        }
        $key                  = strstr($keyStr, ".", true);
        $this->inputs[ $key ] = strip_tags($value, $tags);
    }

    /**
     * Test la validité d'un token ($_SESSION['token']) à une valeur de comparaison
     * et son rapport au temps ($_SESSION['token_time'])
     * 
     * @param string $key Identifiant de la valeur.
     * @param string $value Valeur à tester.
     * @param int $time Nombre de seconde ou le token est valide (défaut 15 minutes), 
     * si la valeur du time = 0 alors le test du temps de validation n'est pas effectif.
     *
     * @throws \\InvalidArgumentException La valeur time n'est pas numérique.
     */
    protected function validToken( $key, $value, $time = 900 )
    {
        if( session_id() == '' )
        {
            @session_start();
        }

        /* À revoir le passage d'argument boolean automatique pour les fonctions hors not */
        if( $time === true )
        {
            $time = 900;
        }
        if( !is_numeric($time) )
        {
            throw new \InvalidArgumentException('The time value must be numeric.');
        }

        if( !isset($_SESSION[ 'token' ]) && !isset($_SESSION[ 'token_time' ]) )
        {
            $this->addReturn($key, $value, 'Une erreur est survenue.');
        }
        else if( $_SESSION[ 'token' ] != $value )
        {
            $this->addReturn($key, $value, 'Le token n\'est pas valide.');
        }
        else if( $_SESSION[ 'token_time' ] <= (time() - intval($time)) )
        {
            $this->addReturn($key, $value, 'Vous avez attendu trop longtemps, veilliez recharger la page.');
        }
    }

    /**
     * Test si une valeur est une URL.
     * 
     * @param string $key Identifiant de la valeur.
     * @param string $value Valeur à tester.
     * @param bool $not Inverse le test.
     */
    protected function validUrl( $key, $value, $not = true )
    {
        if( !filter_var($value, FILTER_VALIDATE_URL) && $not )
        {
            $this->addReturn($key, $value, 'La valeur de %s n\'est pas une URL');
        }
        else if( filter_var($value, FILTER_VALIDATE_URL) && !$not )
        {
            $this->addReturn($key, $value, 'La valeur de %s est une URL');
        }
    }

    /**
     * Ajoute une valeur de retour formaté en cas d'erreur de validation.
     *
     * @param string $keyFunc Identifiant de la valeur.
     * @param mixed $value Valeur de test.
     * @param string $msg Message à formater avec la valeur de test.
     */
    private function addReturn( $keyFunc, $value, $msg )
    {
        $key = strstr($keyFunc, ".", true);

        if( !isset($this->return[ $keyFunc ]) )
        {
            $this->return[ $keyFunc ] = vsprintf($msg, [ $key, $value, $keyFunc ]);
        }
        else
        {
            $this->return[ $keyFunc ] = vsprintf($this->return[ $keyFunc ], [ $key,
                $value, $keyFunc ]);
        }

        $this->keyUniqueReturn[] = $key;
    }
}