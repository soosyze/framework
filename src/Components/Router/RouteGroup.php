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
 */
final class RouteGroup
{
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
    private $withsGroup = null;

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
    private $withsCurrent = null;

    public function __construct(
        string $namespaceCurrent,
        string $nameCurrent,
        string $prefixCurrent,
        ?array $withsCurrent = null
    ) {
        $this->namespaceCurrent = $namespaceCurrent;
        $this->nameCurrent      = $nameCurrent;
        $this->prefixCurrent    = $prefixCurrent;
        $this->withsCurrent     = $withsCurrent;
    }

    /**
     * Créer un group de route.
     */
    public function group(callable $group): void
    {
        $routerGroup = new self(
            $this->namespaceCurrent . $this->namespaceGroup,
            $this->nameCurrent . $this->nameGroup,
            $this->prefixCurrent . $this->prefixGroup,
            $this->arrayMerge($this->withsCurrent, $this->withsGroup)
        );

        $group($routerGroup);

        unset($routerGroup);
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
     * Ajoute une route avec la méthode GET.
     */
    public function get(
        string $key,
        string $path,
        string $uses,
        ?array $withs = null
    ): Route {
        return $this->addMethod('get', $key, $path, $uses, $withs);
    }

    /**
     * Ajoute une route avec la méthode POST.
     */
    public function post(
        string $key,
        string $path,
        string $uses,
        ?array $withs = null
    ): Route {
        return $this->addMethod('post', $key, $path, $uses, $withs);
    }

    /**
     * Ajoute une route avec la méthode PUT.
     */
    public function put(
        string $key,
        string $path,
        string $uses,
        ?array $withs = null
    ): Route {
        return $this->addMethod('put', $key, $path, $uses, $withs);
    }

    /**
     * Ajoute une route avec la méthode PATCH.
     */
    public function patch(
        string $key,
        string $path,
        string $uses,
        ?array $withs = null
    ): Route {
        return $this->addMethod('patch', $key, $path, $uses, $withs);
    }

    /**
     * Ajoute une route avec la méthode DELETE.
     */
    public function delete(
        string $key,
        string $path,
        string $uses,
        ?array $withs = null
    ): Route {
        return $this->addMethod('delete', $key, $path, $uses, $withs);
    }

    /**
     * Ajoute une route avec la méthode OPTION.
     */
    public function option(
        string $key,
        string $path,
        string $uses,
        ?array $withs = null
    ): Route {
        return $this->addMethod('option', $key, $path, $uses, $withs);
    }

    /**
     * Ajoute une route.
     *
     * @param string     $method Type de la méthode.
     * @param string     $key    La clé unique de la route.
     * @param string     $path   La route
     * @param string     $uses   Nom de la classe et sa méthode séparées par '@'
     * @param array|null $withs  Liste des arguments.
     *
     * @return Route
     */
    private function addMethod(
        string $method,
        string $key,
        string $path,
        string $uses,
        ?array $withs
    ): Route {
        RouteCollection::addRoute(
            new Route(
                $this->nameCurrent . $key,
                $method,
                $this->prefixCurrent . $path,
                $this->namespaceCurrent . $uses,
                $this->arrayMerge($this->withsCurrent, $withs)
            )
        );

        /** @phpstan-var Route */
        return RouteCollection::getRoute($this->nameCurrent . $key);
    }

    private function arrayMerge(?array $array1, ?array $array2): ?array
    {
        if ($array1 === null && $array2 === null) {
            return null;
        }

        return array_merge($array1 ?? [], $array2 ?? []);
    }
}
