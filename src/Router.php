<?php

/**
 * Soosyze Framework http://soosyze.com
 * 
 * @package Soosyze
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze;

use Soosyze\Exception\Route\RouteNotFoundException,
    Soosyze\Exception\Route\RouteArgumentException,
    Psr\Http\Message\RequestInterface;

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
     * @var array
     */
    protected $objects = [];

    /**
     * Requête courante
     * @var RequestInterface
     */
    protected $currentRequest;

    /**
     * Configuration des routes.
     * 
     * @var array
     */
    protected $settings = [];

    /**
     * Construit le router avec la liste des routes et les objets à appeler.
     *
     * @param array $routes Liste des routes.
     * @param object[] $obj Liste des instances d'objet.
     */
    public function __construct( array $routes, array $obj = [] )
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
    public function parse( RequestInterface $request )
    {
        foreach( $this->routes as $key => $route )
        {
            $path = $this->relplaceSlash($route[ 'path' ]);

            if( isset($route[ 'with' ]) )
            {
                foreach( $route[ 'with' ] as $flag => $value )
                {
                    $path = str_replace($flag, $value, $path);
                }
            }

            /* Rempli un array des paramètres de l'Uri. */
            $query = $this->parseQueryFromRequest($request);

            if( preg_match("/^" . $path . "$/", $query) )
            {
                /* Ajoute la clé de la route aux données. */
                $route[ 'key' ] = $key;
                return $route;
            }
        }
        return null;
    }

    /**
     * Exécute la méthode d'un contrôleur à partir d'une route et de la requête.
     *
     * @param array $route
     * @param Request $request 
     *
     * @return mixed Le retour de la méthode appelée.
     */
    public function execute( array $route, RequestInterface $request = null )
    {
        $class   = strstr($route[ 'uses' ], '@', true);
        $methode = substr(strrchr($route[ 'uses' ], '@'), 1);

        /* Cherche les différents paramètres de l'URL pour l'injecter dans la méthode. */
        if( isset($route[ 'with' ]) )
        {
            $query  = $this->parseQueryFromRequest($request);
            $params = $this->parseParam($route[ 'path' ], $query, $route[ 'with' ]);
        }

        /* Ajoute la requête en dernier paramètre de fonction. */
        $params[] = $request === null
            ? $this->currentRequest
            : $request;

        /* Créer l'objet si celui-ci n'existe pas. */
        $obj = !empty($this->objects[ $class ])
            ? $this->objects[ $class ]
            : new $class();
        
        $output = call_user_func_array([ $obj, $methode ], $params);
        
        return $output;
    }

    /**
     * Parse les paramètres de la requête et retourne la chaine qui servira à 
     * 
     * @param RequestInterface $request
     * 
     * @return string
     * 
     * @throws \InvalidArgumentException 
     */
    protected function parseQueryFromRequest( RequestInterface $request = null )
    {
        if( $request === null && $this->currentRequest === null )
        {
            throw new \InvalidArgumentException();
        }

        $req = $request === null
            ? $this->currentRequest
            : $request;

        /* Rempli un array des paramètres de l'Uri. */
        parse_str($req->getUri()->getQuery(), $parseQuery);

        /**
         * Pour avoir le paramètre non identifié par une clé.
         * Exemple : http://exemple.com/?first_param = 'first_param'
         * Il faut prendre le 1er élément du tableau inversé des paraètres de l'Uri.
         */
        $query = !empty($parseQuery)
            ? rawurlencode(array_keys($parseQuery)[ 0 ])
            : '%2F';
        return $query;
    }

    /**
     * Cherche dans la requête les paramètres présents dans la configuration 
     * des routes pour l'appel dynamique de la fonction.
     *
     * @param string $route Route qui déclenche l'appel au contrôleur.
     * @param string $query Le paramètre de la requête.
     * @param array $param Clés de comparaison à chercher dans la route.
     *
     * @return array Paramètres présents dans la requête.
     */
    protected function parseParam( $route, $query, array $param )
    {
        $paramOutput = [];
        $paramRoute  = explode("/", $route);
        $paramQuery  = explode("%2F", $query);

        foreach( $paramRoute as $key => $value )
        {
            if( array_key_exists($value, $param) )
            {
                $paramOutput[] = $paramQuery[ $key ];
            }
        }
        return $paramOutput;
    }

    /**
     * remplace les / par sa valeur encodé.
     *
     * @param string $str
     *
     * @return string
     */
    public function relplaceSlash( $str )
    {
        return str_replace('/', '%2F', $str);
    }

    /**
     * Recherche une route à partir de son nom.
     *
     * @param string $name Nom de la route.
     * @param array $params Variables requises par la route.
     *
     * @return string
     */
    public function getRoute( $name, array $params = null )
    {
        if( !isset($this->routes[ $name ]) )
        {
            throw new RouteNotFoundException('The path does not exist.');
        }

        $route = $this->routes[ $name ];

        $prefix = !$this->isRewrite()
            ? '?'
            : '';

        $path = $route[ 'path' ];

        if( !empty($params) && isset($route[ 'with' ]) )
        {
            foreach( $route[ 'with' ] as $key => $value )
            {
                if( !preg_match("/^" . $value . "$/", $params[ $key ]) )
                {
                    throw new RouteArgumentException($params[ $key ], $value, $path);
                }
                $path = str_replace($key, $params[ $key ], $path);
            }
        }

        return $this->getBasePath() . $prefix . $path;
    }

    /**
     * Retourne la base de votre URL.
     * 
     * @return string L'url.
     */
    public function getBasePath()
    {
        return $this->currentRequest->getUri()->getBasePath();
    }

    /**
     * Les configurations possibles pour le router.
     * 
     * @param array $settings
     */
    public function setSettings( array $settings )
    {
        $this->settings = $settings;
        return $this;
    }

    /**
     * Ajout des objets à instancier lors de l'appel.
     *
     * @param object[] $obj
     */
    public function setObjects( array $obj )
    {
        $this->objects = $obj;
        return $this;
    }

    /**
     * Ajoute une nouvelle requête courante.
     * 
     * @param RequestInterface $request
     */
    public function setRequest( RequestInterface $request )
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
}