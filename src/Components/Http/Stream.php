<?php

/**
 * Soosyze Framework http://soosyze.com
 *
 * @package Soosyze\Components\Http
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Http;

use Psr\Http\Message\StreamInterface;

/**
 * Décrit un flux de données.
 *
 * @link https://www.php-fig.org/psr/psr-7/ PSR-7: HTTP message interfaces
 *
 * @author Mathieu NOËL
 */
class Stream implements StreamInterface
{

    /**
     * Modes d'écriture et de lecture d'une ressource.
     *
     * @var array
     * @see http://php.net/manual/function.fopen.php
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
     * @var string
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
     * @param $mixed bool|float|int|object|ressource|string|null
     *
     * @throws \InvalidArgumentException Le type de données n'est pas pris en charge par flux de données.
     */
    public function __construct($mixed = '')
    {
        if (is_scalar($mixed) || $mixed === null) {
            $this->createStreamFromScalar($mixed);
        } elseif (is_resource($mixed)) {
            $this->stream = $mixed;
        } elseif (is_object($mixed) && method_exists($mixed, '__toString')) {
            $this->createStreamFromScalar((string) $mixed);
        } else {
            throw new \InvalidArgumentException('Stream must be a resource');
        }

        $this->meta = stream_get_meta_data($this->stream);
    }

    /**
     * Lit toutes les données du flux dans une chaîne.
     *
     * @see http://php.net/manual/fr/language.oop5.magic.php#object.tostring
     *
     * @return string
     */
    public function __toString()
    {
        if (!$this->isAttached()) {
            return '';
        }

        $this->seek(0);

        return ( string ) stream_get_contents($this->stream);
    }
    
    /**
     * Créer un flux à partir d'un fichier.
     *
     * @param type $filename Nom du fichier.
     * @param type $mode Mode de lecture du fichier.
     *
     * @return \Soosyze\Components\Http\Stream
     *
     * @throws \InvalidArgumentException Le mode de lecture n'est pas valide.
     * @throws \RuntimeException Le fichier ne peut pas être ouvert.
     */
    public static function createStreamFromFile($filename, $mode = 'r')
    {
        if (!in_array($mode, self::$modes['read'])) {
            throw new \InvalidArgumentException('The mode is invalid.');
        }

        try {
            $handle = fopen($filename, $mode);
        } catch (\Exception $ex) {
            throw new \RuntimeException('The file cannot be opened.');
        }

        return new Stream($handle);
    }

