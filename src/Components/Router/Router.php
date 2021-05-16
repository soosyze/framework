<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Router;

use ArrayAccess;
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
     * Configuration des routes.
     *
     * @var array<string, scalar>|ArrayAccess<string, scalar>
     */
    protected $config = [];

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
     * @param string           $key_query Le préfixe de la route.
     *
     * @return array|null La route ou null si non trouvée.
     */
    public function parse(RequestInterface $request, string $key_query = 'q'): ?array
    {
        /* Rempli un array des paramètres de l'Uri. */
        $query  = $this->parseQueryFromRequest($request, $key_query);
        $routes = Route::getRouteByMethode($request->getMethod());

        foreach ($routes as $route) {
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
        $class   = strstr($route[ 'uses' ], '@', true);
        $methode = substr(strrchr($route[ 'uses' ], '@'), 1);

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
        } else {
            $obj->container = $this->container;
        }

        return $reflection->getMethod($methode)->invokeArgs($obj, $params);
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
            if ($strict && !isset($params[ $key ])) {
                throw new \InvalidArgumentException(htmlspecialchars(
                    "the argument $key is missing"
                ));
            }
            if (!$strict && !isset($params[ $key ])) {
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
     * @param string $name      Nom de la route.
     * @param array  $params    Variables requises par la route.
     * @param bool   $strict    Autorise la construction de routes partielles.
     * @param string $key_query Le préfixe de la route.
     *
     * @return string
     */
    public function getRoute(
        string $name,
        ?array $params = null,
        bool $strict = true,
        string $key_query = 'q'
    ): string {
        $prefix = !$this->isRewrite()
            ? "?$key_query="
            : '';

        return $this->basePath . $prefix . $this->getPath($name, $params, $strict);
    }

    /**
     * Retourne une instance de RequestInterface à partir du nom d'une route.
     *
     * @param string $name      Nom de la route.
     * @param array  $params    Variables requises par la route.
     * @param bool   $strict    Autorise la construction de routes partielles.
     * @param string $key_query Le préfixe de la route.
     *
     * @return RequestInterface
     */
    public function getRequestByRoute(
        string $name,
        ?array $params = null,
        bool $strict = true,
        string $key_query = 'q'
    ): RequestInterface {
        $path = $this->getPath($name, $params, $strict);

        return $this->currentRequest->withUri(
            $this->isRewrite()
                ? $this->currentRequest->getUri()->withPath($path)
                : $this->currentRequest->getUri()->withQuery("?$key_query=$path")
        );
    }

    /**
     * Construit une route manuellement.
     *
     * @param string $path      Le chemin de la route.
     * @param string $key_query Le préfixe de la route.
     *
     * @return string
     */
    public function makeRoute(string $path, string $key_query = 'q'): string
    {
        $prefix = $this->isRewrite()
            ? ''
            : "?$key_query=";

        return $this->basePath . $prefix . $path;
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
     * Les configurations pour le router :
     * (bool)settings.rewrite_engine Si les routes doivent tenir compte de la réécriture d'URL.
     *
     * @param mixed $config
     *
     * @return $this
     */
    public function setConfig($config): self
    {
        if (!(\is_array($config) || $config instanceof ArrayAccess)) {
            throw new \InvalidArgumentException('The configuration must be an array or an ArrayAccess instance.');
        }
        $this->config = $config;

        return $this;
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
     * Si le module de réécriture est activé et si la configuration l'exige.
     *
     * @return bool Si l'écriture d'url est possible.
     */
    public function isRewrite(): bool
    {
        return !empty($this->config[ 'settings.rewrite_engine' ]);
    }

    /**
     * Parse les paramètres de la requête et retourne la chaine qui servira à
     *
     * @param RequestInterface $request
     * @param string           $key_query Le préfixe de la route.
     *
     * @throws \InvalidArgumentException
     * @return string
     */
    public function parseQueryFromRequest(
        ?RequestInterface $request = null,
        string $key_query = 'q'
    ): string {
        if ($request === null && $this->currentRequest === null) {
            throw new \InvalidArgumentException('No request is provided.');
        }

        $uri = $request === null
            ? $this->currentRequest->getUri()
            : $request->getUri();

        if ($this->isRewrite()) {
            return $uri->getPath() === '/'
                ? '/'
                : ltrim($uri->getPath(), '/');
        }
        /* Rempli un array des paramètres de l'Uri. */
        parse_str($uri->getQuery(), $parseQuery);

        return $parseQuery[ $key_query ]
            ?? '/';
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
