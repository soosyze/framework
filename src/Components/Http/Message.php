<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
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
 * @author Mathieu NOËL <mathieu@soosyze.com>
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
     * @var StreamInterface
     */
    protected $body;

    /**
     * Les entêtes.
     *
     * @var string[][]
     */
    protected $headers = [];

    /**
     * Les noms des entêtes.
     *
     * @var string[]
     */
    protected $name = [];

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
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * Renvoie une instance avec la version du protocole HTTP.
     *
     * @param string $version Version du protocole HTTP.
     *
     * @return static
     */
    public function withProtocolVersion($version): MessageInterface
    {
        $clone                  = clone $this;
        $clone->protocolVersion = $this->filterProtocolVersion($version);

        return $clone;
    }

    /**
     * Renvoie le tableau d'en-tête.
     *
     * @return string[][]
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Vérifie si un en-tête existe par le nom (insensible à la casse).
     *
     * @param string $name Nom du champ d'en-tête insensible à la casse.
     *
     * @return bool Renvoie true si l'en-tête est trouvé sinon faux.
     */
    public function hasHeader($name): bool
    {
        return isset($this->name[ strtolower($name) ]);
    }

    /**
     * Vérifie si un en-tête existe par le nom (insensible à la casse).
     *
     * @param string $name Nom du champ d'en-tête insensible à la casse.
     *
     * @return string[] Si l'en-tête est trouvé alors il est renvoyé
     *                  toutes ses valeurs, sinon un tableau vide.
     */
    public function getHeader($name): array
    {
        return $this->hasHeader($name)
            ? $this->headers[ $this->name[ strtolower($name) ] ]
            : [];
    }

    /**
     * Récupère une chaîne de valeurs séparées par des virgules pour un seul en-tête.
     *
     * @param string $name Nom du champ d'en-tête insensible à la casse.
     *
     * @return string Si l'en-tête est trouvé alors il est renvoyé
     *                toutes les valeurs de l'en-tête concaténés par une virgule, sinon une chaine vide.
     */
    public function getHeaderLine($name): string
    {
        return $this->hasHeader($name)
            ? implode(',', $this->getHeader($name))
            : '';
    }

    /**
     * Renvoyer une instance avec la valeur fournie en remplaçant l'en-tête spécifié.
     *
     * @param string          $name  Nom du champ d'en-tête insensible à la casse.
     * @param string|string[] $value Valeur(s) de l'en-tête.
     *
     * @return static
     */
    public function withHeader($name, $value): MessageInterface
    {
        $clone  = clone $this;
        $values = $clone->validateAndTrimHeader($name, $value);

        $normalized = strtolower($name);
        if (isset($clone->name[ $normalized ])) {
            unset($clone->headers[ $clone->name[ $normalized ] ]);
        }
        $clone->name[ $normalized ] = $name;
        $clone->headers[ $name ]    = $values;

        return $clone;
    }

    /**
     * Renvoyer une instance avec la valeur fournie en ajoutant l'en-tête spécifié.
     *
     * @param string          $name  Nom du champ d'en-tête insensible à la casse.
     * @param string|string[] $value Valeur(s) de l'en-tête.
     *
     * @return static
     */
    public function withAddedHeader($name, $value): MessageInterface
    {
        $clone  = clone $this;
        $values = $this->validateAndTrimHeader($name, $value);

        $normalized = strtolower($name);
        if (!isset($clone->name[ $normalized ])) {
            $clone->name[ $normalized ] = $name;
        }

        $header = $clone->name[ $normalized ];
        /* Pour ne pas écraser les valeurs avec le array merge utilise une boucle simple. */
        foreach ($values as $head) {
            $clone->headers[ $header ][] = $head;
        }

        return $clone;
    }

    /**
     * Renvoie une instance sans l'en-tête spécifié.
     *
     * @param string $name Nom de champ d'en-tête insensible à la casse à supprimer.
     *
     * @return static
     */
    public function withoutHeader($name): MessageInterface
    {
        $normalized = strtolower($name);

        if (!isset($this->name[$normalized])) {
            return $this;
        }

        $header = $this->name[$normalized];

        $clone = clone $this;
        unset($clone->headers[$header], $clone->name[$normalized]);

        return $clone;
    }

    /**
     * Retourne le corp du message.
     *
     * @return StreamInterface Renvoie le corps en tant que flux.
     */
    public function getBody(): StreamInterface
    {
        return $this->body;
    }

    /**
     * Renvoie une instance avec le corps du message spécifié.
     *
     * @param StreamInterface $body Le corp.
     *
     * @return static
     */
    public function withBody(StreamInterface $body): MessageInterface
    {
        $clone       = clone $this;
        $clone->body = $body;

        return $clone;
    }

    /**
     * Filtre la version du protocole.
     *
     * @param mixed $version
     *
     * @throws \InvalidArgumentException Le protocole spécifié n'est pas valide.
     *
     * @return string Le protocole si celui-ci est conforme.
     */
    protected function filterProtocolVersion($version): string
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
     *
     * @return void
     */
    protected function withHeaders(array $headers): void
    {
        $this->headers = [];
        foreach ($headers as $name => $value) {
            $values = $this->validateAndTrimHeader($name, $value);

            $normalized = strtolower($name);
            if (isset($this->name[ $normalized ])) {
                unset($this->headers[ $this->name[ $normalized ] ]);
            }
            $this->name[ $normalized ] = $name;
            $this->headers[ $name ]    = $values;
        }
    }

    /**
     * Assurez-vous que l'en-tête est conforme à la norme RFC 7230.
     *
     * Les noms d'en-tête doivent être une chaîne non vide composée de caractères de jeton.
     *
     * Les valeurs d'en-tête doivent être des chaînes composées de caractères visibles, tous optionnels.
     * Les espaces blancs de début et de fin sont supprimés. Cette méthode va toujours dépouiller ces
     * espaces optionnels. Notez que la méthode ne permet pas de replier les espaces au sein de
     * Les valeurs étant obsolètes dans presque toutes les instances par la RFC.
     *
     * header-field = field-name ":" OWS field-value OWS
     * field-name   = 1*( "!" / "#" / "$" / "%" / "&" / "'" / "*" / "+" / "-" / "." / "^"
     *              / "_" / "`" / "|" / "~" / %x30-39 / ( %x41-5A / %x61-7A ) )
     * OWS          = *( SP / HTAB )
     * field-value  = *( ( %x21-7E / %x80-FF ) [ 1*( SP / HTAB ) ( %x21-7E / %x80-FF ) ] )
     *
     * @see https://tools.ietf.org/html/rfc7230#section-3.2.4
     *
     * @param mixed $header
     * @param mixed $values
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    private function validateAndTrimHeader($header, $values): array
    {
        if (!\is_string($header) || \preg_match('@^[!#$%&\'*+.^_`|~0-9A-Za-z-]+$@', $header) !== 1) {
            throw new \InvalidArgumentException('Header name must be an RFC 7230 compatible string.');
        }
        if (!\is_array($values)) {
            // This is simple, just one value.
            if ((!\is_numeric($values) && !\is_string($values)) || \preg_match("@^[ \t\x21-\x7E\x80-\xFF]*$@", (string) $values) !== 1) {
                throw new \InvalidArgumentException('Header values must be RFC 7230 compatible strings.');
            }

            return [ \trim((string) $values, " \t") ];
        }
        if (empty($values)) {
            throw new \InvalidArgumentException('Header values must be a string or an array of strings, empty array given.');
        }
        // Assert Non empty array
        $returnValues = [];
        foreach ($values as $v) {
            if ((!\is_numeric($v) && !\is_string($v)) || \preg_match("@^[ \t\x21-\x7E\x80-\xFF]*$@", (string) $v) !== 1) {
                throw new \InvalidArgumentException('Header values must be RFC 7230 compatible strings.');
            }
            $returnValues[] = \trim((string) $v, " \t");
        }

        return $returnValues;
    }
}