    /**
     * Ferme le flux de données et toutes les autres ressources.
     */
    public function close()
    {
        if ($this->isAttached()) {
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
    public function getSize()
    {
        if (!$this->isAttached()) {
            return null;
        }
        $stats = fstat($this->stream);

        return isset($stats[ 'size' ])
            ? $stats[ 'size' ]
            : null;
    }

    /**
     * Renvoie la position actuelle du pointeur de lecture/écriture du fichier.
     *
     * @return int Position du pointeur de fichier
     *
     * @throws \RuntimeException Une erreur est survenue.
     */
    public function tell()
    {
        $this->valideAttach();
        if (($stream = ftell($this->stream)) === false) {
            throw new \RuntimeException('An error has occurred.');
        }

        return $stream;
    }

    /**
     * Renvoie true si le flux se trouve à la fin du flux.
     *
     * @return bool
     *
     * @throws \RuntimeException Une erreur est survenue.
     */
    public function eof()
    {
        $this->valideAttach();

        return feof($this->stream);
    }

    /**
     * Renvoie si la position du flux peut-être modifié.
     *
     * @return bool|array
     */
    public function isSeekable()
    {
        $seekable = $this->getMetadata('seekable');

        return $seekable !== null
            ? $seekable
            : false;
    }

    /**
     * Rechercher une position dans le flux.
     *
     * @link http://www.php.net/manual/fr/function.fseek.php
     *
     * @param int $offset Décalage de flux.
     * @param int $whence Spécifie comment la position du curseur sera calculée
     * basé sur le décalage de recherche.
     *
     * @throws \RuntimeException Une erreur est survenue.
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        $this->valideAttach()->valideSeekable();
        if (fseek($this->stream, $offset, $whence) === -1) {
            throw new \RuntimeException('An error has occurred.');
        }
    }

    /**
     * Replace le pointeur au début du flux.
     *
     * @throws \RuntimeException Une erreur est survenue.
     */
    public function rewind()
    {
        $this->valideAttach();
        if (!rewind($this->stream)) {
            throw new \RuntimeException('An error has occurred.');
        }
    }

    /**
     * Renvoie si le flux est inscriptible ou non.
     *
     * @return bool
     */
    public function isWritable()
    {
        $meta = $this->getMetadata('mode');

        return in_array($meta, self::$modes[ 'write' ]);
    }

    /**
     * Écrire des données dans le flux.
     *
     * @param string $string La chaîne à écrire.
     *
     * @return int Renvoie le nombre d'octets écrits dans le flux.
     *
     * @throws \RuntimeException Une erreur est survenue.
     */
    public function write($string)
    {
        $this->valideAttach()->valideWrite();
        if (($stream = fwrite($this->stream, $string)) === false) {
            throw new \RuntimeException('An error has occurred.');
        }

        return $stream;
    }

    /**
     * Retourne si le flux est lisible ou non.
     *
     * @return bool
     */
    public function isReadable()
    {
        $meta = $this->getMetadata('mode');

        return in_array($meta, self::$modes[ 'read' ]);
    }

    /**
     * Lit les données du flux jusqu'a la longueur d'octet renseignié.
     * Si le flux est inférieur à la longueur donnée il renverra moins d'octet.
     *
     * @param int $length Longueur d'octet.
     *
     * @return string Renvoie les données lues dans le flux
     * ou une chaîne vide si aucun octet n'est disponible.
     *
     * @throws \RuntimeException La valeur d'octet doit être un nombre entier positif.
     * @throws \RuntimeException Une erreur est survenue.
     */
    public function read($length)
    {
        $this->valideAttach()->valideRead();
        if (!is_numeric($length) || $length < 0) {
            throw new \RuntimeException('Byte value must be positive integer.');
        } elseif ($length === 0) {
            return '';
        } elseif (($stream = fread($this->stream, $length)) === false) {
            throw new \RuntimeException('An error has occurred.');
        }

        return $stream;
    }

    /**
     * Renvoie le contenu restant.
     *
     * @return string
     *
     * @throws \RuntimeException Une erreur c'est produit pendant la lecture du flux.
     */
    public function getContents()
    {
        $this->valideAttach()->valideRead();
        if (($stream = stream_get_contents($this->stream)) === false) {
            throw new \RuntimeException('An error occurred while reading the stream.');
        }

        return $stream;
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
     * renvoie une valeur de clé spécifique si une clé est fournie et trouvé,
     * ou null si la clé n'est pas trouvée.
     */
    public function getMetadata($key = null)
    {
        if ($key === null) {
            return $this->meta;
        }

        return isset($this->meta[ $key ])
            ? $this->meta[ $key ]
            : null;
    }

    /**
     * Si le flux de données est attaché.
     *
     * @return bool
     */
    protected function isAttached()
    {
        return is_resource($this->stream);
    }

    /**
     * Charge un flux à partir d'une valeur scalaire.
     * 
     * @param mixed $scalar Valeur scalaire.
     */
    private function createStreamFromScalar($scalar)
    {
        $stream = fopen('php://temp', 'r+');

        if ($scalar !== '') {
            fwrite($stream, $scalar);
            fseek($stream, 0);
        }

        $this->stream = $stream;
    }

    /**
     * Déclenche une exception si le flux de données est détaché.
     *
     * @return $this
     *
     * @throws \RuntimeException Le flux est détaché.
     */
    private function valideAttach()
    {
        if (!$this->isAttached()) {
            throw new \RuntimeException('Stream is detached.');
        }

        return $this;
    }

    /**
     * Déclenche une exception si la position du flux ne peut-être modifié.
     *
     * @return $this
     *
     * @throws \RuntimeException La position du flux ne peut-être modifié.
     */
    private function valideSeekable()
    {
        if (!$this->isSeekable()) {
            throw new \RuntimeException('Stream is not seekable.');
        }

        return $this;
    }

    /**
     * Déclenche une exception si le flux ne peut-être lisible.
     *
     * @return $this
     *
     * @throws \RuntimeException Impossible de lire à partir d'un flux non lisible.
     */
    private function valideRead()
    {
        if (!$this->isReadable()) {
            throw new \RuntimeException('Cannot read from non-readable stream.');
        }

        return $this;
    }

    /**
     * Déclenche une exception si le flux est non accessible en écriture.
     *
     * @return $this
     *
     * @throws \RuntimeException Impossible d'écrire dans un flux non accessible en écriture.
     */
    private function valideWrite()
    {
        if (!$this->isWritable()) {
            throw new \RuntimeException('Cannot write to a non-writable stream.');
        }

        return $this;
    }
}
