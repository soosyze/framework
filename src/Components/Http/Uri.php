<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @package Soosyze\Components\Http
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Http;

use Psr\Http\Message\UriInterface;

/**
 * Implementation de l'interface UriInterface. Classe Représentant les adresses URI selon RFC 3986 et à
 * fournir des méthodes pour la plupart des opérations courantes.
 *
 * @link https://en.wikipedia.org/wiki/Uniform_Resource_Identifier
 * @link https://www.php-fig.org/psr/psr-7/ PSR-7: HTTP message interfaces
 *
 * @author Mathieu NOËL
 */
class Uri implements UriInterface
{
    const CHAR_UNRESERVED = 'a-zA-Z0-9_\-\.~';

    const CHAR_SUB_DELIMS = '!\$&\'\(\)\*\+,;=';

    /**
     * Schéma de l'URI (http(s)|ftp|mailto|file...).
     *
     * @var string
     */
    protected $scheme = '';

    /**
     * Utilisateur (partie de l'authority).
     *
     * @var string
     */
    protected $user = '';

    /**
     * Mot de passe (partie de l'authority).
     *
     * @var string
     */
    protected $pass = '';

    /**
     * Nom d'hôte, nom enregistré ou une adresse IP (partie de l'authority).
     *
     * @var string
     */
    protected $host = '';

    /**
     * Port (80 pour le http, 443 pour le https).
     *
     * @var int
     */
    protected $port = '';

    /**
     * Chemin de l'URI.
     *
     * @var string
     */
    protected $path = '';

    /**
     * Requête encodée.
     *
     * @var string
     */
    protected $query = '';

    /**
     * Fragment (ou ancre) encodée.
     *
     * @var string
     */
    protected $fragment = '';

    /**
     * Les ports supportés.
     *
     * @var array
     */
    protected $ports = [
        'ftp'   => 21,
        'http'  => 80,
        'ldap'  => 389,
        'https' => 443
    ];

    /**
     * Construit une URI à partir de chacun de ses attributs.
     *
     * @param string   $scheme   $_SERVER['REQUEST_SCHEME']
     * @param string   $host     $_SERVER['HTTP_HOST']
     * @param string   $path     $_SERVER['PHP_SELF']
     * @param int|null $port     $_SERVER['SERVER_PORT']
     * @param string   $query    $_SERVER['QUERY_STRING']
     * @param string   $fragment
     * @param string   $user
     * @param string   $password
     */
    public function __construct(
        $scheme = '',
        $host = '',
        $path = '/',
        $port = null,
        $query = '',
        $fragment = '',
        $user = '',
        $password = ''
    ) {
        $this->scheme   = $this->filterScheme($scheme);
        $this->host     = $this->filterStringToLower($host);
        $this->path     = $this->filterPath($path);
        $this->query    = $this->filterQuery($query);
        $this->port     = $this->filterPort($port);
        $this->fragment = $this->filterFragment($fragment);
        $this->user     = $this->filterString($user);
        $this->pass     = $this->filterString($password);
    }

    /**
     * Renvoie la représentation sous forme de chaîne en tant que référence d'URI.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-4.1
     *
     * @return string L'URI.
     */
    public function __toString()
    {
        $uri = $this->scheme !== ''
            ? $this->scheme . ':'
            : '';
        $uri .= $this->getAuthority() !== ''
            ? '//' . $this->getAuthority()
            : '';
        $uri .= preg_match('/^\/.*/', $this->path)
            ? $this->path
            : '/' . $this->path;
        $uri .= $this->query !== ''
            ? '?' . $this->query
            : '';

        return $uri .= $this->fragment !== ''
            ? '#' . $this->fragment
            : '';
    }

    /**
     * Retourne le schéma de l'URI normalisé en minuscule sans le caractère ":".
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.1
     *
     * @return string Schéma de l'URI ou une chaine vide.
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Retourne l'autorité de l'URI ou une chaine vide
     * si l'information d'autorité n'est pas présente.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-3.2*
     *
     * @return string Autorité de l'URI, au format "[user-info@]host[:port]".
     */
    public function getAuthority()
    {
        $authority = $this->getUserInfo()
            ? $this->getUserInfo() . '@'
            : '';
        $authority .= $this->host;

        return $authority .= $this->port
            ? ':' . $this->port
            : '';
    }

    /**
     * Retourne les informations utilisateur de l'URI
     * ou une chaine vide s'il n'y a aucune information.
     *
     * @return string Informations de l'utilisateur de l'URI,
     *                au format "nom d'utilisateur[:mot de passe]".
     */
    public function getUserInfo()
    {
        if ($this->user !== '') {
            return $this->user . ($this->pass !== ''
                ? ':' . $this->pass
                : '');
        }

        return '';
    }

