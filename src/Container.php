<?php

/**
 * Soosyze Framework http://soosyze.com
 *
 * @package Soosyze
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze;

use ArrayAccess;
use Psr\Container\ContainerInterface;
use Soosyze\Exception\Container\ContainerException;
use Soosyze\Exception\Container\NotFoundException;

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
     * Composant de configuration.
     *
     * @var array|ArrayAccess
     */
    protected $config;

    /**
     * Appel un service comme une fonction.
     *
     * @param string $name Nom du service.
     * @param array  $arg  Paramètres passés à la fonction.
     *
     * @return object
     */
    public function __call($name, $arg)
    {
        return $this->get($name);
    }

    /**
     * Charges un service.
     *
     * @param string $key   Nom du service.
     * @param string $class Objet à instancier.
     * @param array  $arg   Arguments d'instanciation.
     *
     * @return $this
     */
    public function setService($key, $class, array $arg = null)
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
    public function setServices(array $services)
    {
        $this->services = $services;
        $this->loadHooks($services);

        return $this;
    }

    /**
     * Charge les services.
     *
     * @param array $services Liste de services.
     *
     * @return $this
     */
    public function addServices(array $services)
    {
        $this->services += $services;
        $this->loadHooks($services);

        return $this;
    }

    /**
     * Ajoute une instance de service.
     *
     * @param string $key      Nom du service.
     * @param object $instance Instance du service.
     *
     * @return $this
     */
    public function setInstance($key, $instance)
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
    public function setInstances(array $instances)
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
     * @throws \InvalidArgumentException La fonction get accepte uniquement les chaînes de caractères.
     * @throws NotFoundException         Le service appelé n'existe pas.
     * @throws ContainerException        La classe n'est pas instanciable.
     * @return object
     */
    public function get($key)
    {
        if (!is_string($key)) {
            throw new \InvalidArgumentException(htmlspecialchars(
                "Get function only accepts strings. Input was : $key."
            ));
        }

        if (isset($this->instances[ $key ])) {
            return $this->instances[ $key ];
        }

        if (!isset($this->services[ $key ])) {
            throw new NotFoundException(htmlspecialchars("Service $key does not exist."));
        }

        try {
            /*
             * ReflectionClass à la même fonctionnalité que call_user_func_array
             * mais pour le constructeur d'un objet.
             */
            $ref = new \ReflectionClass($this->services[ $key ][ 'class' ]);
        } catch (\ReflectionException $ex) {
            throw new ContainerException(htmlspecialchars("$key is not exist."), $ex->getCode(), $ex);
        }

        $args     = $this->matchArgs($key);
        $instance = $ref->newInstanceArgs($args);
        $this->setInstance($key, $instance);

        return $instance;
    }

    /**
     * Si le service existe.
     *
     * @param string $key Nom du service.
     *
     * @throws \InvalidArgumentException La fonction get accepte uniquement les chaînes de caractères.
     * @return bool
     */
    public function has($key)
    {
        if (!is_string($key)) {
            throw new \InvalidArgumentException(htmlspecialchars(
                "Get function only accepts strings. Input was: $key."
            ));
        }

        return isset($this->services[ $key ]) || isset($this->instances[ $key ]);
    }

    /**
     * Ajoute une fonction pour qu'elle puisse être utilisée par le container.
     *
     * @param string   $name Clé pour appeler la fonction.
     * @param callable $func Fonction à exécuter.
     *
     * @return $this
     */
    public function addHook($name, callable $func)
    {
        $this->hooks[ strtolower($name) ][] = $func;

        return $this;
    }

    /**
     * Demande d'exécution de fonction si elle existe.
     * Utilise le container pour l'ajout des hooks depuis les fichier de services.
     *
     * @param string $name Clé pour appeler la fonction.
     * @param array  $args Paramètres passés à la fonction.
     *
     * @return mixed|void le résultat des fonctions appelées ou rien
     */
    public function callHook($name, array $args = [])
    {
        $key = strtolower($name);
        /* Si mes hooks existent, ils sont exécutés. */
        if (!isset($this->hooks[ $key ])) {
            return '';
        }
        foreach ($this->hooks[ $key ] as $services => $func) {
            $return = \is_string($func)
                ? call_user_func_array([ $this->get($services), $func ], $args)
                : call_user_func_array($func, $args);
        }

        return $return;
    }

    /**
     * Ajoute le composant de configuration pour les services.
     *
     * @param array|ArrayAccess $config
     *
     * @return $this
     */
    public function setConfig($config)
    {
        if (!\is_array($config) && !($config instanceof \ArrayAccess)) {
            throw new \InvalidArgumentException('The configuration must be an ArrayAccess array or instance.');
        }
        $this->config = $config;

        return $this;
    }

    /**
     * Charge les hooks contenus dans les services.
     *
     * @param array $services
     */
    protected function loadHooks(array $services)
    {
        foreach ($services as $service => $value) {
            if (!isset($value[ 'hooks' ])) {
                continue;
            }
            foreach ($value[ 'hooks' ] as $key => $hook) {
                $this->hooks[ $key ][ $service ] = $hook;
            }
        }
    }

    /**
     * Alimente les arguments d'un service avec
     * des valeurs, des élements de configuration ou/et d'autres services.
     *
     * @param string $key Nom du service.
     *
     * @return array Arguments chargés.
     */
    private function matchArgs($key)
    {
        if (!isset($this->services[ $key ][ 'arguments' ])) {
            return [];
        }

        $args = $this->services[ $key ][ 'arguments' ];

        foreach ($args as &$arg) {
            /* Injecte d'autres services comme argument d'instantiation du service appelé. */
            if (strpos($arg, '@') === 0) {
                $arg = $this->get(substr($arg, 1));
            }
            /* Injecte un parmètre comme argument d'instantiation du service appelé. */
            elseif (strpos($arg, '#') === 0) {
                $arg = $this->config[substr($arg, 1)];
            }
            /* Dans le cas ou ont souhaites échaper l'appel à un autre service ou un paramètre. */
            elseif (strpos($arg, '\@') === 0 || strpos($arg, '\#') === 0) {
                $arg = substr($arg, 1);
            }
        }

        return $args;
    }
}
