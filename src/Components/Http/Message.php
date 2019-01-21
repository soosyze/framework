<?php

/**
 * Soosyze Framework http://soosyze.com
 *
 * @package Soosyze\Components\Http
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Http;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Les messages HTTP sont constitués de requêtes d'un client vers un serveur et des réponses
 * d'un serveur à un client. Cette interface définit les méthodes communes à chaque.
 *
 * @link http://www.ietf.org/rfc/rfc7230.txt
 * @link http://www.ietf.org/rfc/rfc7231.txt
 * @link https://www.php-fig.org/psr/psr-7/ PSR-7: HTTP message interfaces
 *
 * @author Mathieu NOËL
 */
class Message implements MessageInterface
{
    /**
     * Version du protocole (1.0|1.1|2.0|2).
     *
     * @var string
     */
    protected $protocolVersion = '1.0';

    /**
     * Corp du message.
     *
     * @var \Psr\Http\Message\StreamInterface
     */
    protected $body;

    /**
     * Les entêtes.
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Protocoles pris en charges.
     *
     * @var string[]
     */
    protected $protocols = [ '1.0', '1.1', '2.0', '2' ];

    /**
     * Retourne la version du protocole HTTP.
     *
     * @return string
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * Renvoie une instance avec la version du protocole HTTP.
     *
     * @param string $version Version du protocole HTTP.
     *
     * @return $this
     */
    public function withProtocolVersion($version)
    {
        $clone                  = clone $this;
        $clone->protocolVersion = $this->filterProtocolVersion($version);

        return $clone;
    }

    /**
     * Renvoie le tableau d'en-tête.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Vérifie si un en-tête existe par le nom (insensible à la casse).
     *
     * @param $name Nom du champ d'en-tête insensible à la casse.
     *
     * @return bool Renvoie true si l'en-tête est trouvé sinon faux.
     */
    public function hasHeader($name)
    {
        return isset($this->headers[ strtolower($name) ]);
    }

    /**
     * Vérifie si un en-tête existe par le nom (insensible à la casse).
     *
     * @param $name Nom du champ d'en-tête insensible à la casse.
     *
     * @return string[] Si l'en-tête est trouvé alors il est renvoyé
     *                  toutes ses valeurs, sinon un tableau vide.
     */
    public function getHeader($name)
    {
        return $this->hasHeader($name)
            ? $this->headers[ strtolower($name) ]
            : [];
    }

    /**
     * Récupère une chaîne de valeurs séparées par des virgules pour un seul en-tête.
     *
     * @param $name Nom du champ d'en-tête insensible à la casse.
     *
     * @return string Si l'en-tête est trouvé alors il est renvoyé
     *                toutes les valeurs de l'en-tête concaténés par une virgule, sinon une chaine vide.
     */
    public function getHeaderLine($name)
    {
        return $this->hasHeader($name)
            ? implode(',', $this->headers[ strtolower($name) ])
            : '';
    }

    /**
     * Renvoyer une instance avec la valeur fournie en remplaçant l'en-tête spécifié.
     *
     * @param string          $name  Nom du champ d'en-tête insensible à la casse.
     * @param string|string[] $value Valeur(s) de l'en-tête.
     *
     * @return $this
     */
    public function withHeader($name, $value)
    {
        $clone                               = clone $this;
        $clone->headers[ strtolower($name) ] = is_array($value)
            ? $value
            : [ $value ];

        return $clone;
    }

    /**
     * Renvoyer une instance avec la valeur fournie en ajoutant l'en-tête spécifié.
     *
     * @param string          $name  Nom du champ d'en-tête insensible à la casse.
     * @param string|string[] $value Valeur(s) de l'en-tête.
     *
     * @return $this
     */
    public function withAddedHeader($name, $value)
    {
        $clone = clone $this;
        if (!is_array($value)) {
            $value = [ $value ];
        }
        /* Pour ne pas écraser les valeurs avec le array merge utilise une boucle simple. */
        foreach ($value as $head) {
            $clone->headers[ strtolower($name) ][] = $head;
        }

        return $clone;
    }

    /**
     * Renvoie une instance sans l'en-tête spécifié.
     *
     * @param string $name Nom de champ d'en-tête insensible à la casse à supprimer.
     *
     * @return $this
     */
    public function withoutHeader($name)
    {
        $clone = clone $this;
        if ($clone->hasHeader($name)) {
            unset($clone->headers[ strtolower($name) ]);
        }

        return $clone;
    }

    /**
     * Retourne le corp du message.
     *
     * @return StreamInterface Renvoie le corps en tant que flux.
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Renvoie une instance avec le corps du message spécifié.
     *
     * @param string $body Le corp.
     *
     * @return $this
     */
    public function withBody(StreamInterface $body)
    {
        $clone       = clone $this;
        $clone->body = $body;

        return $clone;
    }

    /**
     * Filtre la version du protocole.
     *
     * @param string $version
     *
     * @throws \InvalidArgumentException Le protocole spécifié n'est pas valide.
     * @return string                    Le protocole si celui-ci est conforme.
     */
    protected function filterProtocolVersion($version)
    {
        if (!is_string($version) || !in_array($version, $this->protocols)) {
            throw new \InvalidArgumentException('The specified protocol is invalid.');
        }

        return $version;
    }

    /**
     * Ajoute les en-têtes au message.
     *
     * @param array $headers
     */
    protected function withHeaders(array $headers)
    {
        $this->headers = [];
        foreach ($headers as $key => $value) {
            $this->headers[ strtolower($key) ] = is_array($value)
                ? $value
                : [ $value ];
        }
    }
}
