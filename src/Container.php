<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
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
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class Container implements ContainerInterface
{
    /**
     * Liste des alias.
     *
     * @var array
     */
    protected $alias = [];

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
     * @var array|ArrayAccess<string, scalar>
     */
    protected $config;

    /**
     * Appel un service comme une fonction.
     *
     * @param string $name Nom du service.
     * @param array  $args Paramètres passés à la fonction.
     *
     * @return object
     */
    public function __call(string $name, array $args)
    {
        return $this->get($name);
    }

    /**
     * Charges un service.
     *
     * @param string $key   Nom du service.
     * @param string $class Objet à instancier.
     * @param array  $attr  Arguments d'instanciation.
     *
     * @return $this
     */
    public function setService(
        string $key,
        string $class,
        array $attr = []
    ): self {
        $this->services[ $key ] = [
            'class'     => $class,
            'arguments' => $attr[ 'arguments' ] ?? null,
            'hooks'     => $attr[ 'hook' ] ?? null,
            'calls'     => $attr[ 'calls' ] ?? null
        ];
        $this->load([ $key =>  $this->services[ $key ] ]);

        return $this;
    }

    /**
     * Charge les services.
     *
     * @param array $services Liste de services.
     *
     * @return $this
     */
    public function setServices(array $services): self
    {
        $this->services = $services;
        $this->load($services);

        return $this;
    }

    /**
     * Charge les services.
     *
     * @param array $services Liste de services.
     *
     * @return $this
     */
    public function addServices(array $services): self
    {
        $this->services += $services;
        $this->load($services);

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
    public function setInstance($key, object $instance): self
    {
        $this->instances[ $key ] = $instance;
        $this->alias[ get_class($instance) ] = $key;

        return $this;
    }

    /**
     * Ajoute les instances de service.
     *
     * @param object[] $instances Instances des services.
     *
     * @return $this
     */
    public function setInstances(array $instances): self
    {
        $this->instances = $instances;

        return $this;
    }

    /**
     * Si le service existe alors on le retourne, sinon on injecte
     * ses dépendances et retourne son instance.
     *
     * @param string $id Nom du service.
     *
     * @throws NotFoundException  Le service appelé n'existe pas.
     * @throws ContainerException La classe n'est pas instanciable.
     *
     * @return object
     */
    public function get(string $id)
    {
        if (isset($this->instances[ $id ])) {
            return $this->instances[ $id ];
        }
        if (isset($this->alias[ $id ])) {
            return $this->get($this->alias[ $id ]);
        }

        $className = $this->services[ $id ][ 'class' ] ?? null;
        if ($className === null) {
            throw new NotFoundException(
                htmlspecialchars("Service $id does not exist.")
            );
        }

        try {
            /*
             * ReflectionClass à la même fonctionnalité que call_user_func_array
             * mais pour le constructeur d'un objet.
             */
            $ref = new \ReflectionClass($className);
        } catch (\ReflectionException $ex) {
            throw new ContainerException(
                htmlspecialchars("Class $id is not exist.")
            );
        }

        $args     = $this->getArgs($ref, $id);
        $instance = $ref->newInstanceArgs($args);

        $this->setInstance($id, $instance);
        $this->setCalls($id);

        return $instance;
    }

    /**
     * Si le service existe.
     *
     * @param string $id Nom du service.
     *
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->services[ $id ]) || isset($this->instances[ $id ]);
    }

    /**
     * Ajoute une fonction pour qu'elle puisse être utilisée par le container.
     *
     * @param string   $name Clé pour appeler la fonction.
     * @param callable $func Fonction à exécuter.
     *
     * @return $this
     */
    public function addHook(string $name, callable $func): self
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
     * @return mixed le résultat des fonctions appelées ou rien
     */
    public function callHook(string $name, array $args = [])
    {
        $key = strtolower($name);
        /* Si mes hooks existent, ils sont exécutés. */
        $out = '';
        if (!isset($this->hooks[ $key ])) {
            return $out;
        }
        foreach ($this->hooks[ $key ] as $services => $func) {
            $callable = is_string($func)
                ? [ $this->get($services), $func ]
                : $func;

            if (\is_callable($callable)) {
                $out = call_user_func_array($callable, $args);
            } else {
                throw new \RuntimeException(
                    sprintf('The hook %s must be a callable', $key)
                );
            }
        }

        return $out;
    }

    /**
     * Ajoute le composant de configuration pour les services.
     *
     * @param mixed $config
     *
     * @return $this
     */
    public function setConfig($config): self
    {
        if (!\is_array($config) && !($config instanceof ArrayAccess)) {
            throw new \InvalidArgumentException(
                'The configuration must be an \ArrayAccess array or instance.'
            );
        }
        $this->config = $config;

        return $this;
    }

    /**
     * Charge les hooks contenus dans les services.
     *
     * @param array $services
     *
     * @return void
     */
    protected function load(array $services): void
    {
        foreach ($services as $service => $value) {
            $this->alias[ $value[ 'class' ] ] = $service;

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
     * @param \ReflectionClass $ref
     * @param string           $id  Nom du service.
     *
     * @throws \RuntimeException
     * @return array             Arguments chargés.
     */
    private function getArgs(\ReflectionClass $ref, string $id): array
    {
        $construct = $ref->getConstructor();
        if (!$construct instanceof \ReflectionMethod) {
            return [];
        }

        $args   = $this->services[ $id ][ 'arguments' ] ?? [];
        $out    = [];

        foreach ($construct->getParameters() as $param) {
            $name = $param->getName();
            if (isset($args[ $name ])) {
                $match = $this->matchParam($args[ $name ]);

                if ($match === null && $param->isOptional()) {
                    continue;
                }

                $out[] = $match;

                continue;
            }

            /** @var \ReflectionNamedType $type */
            $type = $param->getType();
            if (isset($this->alias[ $type->getName() ])) {
                $out[] = $this->get($this->alias[ $type->getName() ]);

                continue;
            }

            if (!$param->isOptional()) {
                throw new \RuntimeException("The $name parameter is absent");
            }
        }

        return $out;
    }

    /**
     * Alimente les arguments d'un service avec
     * des valeurs, des élements de configuration ou/et d'autres services.
     *
     * @param mixed $arg
     *
     * @return mixed
     */
    private function matchParam($arg)
    {
        if (!\is_string($arg)) {
            return $arg;
        }
        /* Injecte d'autres services comme argument d'instantiation du service appelé. */
        if (strpos($arg, '@') === 0) {
            return $this->get(substr($arg, 1));
        }
        /* Injecte un parmètre comme argument d'instantiation du service appelé. */
        if (strpos($arg, '#') === 0) {
            return $this->config[ substr($arg, 1) ] ?? null;
        }
        /* Dans le cas ou ont souhaites échaper l'appel à un autre service ou un paramètre. */
        if (strpos($arg, '\@') === 0 || strpos($arg, '\#') === 0) {
            return substr($arg, 1);
        }

        return $arg;
    }

    /**
     * Injection de réglage.
     *
     * @param string $id
     *
     * @return $this
     */
    private function setCalls(string $id): self
    {
        $calls = $this->services[ $id ][ 'calls' ] ?? null;
        if ($calls === null) {
            return $this;
        }

        foreach ($calls as $method => $value) {
            $arg = $this->matchParam($value);
            $this->instances[ $id ]->$method($arg);
        }

        return $this;
    }
}
