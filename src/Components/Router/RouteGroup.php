<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Router;

/**
 * @author Mathieu NOËL <mathieu@soosyze.com>
 *
 * @method Route get(string $key, string $path, string $uses, ?array $withs = null)    Ajoute une route avec la méthode GET.
 * @method Route post(string $key, string $path, string $uses, ?array $withs = null)   Ajoute une route avec la méthode POST.
 * @method Route put(string $key, string $path, string $uses, ?array $withs = null)    Ajoute une route avec la méthode PUT.
 * @method Route patch(string $key, string $path, string $uses, ?array $withs = null)  Ajoute une route avec la méthode PATCH.
 * @method Route option(string $key, string $path, string $uses, ?array $withs = null) Ajoute une route avec la méthode OPTION.
 * @method Route delete(string $key, string $path, string $uses, ?array $withs = null) Ajoute une route avec la méthode DELETE.
 */
final class RouteGroup
{
    private const HTTP_METHOD = ['get', 'post', 'put', 'patch', 'delete', 'option'];

    /**
     * @var string
     */
    private $namespaceGroup = '';

    /**
     * @var string
     */
    private $nameGroup = '';

    /**
     * @var string
     */
    private $prefixGroup = '';

    /**
     * @var array|null
     */
    private $withsGroup;

    /**
     * @var string
     */
    private $prefixCurrent;

    /**
     * @var string
     */
    private $namespaceCurrent;

    /**
     * @var string
     */
    private $nameCurrent;

    /**
     * @var array|null
     */
    private $withsCurrent;

    /** @var RouteCollection */
    private $collection;

    public function __construct(
        RouteCollection $collection,
        string $namespaceCurrent,
        string $nameCurrent,
        string $prefixCurrent,
        ?array $withsCurrent = null
    ) {
        $this->collection       = $collection;
        $this->namespaceCurrent = $namespaceCurrent;
        $this->nameCurrent      = $nameCurrent;
        $this->prefixCurrent    = $prefixCurrent;
        $this->withsCurrent     = $withsCurrent;
    }

    /**
     * Route Ajoute une route avec la méthode HTTP.
     *
     * @param string $name Methode HTTP
     * @param array  $args [string $key, string $path, string $uses, ?array $withs = null]
     *
     * @return Route
     */
    public function __call(string $name, array $args)
    {
        if (in_array($name, self::HTTP_METHOD)) {
            return $this->addMethod($name, ...$args);
        }

        throw new \BadMethodCallException(
            sprintf('Method %s does not exist', $name)
        );
    }

    /**
     * Créer un group de route.
     */
    public function group(\Closure $group): void
    {
        $group(
            new self(
                $this->collection,
                $this->namespaceCurrent . $this->namespaceGroup,
                $this->nameCurrent . $this->nameGroup,
                $this->prefixCurrent . $this->prefixGroup,
                self::arrayMerge($this->withsCurrent, $this->withsGroup)
            )
        );

        $this->namespaceGroup = '';
        $this->nameGroup      = '';
        $this->prefixGroup    = '';
        $this->withsGroup     = null;
    }

    /**
     * Spécifie le prefixe des chemins.
     */
    public function prefix(string $prefix, ?array $withs = null): self
    {
        $this->prefixGroup = $prefix;
        $this->withsGroup  = $withs ?? $this->withsGroup;

        return $this;
    }

    /**
     * Spécifie le prefixe des noms.
     */
    public function name(string $name): self
    {
        $this->nameGroup = $name;

        return $this;
    }

    /**
     * Spécifie le namespace des contrôleurs.
     */
    public function setNamespace(string $namespace): self
    {
        $this->namespaceGroup = $namespace;

        return $this;
    }

    /**
     * Spécifie les paramètres des chemins.
     */
    public function withs(array $withs): self
    {
        $this->withsGroup = $withs;

        return $this;
    }

    /**
     * Ajoute une route.
     *
     * @param string     $method Type de la méthode.
     * @param string     $key    La clé unique de la route.
     * @param string     $path   La route
     * @param string     $uses   Nom de la classe et sa méthode séparées par '@'
     * @param array|null $withs  Liste des arguments.
     */
    private function addMethod(
        string $method,
        string $key,
        string $path,
        string $uses,
        ?array $withs = null
    ): Route {
        return $this->collection->addRoute(
            new Route(
                $this->nameCurrent . $key,
                $method,
                $this->prefixCurrent . $path,
                $this->namespaceCurrent . $uses,
                self::arrayMerge($this->withsCurrent, $withs)
            )
        );
    }

    private static function arrayMerge(?array $array1, ?array $array2): ?array
    {
        return $array1 === null && $array2 === null
            ? null
            : array_merge($array1 ?? [], $array2 ?? []);
    }
}
