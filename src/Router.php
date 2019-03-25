<?php

/**
 * Soosyze Framework http://soosyze.com
 *
 * @package Soosyze
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze;

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
     * @var array
     */
    protected $settings = [];
    
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
            $path = $this->relplaceSlash($route[ 'path' ]);

            if (isset($route[ 'with' ])) {
                $key_route = array_keys($route[ 'with' ]);
                $path      = str_replace($key_route, $route[ 'with' ], $path);

                if (preg_match('/^' . $path . '$/', $query)) {
                    return array_merge($route, [ 'key' => $key ]);
                }
            } elseif ($path === $query) {
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
     * Remplace les / par sa valeur encodé.
     *
     * @param string $str
     *
     * @return string
     */
    public function relplaceSlash($str)
    {
        return str_replace('/', '%2F', $str);
    }

    /**
     * Recherche une route à partir de son nom.
     *
     * @param string $name   Nom de la route.
     * @param array  $params Variables requises par la route.
     *
     * @return string
     */
    public function getRoute($name, array $params = null)
    {
        if (!isset($this->routes[ $name ])) {
            throw new RouteNotFoundException('The path does not exist.');
        }

        $route = $this->routes[ $name ];

        $prefix = !$this->isRewrite()
            ? '?'
            : '';

        $path = $route[ 'path' ];

        if (!empty($params) && isset($route[ 'with' ])) {
            foreach ($route[ 'with' ] as $key => $value) {
                if (!preg_match('/^' . $value . '$/', $params[ $key ])) {
                    throw new RouteArgumentException($params[ $key ], $value, $path);
                }
                $path = str_replace($key, $params[ $key ], $path);
            }
        }

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
     * Les configurations possibles pour le router.
     *
     * @param array $settings
     *
     * @return $this
     */
    public function setSettings(array $settings)
    {
        $this->settings = $settings;

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
        return !empty($this->settings[ 'RewriteEngine' ]) &&
            $this->settings[ 'RewriteEngine' ] == 'on' &&
            in_array('On', $this->currentRequest->getHeader('HTTP_MOD_REWRITE'));
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

        $req = $request === null
            ? $this->currentRequest
            : $request;

        /* Rempli un array des paramètres de l'Uri. */
        parse_str($req->getUri()->getQuery(), $parseQuery);

        /*
         * Pour avoir le paramètre non identifié par une clé.
         * Exemple : http://exemple.com/?first_param = 'first_param'
         * Il faut prendre le 1er élément du tableau inversé des paraètres de l'Uri.
         */
        return !empty($parseQuery)
            ? rawurlencode(array_keys($parseQuery)[ 0 ])
            : '%2F';
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
        $output     = [];
        $paramQuery = explode('%2F', $query);

        foreach (explode('/', $route) as $key => $value) {
            if (isset($param[ $value ])) {
                $output[] = $paramQuery[ $key ];
            }
        }

        return $output;
    }
}
