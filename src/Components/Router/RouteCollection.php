<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Router;

use Soosyze\Components\Router\Exception\RouteNotFoundException;

/**
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
final class RouteCollection
{
    /**
     * @var array
     */
    private $routes = [];

    /**
     * @var array
     */
    private $routesByMethod = [];

    /**
     * @var string
     */
    private $namespace = '';

    /**
     * @var string
     */
    private $name = '';

    /**
     * @var string
     */
    private $prefix = '';

    /**
     * @var array|null
     */
    private $withs;

    /**
     * Ajoute une route à la collection.
     */
    public function addRoute(Route $route): Route
    {
        $this->routes[$route->getKey()] = $route;
        $this->routesByMethod[$route->getMethod()][] = $route->getKey();

        return $this->routes[$route->getKey()];
    }

    /**
     * Créer un group de route.
     */
    public function group(\Closure $group): void
    {
        $group(
            new RouteGroup(
                $this,
                $this->namespace,
                $this->name,
                $this->prefix,
                $this->withs
            )
        );

        $this->namespace = '';
        $this->name      = '';
        $this->prefix    = '';
        $this->withs     = null;
    }

    /**
     * Spécifie le prefixe des chemins.
     */
    public function prefix(string $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Spécifie les paramètres des chemins.
     */
    public function withs(array $withs): self
    {
        $this->withs = $withs;

        return $this;
    }

    /**
     * Spécifie le prefixe des noms.
     */
    public function name(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Spécifie le namespace des contrôleurs.
     */
    public function setNamespace(string $namespace): self
    {
        $this->namespace = $namespace;

        return $this;
    }

    /**
     * Retourne la liste des routes.
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * Retourne une route.
     */
    public function getRoute(string $key): ?Route
    {
        return $this->routes[$key] ?? null;
    }

    /**
     * Retourne une route.
     */
    public function tryGetRoute(string $key): Route
    {
        if ($this->routes[$key] === null) {
            throw new RouteNotFoundException('The path does not exist.');
        }

        return $this->routes[$key];
    }

    /**
     * Retourne la liste des routes par méthodes.
     */
    public function getRoutesByMethod(string $method): array
    {
        return $this->routesByMethod[strtolower($method)] ?? [];
    }
}
