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
final class RouteCollection
{
    /**
     * @var array
     */
    private static $routes = [];

    /**
     * @var array
     */
    private static $routesByMethod = [];

    /**
     * @var string
     */
    private static $namespace = '';

    /**
     * @var string
     */
    private static $name = '';

    /**
     * @var string
     */
    private static $prefix = '';

    /**
     * @var array|null
     */
    private static $withs;

    /**
     * @var self|null
     */
    private static $instance;

    private function __construct()
    {
    }

    /**
     * Ajoute une route à la collection.
     */
    public static function addRoute(Route $route): void
    {
        self::$routes[ $route->getKey() ]              = $route;
        self::$routesByMethod[ $route->getMethod() ][] = $route->getKey();
    }

    /**
     * Créer un group de route.
     */
    public static function group(callable $group): void
    {
        $routerGroup = new RouteGroup(static::$namespace, static::$name, static::$prefix, static::$withs);

        $group($routerGroup);

        unset($routerGroup);

        static::$namespace = '';
        static::$name      = '';
        static::$prefix    = '';
        static::$withs     = null;
    }

    /**
     * Spécifie le prefixe des chemins.
     */
    public static function prefix(string $prefix): self
    {
        static::$prefix = $prefix;

        return self::getInstance();
    }

    /**
     * Spécifie les paramètres des chemins.
     */
    public static function withs(array $withs): self
    {
        static::$withs = $withs;

        return self::getInstance();
    }

    /**
     * Spécifie le prefixe des noms.
     */
    public static function name(string $name): self
    {
        static::$name = $name;

        return self::getInstance();
    }

    /**
     * Spécifie le namespace des contrôleurs.
     */
    public static function setNamespace(string $namespace): self
    {
        static::$namespace = $namespace;

        return self::getInstance();
    }

    /**
     * Retourne la liste des routes.
     */
    public static function getRoutes(): array
    {
        return self::$routes;
    }

    /**
     * Retourne une route.
     */
    public static function getRoute(string $key): ?Route
    {
        return self::$routes[ $key ] ?? null;
    }

    /**
     * Retourne la liste des routes par méthodes.
     */
    public static function getRoutesByMethod(string $method): array
    {
        return self::$routesByMethod[ strtolower($method) ] ?? [];
    }

    /**
     * Retourne l'intance unique de la collection.
     */
    private static function getInstance(): self
    {
        return self::$instance ?? self::$instance = new self();
    }
}