    /**
     * Retourne l'hôte de l'URI normalisé en minuscule
     * ou une chaine vide si l'hôte est absent.
     *
     * @see http://tools.ietf.org/html/rfc3986#section-3.2.2
     *
     * @return string Hôte de l'URI.
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Retourne le port de l'URI si il présent et non standard pour le schéma actuel,
     * sinon retourne null.
     *
     * @return null|int Port de l'URI.
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Retourne le chemin de l'URI encodé.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.3
     *
     * @return string Chemin de l'URI.
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Retourne la chaîne de requête de l'URI encodé.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.4
     *
     * @return string Chaîne de requête de l'URI.
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Retourne le fragment de l'URI encodée en pourcentage sans
     * le caractère "#" principale, sinon une chaine vide.
     *
     * @see https://tools.ietf.org/html/rfc3986#section-2
     * @see https://tools.ietf.org/html/rfc3986#section-3.5
     *
     * @return string Fragment de requête de l'URI.
     */
    public function getFragment()
    {
        return $this->fragment;
    }

    /**
     * Renvoie une instance avec le schéma spécifié,
     * doit soutenir les schémas "http" et "https".
     *
     * @param string $scheme Schéma à utiliser avec la nouvelle instance.
     *
     * @throws \InvalidArgumentException pour les schémas invalides ou non pris en charge.
     * @return static                    Nouvelle instance avec le schéma spécifié.
     */
    public function withScheme($scheme)
    {
        $clone         = clone $this;
        $clone->scheme = $this->filterScheme($scheme);

        return $clone;
    }

    /**
     * Renvoyer une instance avec les informations utilisateur spécifiées.
     *
     * @param string      $user     Nom d'utilisateur à utiliser pour l'autorité.
     * @param null|string $password Mot de passe associé à $user.
     *
     * @return static Nouvelle instance avec les informations utilisateur spécifiées.
     */
    public function withUserInfo($user, $password = null)
    {
        $clone       = clone $this;
        $clone->user = $this->filterString($user);
        $clone->pass = $password !== null
            ? $this->filterString($password)
            : '';

        return $clone;
    }

    /**
     * Renvoie une instance avec l'hôte spécifié.
     * Une valeur d'hôte vide équivaut à supprimer l'hôte.
     *
     * @param string $host Nom d'hôte à utiliser avec la nouvelle instance.
     *
     * @throws \InvalidArgumentException Noms d'hôtes non valides.
     * @return static                    Nouvelle instance avec l'hôte spécifié.
     */
    public function withHost($host)
    {
        $clone       = clone $this;
        $clone->host = $this->filterStringToLower($host);

        return $clone;
    }

    /**
     * Renvoie une instance avec le port spécifié.
     * Les implémentations DOIVENT soulever une exception pour les ports en dehors de la
     * Les gammes de ports TCP et UDP établies.
     *
     * @param null|int $port Port à utiliser avec la nouvelle instance; Une valeur nulle
     *                       Supprime les informations du port.
     *
     * @throws \InvalidArgumentException Ports non valides.
     * @return static                    Nouvelle instance avec le port spécifié.
     */
    public function withPort($port)
    {
        $clone       = clone $this;
        $clone->port = $this->filterPort($port);

        return $clone;
    }

    /**
     * Renvoie une instance avec le chemin spécifié.
     *
     * @param string $path Chemin d'accès à utiliser avec la nouvelle instance.
     *
     * @throws \InvalidArgumentException Chemins d'accès non valides.
     * @return static                    Nouvelle instance avec le chemin spécifié.
     */
    public function withPath($path)
    {
        $clone       = clone $this;
        $clone->path = $this->filterPath($path);

        return $clone;
    }

    /**
     * Renvoie une instance avec la chaîne de requête spécifiée.
     *
     * @param string $query Chaîne de requête à utiliser avec la nouvelle instance.
     *
     * @throws \InvalidArgumentException Chaînes de requêtes non valides.
     * @return static                    Nouvelle instance avec la chaîne de requête spécifiée.
     */
    public function withQuery($query)
    {
        $clone        = clone $this;
        $clone->query = $this->filterQuery($query);

        return $clone;
    }

    /**
     * Renvoie une instance avec le fragment URI spécifié.
     *
     * @param string $fragment Le fragment à utiliser avec la nouvelle instance.
     *
     * @return static Une nouvelle instance avec le fragment spécifié.
     */
    public function withFragment($fragment)
    {
        $clone           = clone $this;
        $clone->fragment = $this->filterFragment($fragment);

        return $clone;
    }

    /**
     * Retourne si le port est dans la gamme des ports TCP / UDP.
     *
     * Cette méthode ne fait pas partie de la norme PSR-7
     *
     * @param int $port Port à tester.
     *
     * @return bool
     */
    public static function validePort($port)
    {
        return is_numeric($port) && ($port > 0 && $port <= 65535);
    }

