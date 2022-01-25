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
use Psr\Http\Message\ServerRequestInterface;
use Soosyze\Components\Router\Exception\RouteArgumentException;
use Soosyze\Components\Router\Exception\RouteNotFoundException;

/**
 * Cherche un objet et une méthode à exécuter en fonction de la requête HTTP.
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
final class Router
{
    /**
     * Requête courante
     *
     * @var RequestInterface
     */
    protected $currentRequest = null;

    /**
     * @var ServerRequestInterface
     */
    protected $serverRequest;

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
     * @param ServerRequestInterface  $serverRequest
     * @param ContainerInterface|null $container
     */
    public function __construct(ServerRequestInterface $serverRequest, ?ContainerInterface $container = null)
    {
        $this->serverRequest = $serverRequest;
        $this->container     = $container;
    }

    /**
     * Appel un objet et sa méthode en fonction de la requête.
     *
     * @param RequestInterface $request
     *
     * @return Route|null La route ou null si non trouvée.
     */
    public function parse(RequestInterface $request): ?Route
    {
        /* Rempli un array des paramètres de l'Uri. */
        $requestPath = $this->getPathFromRequest($request);

        $method = $request->getHeaderLine('x-http-method-override');

        $routesByMethod = RouteCollection::getRoutesByMethod(
            $method === '' ? $request->getMethod() : $method
        );

        foreach ($routesByMethod as $key) {
            /** @var Route $route */
            $route = RouteCollection::getRoute($key);

            if (!empty($route->getWiths())) {
                $pattern = $this->getRegexForPath($route->getPath(), $route->getWiths());

                if (preg_match('/^(' . $pattern . ')$/', $requestPath)) {
                    return $route;
                }
            } elseif ($route->getPath() === $requestPath) {
                /* Ajoute la clé de la route aux données. */
                return $route;
            }
        }

        return null;
    }

    /**
     * Exécute la méthode d'un contrôleur à partir d'une route et de la requête.
     *
     * @param Route            $route
     * @param RequestInterface $request
     *
     * @return mixed Le retour de la méthode appelée.
     */
    public function execute(Route $route, ?RequestInterface $request = null)
    {
        [ $className, $methodName ] = $route->getCallable();

        /* Cherche les différents paramètres de l'URL pour l'injecter dans la méthode. */
        $withs = $route->getWiths() === null
            ? null
            : $this->parseWiths($route, $request);

        $controller = new $className();
        $reflection = new \ReflectionClass($controller);

        if ($reflection->hasProperty('container')) {
            $property = $reflection->getProperty('container');
            $property->setAccessible(true);
            $property->setValue($controller, $this->container);
        }

        /** @var \ReflectionMethod $reflectionMethod */
        $reflectionMethod = $reflection->getMethod($methodName);

        $args = $this->getArgs($reflectionMethod, $withs);

        return $reflectionMethod->invokeArgs($controller, $args);
    }

    /**
     * Créer une expression régulière à partir du chemin et des arguments d'une route.
     *
     * @param string $path  Chemin de la route.
     * @param array  $withs Arguments de la route.
     *
     * @return string
     */
    public function getRegexForPath(string $path, array $withs): string
    {
        array_walk($withs, static function (&$with, $key): void {
            $with = str_replace([ '(', '/' ], [ '(?:', '\/' ], $with);
            $key  = trim($key, ':{}');
            $with = "(?<$key>$with)";
        });

        $pattern = str_replace([ '\\', '/' ], [ '//', '\/' ], $path);
        $keys    = array_keys($withs);

        return str_replace($keys, $withs, $pattern);
    }

    /**
     * Retourne un chemin à partir du nom d'une route.
     *
     * @param string $name   Nom de la route.
     * @param array  $withs  Variables requises par la route.
     * @param bool   $strict Autorise la construction de routes partielles.
     *
     * @return string
     */
    public function generatePath(
        string $name,
        ?array $withs = null,
        bool $strict = true
    ): string {
        if (($route = RouteCollection::getRoute($name)) === null) {
            throw new RouteNotFoundException('The path does not exist.');
        }

        $path = $route->getPath();
        if ($route->getWiths() === null) {
            return $path;
        }

        foreach ($route->getWiths() as $key => $value) {
            if ($strict && !isset($withs[ $key ])) {
                throw new \InvalidArgumentException(htmlspecialchars(
                    "the argument $key is missing"
                ));
            }
            if (!$strict && !isset($withs[ $key ])) {
                continue;
            }
            $pattern = str_replace([ '(', '/' ], [ '(?:', '\/' ], $value);
            /** @phpstan-var array $withs */
            if ($strict && !preg_match('/^' . $pattern . '$/', (string) $withs[ $key ])) {
                throw new RouteArgumentException($withs[ $key ], $pattern, $path);
            }
            $path = str_replace($key, $withs[ $key ], $path);
        }

        return $path;
    }

    /**
     * Retourne une route à partir de son nom.
     *
     * @param string $name   Nom de la route.
     * @param array  $withs  Variables requises par la route.
     * @param bool   $strict Autorise la construction de routes partielles.
     *
     * @return string
     */
    public function generateUrl(
        string $name,
        ?array $withs = null,
        bool $strict = true
    ): string {
        return $this->basePath . $this->generatePath($name, $withs, $strict);
    }

    /**
     * Retourne une instance de RequestInterface à partir du nom d'une route.
     *
     * @param string $name   Nom de la route.
     * @param array  $withs  Variables requises par la route.
     * @param bool   $strict Autorise la construction de routes partielles.
     *
     * @return RequestInterface
     */
    public function generateRequest(
        string $name,
        ?array $withs = null,
        bool $strict = true
    ): RequestInterface {
        $path = $this->generatePath($name, $withs, $strict);

        /** @phpstan-var array $parseUrl */
        $parseUrl = parse_url($this->basePath);

        return $this->currentRequest->withUri(
            $this->currentRequest->getUri()->withPath($parseUrl[ 'path' ] ?? '' . $path)
        );
    }

    /**
     * Construit une route manuellement.
     *
     * @param string $path Le chemin de la route.
     *
     * @return string
     */
    public function makeUrl(string $path): string
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
        $this->basePath = rtrim($basePath, '/');

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
     * Parse les paramètres de la requête et retourne la chaine qui servira à l'execution.
     *
     * @param RequestInterface $request
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function getPathFromRequest(?RequestInterface $request = null): string
    {
        if ($request === null && $this->currentRequest === null) {
            throw new \InvalidArgumentException('No request is provided.');
        }

        /** @var \Psr\Http\Message\UriInterface $uri */
        $uri = $request === null
            ? $this->currentRequest->getUri()
            : $request->getUri();

        /** @var array $parseUrl */
        $parseUrl = parse_url($this->basePath);

        $path = ltrim($uri->getPath(), $parseUrl[ 'path' ] ?? '');

        return $path === '' || $path === '/'
            ? '/'
            : $path;
    }

    /**
     * Cherche dans la requête les paramètres présents dans la configuration
     * de la routes pour l'appel dynamique de la fonction.
     *
     * @param Route                 $route   La route qui déclenche l'appel au contrôleur.
     * @param RequestInterface|null $request La requête
     *
     * @return array Paramètres présents dans la requête.
     */
    public function parseWiths(Route $route, ?RequestInterface $request = null): array
    {
        $path    = $this->getPathFromRequest($request);
        $pattern = $this->getRegexForPath($route->getPath(), $route->getWiths() ?? []);

        if (preg_match("/$pattern/", $path, $matches)) {
            array_shift($matches);

            return array_filter($matches, static function ($key): bool {
                return !is_int($key);
            }, ARRAY_FILTER_USE_KEY);
        }

        return [];
    }

    protected function getArgs(\ReflectionMethod $reflectionMethod, ?array $withs): array
    {
        $args = [];
        /** @var \ReflectionParameter $parameter */
        foreach ($reflectionMethod->getParameters() as $parameter) {
            $parameterName = $parameter->name;
            if (isset($withs[ $parameterName ])) {
                $args[$parameterName] = $withs[ $parameterName ];

                continue;
            }

            $parameterType = $parameter->getType();
            if ($parameterType instanceof \ReflectionNamedType && $parameterType->getName() === ServerRequestInterface::class) {
                $args[ $parameterName ] = $this->serverRequest;

                continue;
            }

            if (!$parameter->isOptional()) {
                throw new \RuntimeException("The $parameterName parameter is absent");
            }
        }

        return $args;
    }
}
