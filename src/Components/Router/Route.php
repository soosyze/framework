<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Router;

/**
 * Description of Route
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
final class Route
{
    /**
     * La liste des routes par clé.
     *
     * @var array
     */
    protected static $routes = [];

    /**
     * La liste des routes par méthodes.
     *
     * @var array
     */
    protected static $routeByMethod = [];

    /**
     * Le namespace courant des routes.
     *
     * @var string
     */
    protected static $namespace = '';

    /**
     * Préfix des chemins.
     *
     * @var string
     */
    protected static $prefix = '';

    /**
     * Prefix des nom.
     *
     * @var string
     */
    protected static $name = '';

    /**
     * Ajoute une route avec la méthode GET.
     *
     * @param string $key
     * @param string $path
     * @param string $uses
     * @param array  $withs
     *
     * @return void
     */
    public static function get(
        string$key,
        string $path,
        string $uses,
        array $withs = []
    ): void {
        self::addMethod('get', $key, $path, $uses, $withs);
    }

    /**
     * Ajoute une route avec la méthode POST.
     *
     * @param string $key
     * @param string $path
     * @param string $uses
     * @param array  $withs
     *
     * @return void
     */
    public static function post(
        string $key,
        string $path,
        string $uses,
        array $withs = []
    ): void {
        self::addMethod('post', $key, $path, $uses, $withs);
    }

    /**
     * Ajoute une route avec la méthode PUT.
     *
     * @param string $key
     * @param string $path
     * @param string $uses
     * @param array  $withs
     *
     * @return void
     */
    public static function put(
        string $key,
        string $path,
        string $uses,
        array $withs = []
    ): void {
        self::addMethod('put', $key, $path, $uses, $withs);
    }

    /**
     * Ajoute une route avec la méthode PATH.
     *
     * @param string $key
     * @param string $path
     * @param string $uses
     * @param array  $withs
     *
     * @return void
     */
    public static function path(
        string $key,
        string $path,
        string $uses,
        array $withs = []
    ): void {
        self::addMethod('path', $key, $path, $uses, $withs);
    }

    /**
     * Ajoute une route avec la méthode DELETE.
     *
     * @param string $key
     * @param string $path
     * @param string $uses
     * @param array  $withs
     *
     * @return void
     */
    public static function delete(
        string $key,
        string $path,
        string $uses,
        array $withs = []
    ): void {
        self::addMethod('delete', $key, $path, $uses, $withs);
    }

    /**
     * Ajoute une route avec la méthode OPTION.
     *
     * @param string $key
     * @param string $path
     * @param string $uses
     * @param array  $withs
     *
     * @return void
     */
    public static function option(
        string $key,
        string $path,
        string $uses,
        array $withs = []
    ): void {
        self::addMethod('option', $key, $path, $uses, $withs);
    }

    /**
     * @param callable $group
     */
    public function group(callable $group): void
    {
        $group();
        self::$namespace = '';
        self::$name      = '';
        self::$prefix    = '';
    }

    /**
     * Spécifie le prefixe des chemins.
     *
     * @param string $prefix
     *
     * @return static
     */
    public static function prefix(string $prefix)
    {
        self::$prefix = $prefix;

        return new static;
    }

    /**
     * Spécifie le prefixe des noms.
     *
     * @param string $name
     *
     * @return static
     */
    public static function name(string $name)
    {
        self::$name = $name;

        return new static;
    }

    /**
     * Retourne la route en spécifiant sa clé.
     *
     * @param string $key Nom de la route.
     *
     * @return array|null
     */
    public static function getRoute(string $key): ?array
    {
        return self::$routes[ strtolower($key) ] ?? null;
    }

    /**
     * Retourne la liste des routes.
     *
     * @return array
     */
    public static function getRoutes(): array
    {
        return self::$routes;
    }

    /**
     * Spécifie le namespace des contrôleurs.
     *
     * @param string $namespace
     *
     * @return self
     */
    public static function useNamespace(string $namespace = '')
    {
        self::$namespace = trim($namespace, '\\/');

        return new static;
    }

    /**
     * Retourne la liste des routes par méthodes.
     *
     * @param string $method Nom de la méthode.
     *
     * @return array
     */
    public static function getRouteByMethod(string $method): array
    {
        return self::$routeByMethod[ strtolower($method) ] ?? [];
    }

    /**
     * Ajoute une route.
     *
     * @param string $method Type de la méthode.
     * @param string $key    La clé unique de la route.
     * @param string $path   La route
     * @param string $uses   Nom de la classe et sa méthode séparées par '@'
     * @param array  $withs  Liste des arguments.
     *
     * @return void
     */
    protected static function addMethod(
        string $method,
        string $key,
        string $path,
        string $uses,
        array $withs = []
    ): void {
        self::$routes[ self::$name . $key ] = [
            'key'    => self::$name . $key,
            'method' => $method,
            'path'   => self::$prefix . $path,
            'uses'   => self::$namespace . '\\' . ltrim($uses, '\\/'),
            'with'   => $withs
        ];
        self::$routeByMethod[ $method ][]   = self::$name . $key;
    }
}
