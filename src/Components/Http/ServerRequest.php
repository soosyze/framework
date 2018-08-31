<?php

/**
 * Soosyze Framework http://soosyze.com
 *
 * @package Soosyze\Components\Http
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Http;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Représentation d'une requête HTTP entrante, côté serveur.
 *
 * @link https://www.php-fig.org/psr/psr-7/ PSR-7: HTTP message interfaces
 *
 * @author Mathieu NOËL
 */
class ServerRequest extends Request implements ServerRequestInterface
{
    /**
     * Paramètres du serveur ($_SERVER).
     *
     * @var array
     */
    protected $serverParams = [];

    /**
     * Les cookies ($_COOKIE).
     *
     * @var type
     */
    protected $cookieParams = [];

    /**
     * Paramètres de la requête ($_GET).
     *
     * @var array
     */
    protected $queryParams = [];

    /**
     * Fichiers transmis au serveur ($_FILES).
     *
     * @var array
     */
    protected $uploadFiles = [];

    /**
     * Les attribues.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * Le corps de la requête.
     *
     * @var null|array|object
     */
    protected $parseBody = null;

    /**
     * Construit une requête coté serveur.
     *
     * @param string $method Méthode HTTP ('GET'|'POST'|...).
     * @param \Soosyze\Components\Http\UriInterface $uri L'URI de la requête.
     * @param array $headers Les en-têtes du message.
     * @param \Soosyze\Components\Http\StreamInterface $body Corp de la requête.
     * @param string $version La version du protocole HTTP.
     * @param array $serverParams Paramètres de la requête.
     * @param array $cookies Les cookies.
     * @param array $uploadFiles Fichiers transmis au serveur.
     */
    public function __construct(
    $method,
        \Psr\Http\Message\UriInterface $uri,
        array $headers = [],
        \Psr\Http\Message\StreamInterface $body = null,
        $version = '1.1',
        array $serverParams = [],
        array $cookies = [],
        array $uploadFiles = []
    ) {
        parent::__construct($method, $uri, $headers, $body, $version);
        $this->serverParams = $serverParams;
        $this->cookieParams = $cookies;
        $this->uploadFiles  = $uploadFiles;
    }

    /**
     * Construit une requête à partir des paramètre du serveur.
     *
     * @return ServerRequest
     */
    public static function create()
    {
        $scheme = isset($_SERVER[ 'HTTPS' ]) && ($_SERVER[ 'HTTPS' ] == 'on' || $_SERVER[ 'HTTPS' ] == 1) ||
            isset($_SERVER[ 'HTTP_X_FORWARDED_PROTO' ]) && $_SERVER[ 'HTTP_X_FORWARDED_PROTO' ] == 'https'
            ? 'https'
            : 'http';

        /* Construit le Uri de la requête */
        $uri      = Uri::create($scheme . '://' . $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'REQUEST_URI' ]);
        $headers  = function_exists('getallheaders')
            ? getallheaders()
            : [];
        $protocol = isset($_SERVER[ 'SERVER_PROTOCOL' ])
            ? str_replace('HTTP/', '', $_SERVER[ 'SERVER_PROTOCOL' ])
            : '1.1';

        $request = new ServerRequest(
            $_SERVER[ 'REQUEST_METHOD' ],
            $uri,
            $headers,
            new Stream(),
            $protocol,
            $_SERVER,
            $_COOKIE,
            $_FILES
        );

