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
abstract class Route
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
    protected static $routeByMethode = [];

    /**
     * Le namespace courant des routes.
     *
     * @var string
     */
    protected static $namespace = '';

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
        self::addMethode('get', $key, $path, $uses, $withs);
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
        self::addMethode('post', $key, $path, $uses, $withs);
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
        self::addMethode('put', $key, $path, $uses, $withs);
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
        self::addMethode('path', $key, $path, $uses, $withs);
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
        self::addMethode('delete', $key, $path, $uses, $withs);
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
        self::addMethode('option', $key, $path, $uses, $withs);
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
        return !empty(self::$routes[ strtolower($key) ])
            ? self::$routes[ strtolower($key) ]
            : null;
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
     * @return void
     */
    public static function useNamespace(string $namespace): void
    {
        self::$namespace = trim($namespace, '\\/');
    }

    /**
     * Retourne la liste des routes par méthodes.
     *
     * @param string $methode Nom de la méthode.
     *
     * @return array
     */
    public static function getRouteByMethode(string $methode): array
    {
        return self::$routeByMethode[ strtolower($methode) ]
            ?? [];
    }

    /**
     * Ajoute une route.
     *
     * @param string $methode Type de la méthode.
     * @param string $key     La clé unique de la route.
     * @param string $path    La route
     * @param string $uses    Nom de la classe et sa méthode séparées par '@'
     * @param array  $withs   Liste des arguments.
     *
     * @return void
     */
    protected static function addMethode(
        string $methode,
        string $key,
        string $path,
        string $uses,
        array $withs = []
    ): void {
        self::$routes[ $key ]               = [
            'methode' => $methode,
            'path'    => $path,
            'uses'    => self::$namespace . '\\' . ltrim($uses, '\\/'),
            'with'    => $withs
        ];
        self::$routeByMethode[ $methode ][] = [
            'key'     => $key,
            'methode' => $methode,
            'path'    => $path,
            'uses'    => self::$namespace . '\\' . ltrim($uses, '\\/'),
            'with'    => $withs
        ];
    }
}
