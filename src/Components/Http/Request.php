<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @package Soosyze\Components\Http
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Http;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * Représentation d'une requête côté client sortant.
 *
 * @link https://www.php-fig.org/psr/psr-7/ PSR-7: HTTP message interfaces
 *
 * @author Mathieu NOËL
 */
class Request extends Message implements RequestInterface
{
    /**
     * Méthode de la requête HTTP.
     *
     * @var string
     */
    protected $method;

    /**
     * Cible de la requête.
     *
     * @var string
     */
    protected $requestTarget;

    /**
     * L'URI de la requête.
     *
     * @var \Psr\Http\Message\UriInterface
     */
    protected $uri;

    /**
     * Méthodes acceptés par le protocole HTTP.
     *
     * @var string
     */
    protected $methods = [
        'CONNECT',
        'DELETE',
        'GET',
        'HEAD',
        'OPTIONS',
        'PATCH',
        'POST',
        'PURGE',
        'PUT',
        'TRACE',
    ];

    /**
     * Pendant la construction, les implémentations DOIVENT essayer de définir l'en-tête Host à partir de
     * un URI fourni si aucun en-tête Host n'est fourni.
     *
     * @param string          $method  Méthode HTTP ('GET'|'POST'|...).
     * @param UriInterface    $uri     L'URI de la requête.
     * @param array           $headers Les en-têtes du message.
     * @param StreamInterface $body    Le corps du message.
     * @param type            $version La version du protocole HTTP.
     */
    public function __construct(
        $method,
        UriInterface $uri,
        array $headers = [],
        StreamInterface $body = null,
        $version = '1.1'
    ) {
        $this->method          = $this->filterMethod($method);
        $this->uri             = $uri;
        $this->withHeaders($headers);
        $this->body            = $body;
        $this->protocolVersion = $this->filterProtocolVersion($version);

        if (!isset($headers[ 'Host' ]) && $uri->getHost() !== '') {
            $this->headers[ 'host' ] = [ $uri->getHost() ];
        }
    }

    /**
     * Récupère la méthode HTTP de la requête.
     *
     * @return string Renvoie la méthode de requête.
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Récupère la cible de requête telque les utilisateurs la voit.
     * Si aucune adresse URI n'est disponible et qu'aucune cible de requête n'a été spécifiée
     * cette méthode DOIT retourner la chaîne "/".
     *
     * @return string Cible ce la requête.
     */
    public function getRequestTarget()
    {
        if ($this->requestTarget !== null) {
            return $this->requestTarget;
        }

        $target = $this->uri->getPath() != ''
            ? $this->uri->getPath()
            : '/';

        return $target .= $this->uri->getQuery() != ''
            ? '?' . $this->uri->getQuery()
            : '';
    }

    /**
     * Récupère l'instance d'URI.
     *
     * Cette méthode DOIT retourner une instance d'UriInterface.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     *
     * @return UriInterface Renvoie une instance d'UriInterface
     *                      représentant l'URI de la requête.
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Renvoie une instance avec la méthode HTTP fournie.
     *
     * @param string $method Nom de la méthode (sensible à la casse).
     *
     * @throws \InvalidArgumentException pour les méthodes HTTP invalides.
     * @return static
     */
    public function withMethod($method)
    {
        $clone         = clone $this;
        $clone->method = $this->filterMethod($method);

        return $clone;
    }

    /**
     * Renvoie une instance avec la cible de requête spécifique.
     *
     * @link http://tools.ietf.org/html/rfc7230#section-5.3 (pour les différents
     * formulaires de demande-cible autorisés dans les messages de demande)
     *
     * @param mixed $requestTarget
     *
     * @return static
     */
    public function withRequestTarget($requestTarget)
    {
        if (!is_string($requestTarget)) {
            throw new \InvalidArgumentException('The target of the request must be a string.');
        }
        $clone                = clone $this;
        $clone->requestTarget = $requestTarget;

        return $clone;
    }

    /**
     * Renvoie une instance avec l'URI fourni.
     *
     * @link http://tools.ietf.org/html/rfc3986#section-4.3
     *
     * @param UriInterface $uri          Nouvelle requête URI à utiliser.
     * @param bool         $preserveHost Préserve l'état d'origine de l'en-tête Host.
     *
     * @return static
     */
    public function withUri(UriInterface $uri, $preserveHost = false)
    {
        $clone      = clone $this;
        $clone->uri = $uri;
        /*
         * Si l'en-tête Host est manquant ou vide, et que le nouvel URI contient
         * un composant hôte, cette méthode DOIT mettre à jour l'en-tête Host dans le retour.
         */
        if (!$preserveHost || !$this->hasHeader('Host') && $uri->getHost() !== '') {
            return $clone->withHeader('Host', $uri->getHost());
        }

        return $clone;
    }

    /**
     * Filtre la méthde HTTP de la requête.
     *
     * @param string $method Méthode HTTP ('GET'|'POST'|...).
     *
     * @throws \InvalidArgumentException La méthode doit être une chaine de caractère.
     * @throws \InvalidArgumentException La méthode n'est pas prise en charge par la requête.
     * @return string                    Méthode HTTP filtré.
     */
    protected function filterMethod($method)
    {
        if (!is_string($method)) {
            throw new \InvalidArgumentException('The method must be a string');
        }

        $methodUp = strtoupper($method);

        if (!in_array($methodUp, $this->methods)) {
            throw new \InvalidArgumentException('The method is not valid (only ' . implode('|', $this->methods) . ').');
        }

        return $method;
    }
}