        return $request->withParsedBody($_POST)
                ->withQueryParams($_GET);
    }

    /**
     * Récupérer les paramètres du serveur.
     *
     * Récupère les données liées à l'environnement de demande entrante,
     * typiquement dérivé de superglobal $_SERVER de PHP. Les données ne sont PAS
     * REQUIS pour provenir de $_SERVER.
     *
     * @return array
     */
    public function getServerParams()
    {
        return $this->serverParams;
    }

    /**
     * Récupérer les cookies.
     *
     * Récupère les cookies envoyés par le client au serveur.
     *
     * Les données DOIVENT être compatibles avec la structure du $_COOKIE
     * superglobal.
     *
     * @return array
     */
    public function getCookieParams()
    {
        return $this->cookieParams;
    }

    /**
     * Renvoyer une instance avec les cookies spécifiés.
     *
     * Les données ne sont PAS OBLIGATOIRES pour provenir de la superglobale $_COOKIE, mais DOIVENT
     * être compatible avec la structure de $_COOKIE. Typiquement, ces données seront
     * être injecté à l'instanciation.
     *
     * @param array $cookies Tableau de paires clé/valeur représentant les cookies.
     *
     * @return static
     */
    public function withCookieParams(array $cookies)
    {
        $clone               = clone $this;
        $clone->cookieParams = $cookies;

        return $clone;
    }

    /**
     * Récupérer les arguments de chaîne de requête.
     *
     * Remarque: les paramètres de requête peuvent ne pas être synchronisés avec l'URI ou le serveur
     * params. Si vous devez vous assurer que vous n'obtenez que l'original
     * valeurs, vous devrez peut-être analyser la chaîne de requête à partir de `getUri()->getQuery()`
     * ou à partir du paramètre du serveur `QUERY_STRING`.
     *
     * @return array
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }

    /**
     * Renvoie une instance avec les arguments de chaîne de requête spécifiés.
     *
     * @param array $query Tableau d'arguments de chaîne de requête, généralement de
     * $_GET.
     *
     * @return static
     */
    public function withQueryParams(array $query)
    {
        $clone              = clone $this;
        $clone->queryParams = $query;

        return $clone;
    }

    /**
     * Récupérer les données de téléchargement de fichier normalisées.
     *
     * Ces valeurs PEUVENT être préparées à partir de $_FILES ou du corps du message pendant
     * instanciation, ou PEUT être injecté via withUploadedFiles().
     *
     * @return array Arbre de tableau des instances de UploadedFileInterface; un vide
     * Le tableau DOIT être retourné si aucune donnée n'est présente.
     */
    public function getUploadedFiles()
    {
        return $this->uploadFiles;
    }

    /**
     * Créer une nouvelle instance avec les fichiers téléchargés spécifiés.
     *
     * @param array $uploadedFiles Arbre de tableau des instances de UploadedFileInterface.
     *
     * @return static
     *
     * @throws \InvalidArgumentException Les contenus doivent être tous des instance d'UploadedFileInterface.
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        foreach ($uploadedFiles as $value) {
            if (!$value instanceof UploadedFile) {
                throw new \InvalidArgumentException('The contents must all be instances of UploadedFileInterface.');
            }
        }
        $clone              = clone $this;
        $clone->uploadFiles = $uploadedFiles;

        return $clone;
    }

    /**
     * Récupérer tous les paramètres fournis dans le corps de la requête.
     *
     * @return null|array|object Les paramètres de corps désérialisés, le cas échéant.
     * Ceux-ci seront généralement un tableau ou un objet.
     */
    public function getParsedBody()
    {
        return $this->parseBody;
    }

    /**
     * Renvoie une instance avec les paramètres de corps spécifiés.
     *
     * Si la demande Content-Type est soit application / x-www-form-urlencoded
     * ou multipart / form-data, et la méthode de requête est POST, utilisez cette méthode
     * SEULEMENT pour injecter le contenu de $_POST.
     *
     * @param null|array|object $data Données du corps désérialisées.
     *
     * @return static
     *
     * @throws \InvalidArgumentException Si un type d'argument non pris en charge est
     * à condition de.
     */
    public function withParsedBody($data)
    {
        $clone            = clone $this;
        $clone->parseBody = $data;

        return $clone;
    }

    /**
     * Récupérer les attributs dérivés de la requête.
     *
     * La requête "attributs" peut être utilisée pour permettre l'injection de
     * paramètres dérivés de la requête: par exemple, les résultats du chemin
     * opérations de correspondance; les résultats des cookies décryptés; les résultats de
     * désérialiser des corps de message codés sans forme; etc. Attributs
     * sera spécifique à l'application et à la demande, et peut être mutable.
     *
     * @return array Attributs dérivés de la requête.
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Récupérer un seul attribut de requête dérivé.
     *
     * Récupère un seul attribut de requête dérivé comme décrit dans
     * getAttributes(). Si l'attribut n'a pas été défini précédemment, renvoie
     * la valeur par défaut fournie.
     *
     * @see getAttributes()
     *
     * @param string $name Nom de l'attribut.
     * @param mixed $default Valeur par défaut à renvoyer si l'attribut n'existe pas.
     *
     * @return mélangé
     */
    public function getAttribute($name, $default = null)
    {
        return isset($this->attributes[ $name ])
            ? $this->attributes[ $name ]
            : $default;
    }

    /**
     * Renvoie une instance avec l'attribut de requête dérivé spécifié.
     *
     * Cette méthode permet de définir un seul attribut de requête dérivé
     * décrit dans getAttributes().
     *
     * Cette méthode DOIT être mise en œuvre de manière à conserver les
     * immutabilité du message, et DOIT retourner une instance qui a le
     * attribut mis à jour.
     *
     * @see getAttributes()
     *
     * @param string $name Nom de l'attribut.
     * @param mixed $value Valeur de l'attribut.
     *
     * @return static
     */
    public function withAttribute($name, $value)
    {
        $clone                      = clone $this;
        $clone->attributes[ $name ] = $value;

        return $clone;
    }

    /**
     * Renvoie une instance qui supprime l'attribut de requête dérivé spécifié.
     *
     * Cette méthode permet de supprimer un seul attribut de requête dérivé
     * décrit dans getAttributes().
     *
     * @see getAttributes ()
     *
     * @param string $name Nom de l'attribut.
     *
     * @return static
     */
    public function withoutAttribute($name)
    {
        $clone = clone $this;
        if (isset($clone->attributes[ $name ])) {
            unset($clone->attributes[ $name ]);
        }

        return $clone;
    }
}