    /**
     * Créer une URI.
     *
     * Cette méthode ne fait pas partie de la norme PSR-7
     *
     * @param string $uri
     *
     * @throws \InvalidArgumentException
     * @return static                    Nouvelle instance d'URI.
     */
    public static function create($uri)
    {
        if (($parse = parse_url($uri)) === false) {
            throw new \InvalidArgumentException('Unable to parse URI');
        }

        return new Uri(
            isset($parse[ 'scheme' ])
                ? $parse[ 'scheme' ]
                : '',
            isset($parse[ 'host' ])
                ? $parse[ 'host' ]
                : '',
            isset($parse[ 'path' ])
                ? $parse[ 'path' ]
                : '',
            isset($parse[ 'port' ])
                ? $parse[ 'port' ]
                : '',
            isset($parse[ 'query' ])
                ? $parse[ 'query' ]
                : '',
            isset($parse[ 'fragment' ])
                ? $parse[ 'fragment' ]
                : '',
            isset($parse[ 'user' ])
                ? $parse[ 'user' ]
                : '',
            isset($parse[ 'pass' ])
                ? $parse[ 'pass' ]
                : ''
        );
    }

    /**
     * Filtre un schéma.
     *
     * @param string|null $sch Schéma à filtrer.
     *
     * @throws \InvalidArgumentException Le schéma n'est pas pris en compte.
     * @return string                    Schéma normalisé.
     */
    protected function filterScheme($sch = '')
    {
        if (empty($sch)) {
            return '';
        }

        $str = $this->filterStringToLower($sch);

        $schStr = trim($str, ':');

        if (!isset($this->ports[ $schStr ])) {
            throw new \InvalidArgumentException('The schema is not supported (only ' . implode('|', array_keys($this->ports)) . ').');
        }

        return $schStr;
    }

    /**
     * Filtre un port.
     *
     * @param string|int|null $port Port à filtrer.
     *
     * @throws \InvalidArgumentException Le port n'est pas dans la gamme des ports TCP/UDP.
     * @return int|null                  Port normalisé.
     */
    protected function filterPort($port)
    {
        if (empty($port)) {
            return null;
        }

        if (!self::validePort($port)) {
            throw new \InvalidArgumentException('The port is not in the TCP/UDP port.');
        }

        return !$this->validPortStandard($port)
            ? (int) $port
            : null;
    }

    /**
     * Filtre une requête.
     *
     * @param string|null $query Requête à filtrer.
     *
     * @return string Requête normalisée.
     */
    protected function filterQuery($query)
    {
        $queryStr = $this->filterString($query);

        return $this->rawurldecodeValue(ltrim($queryStr, '?'));
    }

    /**
     * Filtre une ancre.
     *
     * @param string|null $fragment Ancre à filtrer.
     *
     * @return string Ancre normalisée.
     */
    protected function filterFragment($fragment)
    {
        $fragmentStr = $this->filterString($fragment);

        return $this->rawurldecodeValue(ltrim($fragmentStr, '#'));
    }

    /**
     * Filtre un chemin.
     *
     * @param string|null $path Chemin à filtrer.
     *
     * @return string Chemin normalisé.
     */
    protected function filterPath($path)
    {
        $pathStr = $this->filterString($path);

        return $this->rawurldecodeValue($pathStr);
    }

    /**
     * Filtre une chaine de caractère.
     *
     * @param string|object $value Chaine de caractère à filtrer.
     *
     * @return string Chaine de caractère normalisée.
     */
    protected function filterString($value)
    {
        if ($value === null) {
            return '';
        }
        if (!is_string($value) && !method_exists($value, '__toString')) {
            throw new \InvalidArgumentException('The value must be a string.');
        }

        return (string) $value;
    }

    /**
     * Filtre une chaine de caractère et la renvoie en minuscule.
     *
     * @param string $value Chaine de caractère à filtrer.
     *
     * @return string Chaine de caractère filtré.
     */
    protected function filterStringToLower($value)
    {
        return strtolower($this->filterString($value));
    }

    /**
     * Si le port est prise en charge.
     *
     * @param int $port
     *
     * @return bool
     */
    protected function validPortStandard($port)
    {
        return in_array($port, $this->ports) &&
            $this->scheme === array_keys($this->ports, $port)[ 0 ];
    }

    /**
     * Analyse une requête HTTP et génère une chaîne de requête en encodage URL.
     *
     * @param string $query Requête HTTP.
     *
     * @return string Chaine de requête en encodage URL.
     */
    protected function rawurldecodeValue($query)
    {
        return preg_replace_callback(
            '/(?:[^' . self::CHAR_UNRESERVED . self::CHAR_SUB_DELIMS . '%:@\/]++|%(?![A-Fa-f0-9]{2}))/',
            function ($match) {
                return rawurlencode($match[ 0 ]);
            },
            $query
        );
    }
}
