<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @package Soosyze
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze;

use ArrayAccess;
use Psr\Http\Message\RequestInterface;
use Soosyze\Exception\Route\RouteArgumentException;
use Soosyze\Exception\Route\RouteNotFoundException;

/**
 * Cherche un objet et une méthode à exécuter en fonction de la requête HTTP.
 *
 * @author Mathieu NOËL
 */
class Router
{
    /**
     * Routes à parser.
     *
     * @var array
     */
    protected $routes = [];

    /**
     * Objets à appeler.
     *
     * @var object[]
     */
    protected $objects = [];

    /**
     * Requête courante
     *
     * @var RequestInterface
     */
    protected $currentRequest = null;

    /**
     * Configuration des routes.
     *
     * @var array|ArrayAccess
     */
    protected $config = [];
    
    /**
     * La base de l'URL de vos routes.
     *
     * @var string
     */
    protected $basePath = '';

    /**
     * Construit le router avec la liste des routes et les objets à appeler.
     *
     * @param array    $routes Liste des routes.
     * @param object[] $obj    Liste des instances d'objet.
     */
    public function __construct(array $routes, array $obj = [])
    {
        $this->routes  = $routes;
        $this->objects = $obj;
    }

    /**
     * Appel un objet et sa méthode en fonction de la requête.
     *
     * @param RequestInterface $request
     *
     * @return array|null La route ou null si non trouvée.
     */
    public function parse(RequestInterface $request)
    {
        /* Rempli un array des paramètres de l'Uri. */
        $query = $this->parseQueryFromRequest($request);
        foreach ($this->routes as $key => $route) {
            if (strtoupper($route[ 'methode' ]) !== $request->getMethod()) {
                continue;
            }

            if (isset($route[ 'with' ])) {
                $path = $this->getRegexForPath($route[ 'path' ], $route[ 'with' ]);

                if (preg_match('/^(' . $path . ')$/', $query)) {
                    return array_merge($route, [ 'key' => $key ]);
                }
            } elseif ($route[ 'path' ] === $query) {
                /* Ajoute la clé de la route aux données. */
                return array_merge($route, [ 'key' => $key ]);
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
    public function execute(array $route, RequestInterface $request = null)
    {
        $class   = strstr($route[ 'uses' ], '@', true);
        $methode = substr(strrchr($route[ 'uses' ], '@'), 1);

        /* Cherche les différents paramètres de l'URL pour l'injecter dans la méthode. */
        if (isset($route[ 'with' ])) {
            $query  = $this->parseQueryFromRequest($request);
            $params = $this->parseParam($route[ 'path' ], $query, $route[ 'with' ]);
        }

        /* Ajoute la requête en dernier paramètre de fonction. */
        $params[] = ($request === null)
            ? $this->currentRequest
            : $request;

        /* Créer l'objet si celui-ci n'existe pas. */
        $obj = !empty($this->objects[ $class ])
            ? $this->objects[ $class ]
            : new $class();

        return call_user_func_array([ $obj, $methode ], $params);
    }

    /**
     * Créer une expression régulière à partir du chemin et des arguments d'une route.
     *
     * @param string $path  Chemin de la route.
     * @param array  $param Arguments de la route.
     *
     * @return string
     */
    public function getRegexForPath($path, array $param)
    {
        array_walk($param, function (&$with) {
            $with = str_replace([ '(', '/' ], [ '(?:', '\/' ], $with);
            $with = "($with)";
        });
        
        $str = str_replace(['\\', '/'], [ '//', '\/'], $path);
        $key = array_keys($param);
        
        return str_replace($key, $param, $str);
    }

    /**
     * Recherche une route à partir de son nom.
     *
     * @param string $name   Nom de la route.
     * @param array  $params Variables requises par la route.
     * @param bool   $strict Autorise la construction de routes partielles.
     *
     * @return string
     */
    public function getRoute($name, array $params = null, $strict = true)
    {
        if (!isset($this->routes[ $name ])) {
            throw new RouteNotFoundException('The path does not exist.');
        }

        $route = $this->routes[ $name ];
        $path  = $route[ 'path' ];

        if (isset($route[ 'with' ])) {
            foreach ($route[ 'with' ] as $key => $value) {
                if ($strict && !isset($params[$key])) {
                    throw new \InvalidArgumentException(htmlspecialchars(
                        "the argument $key is missing"
                    ));
                }
                if (!$strict && !isset($params[$key])) {
                    continue;
                }
                $value = str_replace([ '(', '/' ], [ '(?:', '\/' ], $value);
                if ($strict && !preg_match('/^' . $value . '$/', $params[ $key ])) {
                    throw new RouteArgumentException($params[ $key ], $value, $path);
                }
                $path = str_replace($key, $params[ $key ], $path);
            }
        }
        $prefix = !$this->isRewrite()
            ? '?q='
            : '';

        return $this->basePath . $prefix . $path;
    }

    /**
     * Ajoute la base de l'URL de vos routes (schéma + host + path - script_name).
     *
     * @param string $basePath
     *
     * @return $this
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;

        return $this;
    }

    /**
     * Retourne la base de votre URL.
     *
     * @return string L'URL.
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * Les configurations pour le router :
     * (bool)settings.rewrite_engine Si les routes doivent tenir compte de la réécriture d'URL.
     *
     * @param array|ArrayAccess $config
     *
     * @return $this
     */
    public function setConfig($config)
    {
        if (!\is_array($config) && !($config instanceof \ArrayAccess)) {
            throw new \InvalidArgumentException('The configuration must be an ArrayAccess array or instance.');
        }
        $this->config = $config;

        return $this;
    }

    /**
     * Ajout des objets à instancier lors de l'appel.
     *
     * @param object[] $obj
     *
     * @return $this
     */
    public function setObjects(array $obj)
    {
        $this->objects = $obj;

        return $this;
    }

    /**
     * Ajoute une nouvelle requête courante.
     *
     * @param RequestInterface $request
     *
     * @return $this
     */
    public function setRequest(RequestInterface $request)
    {
        $this->currentRequest = $request;

        return $this;
    }

    /**
     * Si le module de réécriture est activé et si la configuration l'exige.
     *
     * @return bool Si l'écriture d'url est possible.
     */
    public function isRewrite()
    {
        return !empty($this->config[ 'settings.rewrite_engine' ]);
    }

    /**
     * Parse les paramètres de la requête et retourne la chaine qui servira à
     *
     * @param RequestInterface $request
     *
     * @throws \InvalidArgumentException
     * @return string
     */
    public function parseQueryFromRequest(RequestInterface $request = null)
    {
        if ($request === null && $this->currentRequest === null) {
            throw new \InvalidArgumentException('No request is provided.');
        }

        $uri = $request === null
            ? $this->currentRequest->getUri()
            : $request->getUri();

        /* Rempli un array des paramètres de l'Uri. */
        parse_str($uri->getQuery(), $parseQuery);

        return !empty($parseQuery['q'])
            ? $parseQuery['q']
            : '/';
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
    public function parseParam($route, $query, array $param)
    {
        $path = $this->getRegexForPath($route, $param);

        if (preg_match("/$path/", $query, $matches)) {
            array_shift($matches);

            return $matches;
        }

        return [];
    }
}
