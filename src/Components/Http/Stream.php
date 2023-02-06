<?php

//declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Http;

use Psr\Http\Message\StreamInterface;

/**
 * Décrit un flux de données.
 *
 * @link https://www.php-fig.org/psr/psr-7/ PSR-7: HTTP message interfaces
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class Stream implements StreamInterface
{
    /**
     * Modes d'écriture et de lecture d'une ressource.
     *
     * @see http://php.net/manual/function.fopen.php
     *
     * @var array
     */
    protected static $modes = [
        'read'  => [
            'a+',
            'c+', 'c+b', 'c+t',
            'r', 'rb', 'rt',
            'r+', 'r+b', 'r+t',
            'w+', 'w+b', 'w+t',
            'x+', 'x+b', 'x+t'
        ],
        'write' => [
            'a', 'a+',
            'c+', 'c+b', 'c+t',
            'rw',
            'r+', 'r+b', 'r+t',
            'w', 'wb',
            'w+', 'w+b', 'w+t',
            'x+', 'x+b', 'x+t'
        ]
    ];

    /**
     * Flux de données.
     *
     * @var null|resource
     */
    private $stream;

    /**
     * Meta données du flux.
     *
     * @var array
     */
    private $meta = [];

    /**
     * Créer un flux de données à partir de données scalaire ou d'une ressource.
     *
     * @see http://php.net/manual/fr/wrappers.php.php
     *
     * @param mixed $mixed
     *
     * @throws \InvalidArgumentException Le type de données n'est pas pris en charge par flux de données.
     */
    public function __construct($mixed = '')
    {
        $this->stream = $this->createStream($mixed);

        $this->meta = stream_get_meta_data($this->stream);
    }

    /**
     * Lit toutes les données du flux dans une chaîne.
     *
     * @see http://php.net/manual/fr/language.oop5.magic.php#object.tostring
     */
    public function __toString(): string
    {
        if ($this->stream === null) {
            return '';
        }

        $this->seek(0);

        /** @phpstan-ignore-next-line */
        return (string) stream_get_contents($this->stream);
    }

    /**
     * Créer un flux à partir d'un fichier.
     *
     * @param string $filename Nom du fichier.
     * @param string $mode     Mode de lecture du fichier.
     *
     * @throws \InvalidArgumentException Le mode de lecture n'est pas valide.
     * @throws \RuntimeException         Le fichier ne peut pas être ouvert.
     *
     * @return Stream
     */
    public static function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        if (!in_array($mode, self::$modes[ 'read' ])) {
            throw new \InvalidArgumentException('The mode is invalid.');
        }
        $handle = Utils::tryFopen($filename, $mode);

        return new Stream($handle);
    }

    /**
     * Ferme le flux de données et toutes les autres ressources.
     */
    public function close(): void
    {
        if ($this->stream !== null) {
            fclose($this->stream);
            $this->detach();
        }
    }

    /**
     * Sépare les ressources sous-jacentes du flux.
     *
     * Après que le flux a été détaché, le flux est dans un état inutilisable.
     *
     * @return resource|null Flux PHP sous-jacent, le cas échéant
     */
    public function detach()
    {
        $output       = $this->stream;
        $this->stream = null;
        $this->meta   = [];

        return $output;
    }

    /**
     * Retourne la taille du flux.
     *
     * @return int|null Renvoie la taille en octets si elle est connue, ou null si elle est inconne.
     */
    public function getSize(): ?int
    {
        if ($this->stream === null) {
            return null;
        }
        $stats = fstat($this->stream);

        return $stats === false
            ? null
            :  $stats[ 'size' ];
    }

    /**
     * Renvoie la position actuelle du pointeur de lecture/écriture du fichier.
     *
     * @throws \RuntimeException Une erreur est survenue.
     *
     * @return int Position du pointeur de fichier
     */
    public function tell(): int
    {
        if ($this->stream === null) {
            throw new \RuntimeException('Stream is detached.');
        }
        if (($handle = ftell($this->stream)) === false) {
            throw new \RuntimeException('An error has occurred.');
        }

        return $handle;
    }

    /**
     * Renvoie true si le flux se trouve à la fin du flux.
     */
    public function eof(): bool
    {
        if ($this->stream === null) {
            throw new \RuntimeException('Stream is detached.');
        }

        return feof($this->stream);
    }

    /**
     * Renvoie si la position du flux peut-être modifié.
     */
    public function isSeekable(): bool
    {
        $seekable = $this->getMetadata('seekable');

        return $seekable !== null && $seekable !== false;
    }

    /**
     * Rechercher une position dans le flux.
     *
     * @link http://www.php.net/manual/fr/function.fseek.php
     *
     * @param int $offset Décalage de flux.
     * @param int $whence Spécifie comment la position du curseur sera calculée
     *                    basé sur le décalage de recherche.
     *
     * @throws \RuntimeException Une erreur est survenue.
     */
    public function seek($offset, $whence = SEEK_SET): void
    {
        if ($this->stream === null) {
            throw new \RuntimeException('Stream is detached.');
        }
        $this->valideSeekable();
        if (fseek($this->stream, $offset, $whence) === -1) {
            throw new \RuntimeException('An error has occurred.');
        }
    }

    /**
     * Replace le pointeur au début du flux.
     *
     * @throws \RuntimeException Une erreur est survenue.
     */
    public function rewind(): void
    {
        if ($this->stream === null) {
            throw new \RuntimeException('Stream is detached.');
        }
        $this->valideSeekable();
        if (!rewind($this->stream)) {
            throw new \RuntimeException('An error has occurred.');
        }
    }

    /**
     * Renvoie si le flux est inscriptible ou non.
     */
    public function isWritable(): bool
    {
        return in_array($this->getMetadata('mode'), self::$modes[ 'write' ]);
    }

    /**
     * Écrire des données dans le flux.
     *
     * @param string $string La chaîne à écrire.
     *
     * @throws \RuntimeException Une erreur est survenue.
     *
     * @return int Renvoie le nombre d'octets écrits dans le flux.
     */
    public function write($string): int
    {
        if ($this->stream === null) {
            throw new \RuntimeException('Stream is detached.');
        }
        $this->valideWrite();
        if (($handle = fwrite($this->stream, $string)) === false) {
            throw new \RuntimeException('An error has occurred.');
        }

        return $handle;
    }

    /**
     * Retourne si le flux est lisible ou non.
     */
    public function isReadable(): bool
    {
        return in_array($this->getMetadata('mode'), self::$modes[ 'read' ]);
    }

    /**
     * Lit les données du flux jusqu'a la longueur d'octet renseignié.
     * Si le flux est inférieur à la longueur donnée il renverra moins d'octet.
     *
     * @param int $length Longueur d'octet.
     *
     * @throws \RuntimeException La valeur d'octet doit être un nombre entier positif.
     * @throws \RuntimeException Une erreur est survenue.
     *
     * @return string Renvoie les données lues dans le flux ou une chaîne vide si aucun octet n'est disponible.
     */
    public function read($length): string
    {
        if ($this->stream === null) {
            throw new \RuntimeException('Stream is detached.');
        }
        $this->valideRead();
        if (!is_numeric($length) || $length < 0) {
            throw new \RuntimeException('Byte value must be positive integer.');
        }
        if ($length === 0) {
            return '';
        }
        if (($handle = fread($this->stream, $length)) === false) {
            throw new \RuntimeException('An error has occurred.');
        }

        return $handle;
    }

    /**
     * Renvoie le contenu restant.
     *
     * @throws \RuntimeException Une erreur c'est produit pendant la lecture du flux.
     */
    public function getContents(): string
    {
        if ($this->stream === null) {
            throw new \RuntimeException('Stream is detached.');
        }
        $this->valideRead();
        if (($handle = stream_get_contents($this->stream)) === false) {
            throw new \RuntimeException('An error occurred while reading the stream.');
        }

        return $handle;
    }

    /**
     * Obtenir des métadonnées de flux en tant que tableau associatif ou récupérer une clé spécifique.
     * Les clés retournées sont identiques aux clés retournées par PHP.
     *
     * @link http://php.net/manual/fr/function.stream-get-meta-data.php
     *
     * @param string $key Métadonnées spécifiques à récupérer.
     *
     * @return array|mixed|null Renvoie un tableau associatif si aucune clé n'est renségné.
     *                          renvoie une valeur de clé spécifique si une clé est fournie et trouvé,
     *                          ou null si la clé n'est pas trouvée.
     */
    public function getMetadata($key = null)
    {
        if ($key === null) {
            return $this->meta;
        }

        return $this->meta[ $key ] ?? null;
    }

    /**
     * Charge un flux à partir d'une valeur.
     *
     * @param mixed $mixed
     *
     * @throws \InvalidArgumentException
     * @return resource
     */
    private function createStream($mixed)
    {
        if (is_scalar($mixed) || $mixed === null) {
            return $this->createStreamFromScalar($mixed);
        }
        if (is_resource($mixed)) {
            return $mixed;
        }
        if (is_object($mixed) && method_exists($mixed, '__toString')) {
            return $this->createStreamFromScalar((string) $mixed);
        }

        throw new \InvalidArgumentException('Stream must be a resource.');
    }

    /**
     * Charge un flux à partir d'une valeur scalaire.
     *
     * @param null|scalar $scalar Valeur scalaire.
     *
     * @return resource
     */
    private function createStreamFromScalar($scalar)
    {
        $handle = Utils::tryFopen('php://temp', 'r+');

        if ($scalar !== '') {
            fwrite($handle, (string) $scalar);
            fseek($handle, 0);
        }

        return $handle;
    }

    /**
     * Déclenche une exception si la position du flux ne peut-être modifié.
     *
     * @throws \RuntimeException La position du flux ne peut-être modifié.
     */
    private function valideSeekable(): self
    {
        if (!$this->isSeekable()) {
            throw new \RuntimeException('Stream is not seekable.');
        }

        return $this;
    }

    /**
     * Déclenche une exception si le flux ne peut-être lisible.
     *
     * @throws \RuntimeException Impossible de lire à partir d'un flux non lisible.
     */
    private function valideRead(): self
    {
        if (!$this->isReadable()) {
            throw new \RuntimeException('Cannot read from non-readable stream.');
        }

        return $this;
    }

    /**
     * Déclenche une exception si le flux est non accessible en écriture.
     *
     * @throws \RuntimeException Impossible d'écrire dans un flux non accessible en écriture.
     */
    private function valideWrite(): self
    {
        if (!$this->isWritable()) {
            throw new \RuntimeException('Cannot write to a non-writable stream.');
        }

        return $this;
    }
}
