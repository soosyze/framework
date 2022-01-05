<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Router;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;

/**
 * Cherche un objet et une méthode à exécuter en fonction de la requête HTTP.
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class Router
{
    /**
     * Requête courante
     *
     * @var RequestInterface
     */
    protected $currentRequest = null;

    /**
     * La base de l'URL de vos routes.
     *
     * @var string
     */
    protected $basePath = '';

    /**
     * Le container à transmetre aux objets appelé.
     *
     * @var ContainerInterface|null
     */
    protected $container;

    /**
     * Construit le router avec la liste des routes et les objets à appeler.
     *
     * @param ContainerInterface|null $container
     */
    public function __construct(?ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Appel un objet et sa méthode en fonction de la requête.
     *
     * @param RequestInterface $request
     *
     * @return array|null La route ou null si non trouvée.
     */
    public function parse(RequestInterface $request): ?array
    {
        /* Rempli un array des paramètres de l'Uri. */
        $query = $this->parseQueryFromRequest($request);

        $routesByMethod = Route::getRouteByMethod($request->getMethod());

        foreach ($routesByMethod as $key) {
            /** @var array $route */
            $route = Route::getRoute($key);

            if (!empty($route[ 'with' ])) {
                $path = $this->getRegexForPath($route[ 'path' ], $route[ 'with' ]);

                if (preg_match('/^(' . $path . ')$/', $query)) {
                    return $route;
                }
            } elseif ($route[ 'path' ] === $query) {
                /* Ajoute la clé de la route aux données. */
                return $route;
            }
        }

        return null;
    }

    /**
     * Exécute la méthode d'un contrôleur à partir d'une route et de la requête.
     *
     * @param array            $route
     * @param RequestInterface $request
     *
     * @return mixed Le retour de la méthode appelée.
     */
    public function execute(array $route, ?RequestInterface $request = null)
    {
        $class  = strstr($route[ 'uses' ], '@', true);
        $method = ltrim($route[ 'uses' ], $class . '@');

        /* Cherche les différents paramètres de l'URL pour l'injecter dans la méthode. */
        if (!empty($route[ 'with' ])) {
            $query  = $this->parseQueryFromRequest($request);
            $params = $this->parseParam($route[ 'path' ], $query, $route[ 'with' ]);
        }

        /* Ajoute la requête en dernier paramètre de fonction. */
        $params[] = $request ?? $this->currentRequest;

        $obj        = new $class();
        $reflection = new \ReflectionClass($obj);
        if ($reflection->hasProperty('container')) {
            $property = $reflection->getProperty('container');
            $property->setAccessible(true);
            $property->setValue($obj, $this->container);
        }

        return $reflection->getMethod($method)->invokeArgs($obj, $params);
    }

    /**
     * Créer une expression régulière à partir du chemin et des arguments d'une route.
     *
     * @param string $path  Chemin de la route.
     * @param array  $param Arguments de la route.
     *
     * @return string
     */
    public function getRegexForPath(string $path, array $param): string
    {
        array_walk($param, static function (&$with) {
            $with = str_replace([ '(', '/' ], [ '(?:', '\/' ], $with);
            $with = "($with)";
        });

        $str = str_replace([ '\\', '/' ], [ '//', '\/' ], $path);
        $key = array_keys($param);

        return str_replace($key, $param, $str);
    }

    /**
     * Retourne un chemin à partir du nom d'une route.
     *
     * @param string $name   Nom de la route.
     * @param array  $params Variables requises par la route.
     * @param bool   $strict Autorise la construction de routes partielles.
     *
     * @return string
     */
    public function getPath(
        string $name,
        ?array $params = null,
        bool $strict = true
    ): string {
        if (($route = Route::getRoute($name)) === null) {
            throw new Exception\RouteNotFoundException('The path does not exist.');
        }

        $path = $route[ 'path' ];
        foreach ($route[ 'with' ] as $key => $value) {
            if (!isset($params[ $key ])) {
                if ($strict) {
                    throw new \InvalidArgumentException(htmlspecialchars(
                        "the argument $key is missing"
                    ));
                }

                continue;
            }

            $value = str_replace([ '(', '/' ], [ '(?:', '\/' ], $value);
            if ($strict && !preg_match('/^' . $value . '$/', $params[ $key ])) {
                throw new Exception\RouteArgumentException($params[ $key ], $value, $path);
            }
            $path = str_replace($key, $params[ $key ], $path);
        }

        return $path;
    }

    /**
     * Retourne une route à partir de son nom.
     *
     * @param string $name   Nom de la route.
     * @param array  $params Variables requises par la route.
     * @param bool   $strict Autorise la construction de routes partielles.
     *
     * @return string
     */
    public function getRoute(
        string $name,
        ?array $params = null,
        bool $strict = true
    ): string {
        return $this->basePath . $this->getPath($name, $params, $strict);
    }

    /**
     * Retourne une instance de RequestInterface à partir du nom d'une route.
     *
     * @param string $name   Nom de la route.
     * @param array  $params Variables requises par la route.
     * @param bool   $strict Autorise la construction de routes partielles.
     *
     * @return RequestInterface
     */
    public function getRequestByRoute(
        string $name,
        ?array $params = null,
        bool $strict = true
    ): RequestInterface {
        $path = $this->getPath($name, $params, $strict);

        return $this->currentRequest->withUri(
            $this->currentRequest->getUri()->withPath($path)
        );
    }

    /**
     * Construit une route manuellement.
     *
     * @param string $path Le chemin de la route.
     *
     * @return string
     */
    public function makeRoute(string $path): string
    {
        return $this->basePath . $path;
    }

    /**
     * Ajoute la base de l'URL de vos routes (schéma + host + path - script_name).
     *
     * @param string $basePath
     *
     * @return $this
     */
    public function setBasePath(string $basePath): self
    {
        $this->basePath = $basePath;

        return $this;
    }

    /**
     * Retourne la base de votre URL.
     *
     * @return string L'URL.
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * Ajoute une nouvelle requête courante.
     *
     * @param RequestInterface $request
     *
     * @return $this
     */
    public function setRequest(RequestInterface $request): self
    {
        $this->currentRequest = $request;

        return $this;
    }

    /**
     * Parse les paramètres de la requête et retourne la chaine qui servira à
     *
     * @param RequestInterface $request
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function parseQueryFromRequest(?RequestInterface $request = null): string
    {
        if ($request === null && $this->currentRequest === null) {
            throw new \InvalidArgumentException('No request is provided.');
        }

        $path = $request === null
            ? $this->currentRequest->getUri()->getPath()
            : $request->getUri()->getPath();

        return $path === ''
            ? '/'
            : $path;
    }

    /**
     * Cherche dans la requête les paramètres présents dans la configuration
     * des routes pour l'appel dynamique de la fonction.
     *
     * @param string $route Route qui déclenche l'appel au contrôleur.
     * @param string $query Le paramètre de la requête.
     * @param array  $param Clés de comparaison à chercher dans la route.
     *
     * @return array Paramètres présents dans la requête.
     */
    public function parseParam(string $route, string $query, array $param): array
    {
        $path = $this->getRegexForPath($route, $param);

        if (preg_match("/$path/", $query, $matches)) {
            array_shift($matches);

            return $matches;
        }

        return [];
    }
}
