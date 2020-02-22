<?php

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
     */
    public static function get($key, $path, $uses, array $withs = [])
    {
        self::addMethode('get', $key, $path, $uses, $withs);
    }

    /**
     * Ajoute une route avec la méthode POST.
     *
     * @param type  $key
     * @param type  $path
     * @param type  $uses
     * @param array $withs
     */
    public static function post($key, $path, $uses, array $withs = [])
    {
        self::addMethode('post', $key, $path, $uses, $withs);
    }

    /**
     * Ajoute une route avec la méthode PUT.
     *
     * @param type  $key
     * @param type  $path
     * @param type  $uses
     * @param array $withs
     */
    public static function put($key, $path, $uses, array $withs = [])
    {
        self::addMethode('put', $key, $path, $uses, $withs);
    }

    /**
     * Ajoute une route avec la méthode PATH.
     *
     * @param type  $key
     * @param type  $path
     * @param type  $uses
     * @param array $withs
     */
    public static function path($key, $path, $uses, array $withs = [])
    {
        self::addMethode('path', $key, $path, $uses, $withs);
    }

    /**
     * Ajoute une route avec la méthode DELETE.
     *
     * @param type  $key
     * @param type  $path
     * @param type  $uses
     * @param array $withs
     */
    public static function delete($key, $path, $uses, array $withs = [])
    {
        self::addMethode('delete', $key, $path, $uses, $withs);
    }

    /**
     * Ajoute une route avec la méthode OPTION.
     *
     * @param type  $key
     * @param type  $path
     * @param type  $uses
     * @param array $withs
     */
    public static function option($key, $path, $uses, array $withs = [])
    {
        self::addMethode('option', $key, $path, $uses, $withs);
    }

    /**
     * Retourne la route en spécifiant sa clé.
     *
     * @param string $key Nom de la route.
     *
     * @return array|null
     */
    public static function getRoute($key)
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
    public static function getRoutes()
    {
        return self::$routes;
    }

    /**
     * Spécifie le namespace des contrôleurs.
     *
     * @param type $namespace
     */
    public static function useNamespace($namespace)
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
    public static function getRouteByMethode($methode = null)
    {
        return !empty(self::$routeByMethode[ strtolower($methode) ])
            ? self::$routeByMethode[ strtolower($methode) ]
            : [];
    }

    /**
     * Ajoute une route.
     *
     * @param string $methode Type de la méthode.
     * @param string $key     La clé unique de la route.
     * @param string $path    La route
     * @param string $uses    Nom de la classe et sa méthode séparées par '@'
     * @param array  $withs   Liste des arguments.
     */
    protected static function addMethode(
    $methode,
        $key,
        $path,
        $uses,
        array $withs = []
    ) {
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
