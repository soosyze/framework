<?php

/**
 * Soosyze Framework http://soosyze.com
 *
 * @package Soosyze
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze;

use Psr\Container\ContainerInterface,
    Soosyze\Exception\Container\NotFoundException,
    Soosyze\Exception\Container\ContainerException;

/**
 * Conteneur d'injection de dépendances et middleware.
 *
 * @see https://www.php-fig.org/psr/psr-11/ Suit les recommandations PSR-11.
 * 
 * @author Mathieu NOËL
 */
class Container implements ContainerInterface
{
    /**
     * Liste des services.
     * 
     * @var array
     */
    protected $services = [];

    /**
     * Liste des objets instanciés.
     * 
     * @var array
     */
    protected $instances = [];

    /**
     * Fonctions de hook
     * 
     * @var array
     */
    protected $hooks = [];

    /**
     * Charges un service.
     * 
     * @param string $key Nom du service.
     * @param string $class Objet à instancier.
     * @param array $arg Arguments d'instanciation.
     *
     * @return $this
     */
    public function setService( $key, $class, array $arg = null )
    {
        $this->services[ $key ] = [ 'class' => $class, 'arguments' => $arg ];
        return $this;
    }

    /**
     * Charge les services.
     * 
     * @param array $services Liste de services.
     *
     * @return $this
     */
    public function setServices( array $services )
    {
        $this->services = $services;
        return $this;
    }

    /**
     * Ajoute une instance de service.
     *
     * @param string $key Nom du service.
     * @param object $instance Instance du service.
     *
     * @return $this
     */
    public function setInstance( $key, $instance )
    {
        $this->instances[ $key ] = $instance;
        return $this;
    }

    /**
     * Ajoute les instances de service.
     *
     * @param object[] $instances Instances des services.
     *
     * @return $this
     */
    public function setInstances( array $instances )
    {
        $this->instances = $instances;
        return $this;
    }

    /**
     * Si le service existe alors on le retourne, sinon on injecte 
     * ses dépendances et retourne son instance.
     * 
     * @param string $key Nom du service.
     * 
     * @return object
     * 
     * @throws \InvalidArgumentException La fonction get accepte uniquement les chaînes de caractères.
     * @throws NotFoundException Le service appelé n'existe pas.
     * @throws ContainerException La classe n'est pas instanciable.
     */
    public function get( $key )
    {
        if( !is_string($key) )
        {
            throw new \InvalidArgumentException('Get function only accepts strings. Input was : '
            . htmlspecialchars($key) . '.'
            );
        }

        if( isset($this->instances[ $key ]) )
        {
            return $this->instances[ $key ];
        }

        if( !isset($this->services[ $key ]) )
        {
            throw new NotFoundException('Service ' . htmlspecialchars($key) . ' does not exist.');
        }

        $args = [];
        if( isset($this->services[ $key ][ 'arguments' ]) )
        {
            $args = $this->services[ $key ][ 'arguments' ];
            foreach( $args as $keyArg => $arg )
            {
                /* Injecte d'autres services comme argument d'instantiation du service appelé. */
                if( preg_match("/^@.*/", $arg, $matches) )
                {
                    $args[ $keyArg ] = $this->get(substr($matches[ 0 ], 1));
                }
            }
        }

        try
        {
            /*
             * ReflectionClass à la même fonctionnalité que call_user_func_array 
             * mais pour le constructeur d'un objet.
             */
            $ref = new \ReflectionClass($this->services[ $key ][ 'class' ]);
        }
        catch( \ReflectionException $ex )
        {
            throw new ContainerException(htmlspecialchars($key) . " is not exist.", $ex->getCode(), $ex);
        }

        $instance = $ref->newInstanceArgs($args);
        $this->setInstance($key, $instance);


        return $this->get($key);
    }

    /**
     * Si le service existe.
     * 
     * @param string $key Nom du service.
     * 
     * @return bool
     * 
     * @throws \InvalidArgumentException La fonction get accepte uniquement les chaînes de caractères.
     */
    public function has( $key )
    {
        if( !is_string($key) )
        {
            throw new \InvalidArgumentException('Get function only accepts strings. Input was: '
            . htmlspecialchars($key) . '.'
            );
        }
        return isset($this->services[ $key ]) || isset($this->instances[ $key ]);
    }

    /**
     * Ajoute une fonction pour qu'elle puisse être utilisée par le container.
     *
     * @param string $name Clé pour appeler la fonction.
     * @param callable $func Fonction à exécuter.
     *
     * @return $this
     */
    public function addHook( $name, callable $func )
    {
        $this->hooks[ $name ][] = $func;
        return $this;
    }

    /**
     * Demande d'exécution de fonction si elle existe. 
     * Utilise le container pour l'ajout des hooks depuis les fichier de services.
     *
     * @param string $name Clé pour appeler la fonction.
     * @param array args Paramètres passés à la fonction.
     * 
     * @return mixed|void le résultat des fonctions appelées ou rien
     */
    public function callHook( $name, array $args = [] )
    {
        $return = "";
        /* Si mes hooks existent, ils sont exécutés. */
        if( isset($this->hooks[ $name ]) )
        {
            foreach( $this->hooks[ $name ] as $func )
            {
                $return = call_user_func_array($func, $args);
            }
        }
        else
        {
            /* Je regarde dans les hooks de mes services. */
            foreach( $this->services as $key => $value )
            {
                /* Si le hook que je recherche existe alors je le charge. */
                if( isset($value[ 'hooks' ][ $name ]) )
                {
                    $obj = $this->get($key);
                    $this->addHook($name, [
                        /* L'instance de mon service. */
                        $obj,
                        /* La fonction à exécuter. */
                        $value[ 'hooks' ][ $name ] ]);
                }
            }
            if( isset($this->hooks[ $name ]) )
            {
                $return = $this->callHook($name, $args);
            }
        }
        return $return;
    }

    /**
     * Appel un service comme une fonction.
     * 
     * @param string $name Nom du service.
     * @param array $arg Paramètres passés à la fonction.
     * 
     * @return object
     */
    public function __call( $name, $arg )
    {
        return $this->get($name);
    }
}