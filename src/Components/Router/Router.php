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
    protected $currentRequest;

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
     * @var RouteCollection
     */
    protected $routeCollection;

    /**
     * Construit le router avec la liste des routes et les objets à appeler.
     */
    public function __construct(
        RouteCollection $routeCollection,
        ServerRequestInterface $serverRequest,
        ?ContainerInterface $container = null
    ) {
        $this->routeCollection = $routeCollection;
        $this->serverRequest = $serverRequest;
        $this->container     = $container;
    }

    /**
     * Appel un objet et sa méthode en fonction de la requête.
     *
     * @return Route|null La route ou null si non trouvée.
     */
    public function parse(RequestInterface $request): ?Route
    {
        /* Rempli un array des paramètres de l'Uri. */
        $requestPath = $this->getPathFromRequest($request);

        $method = $request->getHeaderLine('x-http-method-override');

        $routesByMethod = $this->routeCollection->getRoutesByMethod(
            $method === '' ? $request->getMethod() : $method
        );

        foreach ($routesByMethod as $key) {
            /** @var Route $route */
            $route = $this->routeCollection->getRoute($key);

            if (!empty($route->getWiths())) {
                $pattern = $route->getRegexForPath();

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
     * @param  RequestInterface $request
     * @return mixed            Le retour de la méthode appelée.
     */
    public function execute(Route $route, ?RequestInterface $request = null)
    {
        [ $className, $methodName ] = $route->getCallable();

        /* Cherche les différents paramètres de l'URL pour l'injecter dans la méthode. */
        $withs = $route->getWiths() === null
            ? $route->getWithsDefault()
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
     * Retourne un chemin à partir du nom d'une route.
     *
     * @param string $name   Nom de la route.
     * @param array  $withs  Variables requises par la route.
     * @param bool   $strict Autorise la construction de routes partielles.
     */
    public function generatePath(
        string $name,
        ?array $withs = null,
        bool $strict = true
    ): string {
        return $this->routeCollection
            ->tryGetRoute($name)
            ->generatePath($withs, $strict);
    }

    /**
     * Retourne une route à partir de son nom.
     *
     * @param string $name   Nom de la route.
     * @param array  $withs  Variables requises par la route.
     * @param bool   $strict Autorise la construction de routes partielles.
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
     */
    public function makeUrl(string $path): string
    {
        return $this->basePath . $path;
    }

    /**
     * Ajoute la base de l'URL de vos routes (schéma + host + path - script_name).
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
     * @return $this
     */
    public function setRequest(RequestInterface $request): self
    {
        $this->currentRequest = $request;

        return $this;
    }

    /**
     * Ajoute une nouvelle requête courante.
     *
     * @return $this
     */
    public function setServerRequest(ServerRequestInterface $serverRequest): self
    {
        $this->serverRequest = $serverRequest;

        return $this;
    }

    /**
     * Parse les paramètres de la requête et retourne la chaine qui servira à l'execution.
     *
     * @param RequestInterface $request
     *
     * @throws \InvalidArgumentException
     */
    public function getPathFromRequest(?RequestInterface $request = null): string
    {
        if ($request === null && $this->currentRequest === null) {
            throw new \InvalidArgumentException('No request is provided.');
        }

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
        if (preg_match('/' . $route->getRegexForPath() . '/', $this->getPathFromRequest($request), $matches)) {
            array_shift($matches);

            if ($route->getWithsDefault()) {
                $matches += $route->getWithsDefault();
            }

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
