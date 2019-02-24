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
use Psr\Http\Message\UploadedFileInterface;

/**
 * Objet de valeur représentant un fichier téléchargé via une requête HTTP.
 *
 * @link https://www.php-fig.org/psr/psr-7/ PSR-7: HTTP message interfaces
 *
 * @author Mathieu NOËL
 */
class UploadedFile implements UploadedFileInterface
{
    /**
     * Nom du fichier ($_FILES['key']['tmp_name']).
     *
     * @var string|null
     */
    protected $file = null;

    /**
     * Chemin du fichier temporaire ($_FILES['key']['name']).
     *
     * @var string|null
     */
    protected $name = null;

    /**
     * Taille du fichier en octets ($_FILES['key']['size']).
     *
     * @var int|null
     */
    protected $size = 0;

    /**
     * Type MIME du fichier ($_FILES['key']['type']).
     *
     * @var string|null
     */
    protected $type = null;

    /**
     * Code erreur ($_FILES['key']['error']).
     *
     * @var int
     */
    protected $error;

    /**
     * Codes d'erreurs approprié dans le tableau de fichier.
     *
     * @see http://php.net/manual/fr/features.file-upload.errors.php
     *
     * @var int[]
     */
    protected $errors = [
        UPLOAD_ERR_OK,
        UPLOAD_ERR_INI_SIZE,
        UPLOAD_ERR_FORM_SIZE,
        UPLOAD_ERR_PARTIAL,
        UPLOAD_ERR_NO_FILE,
        UPLOAD_ERR_NO_TMP_DIR,
        UPLOAD_ERR_CANT_WRITE,
        UPLOAD_ERR_EXTENSION,
    ];

    /**
     * Représentation du fichier en flux de données.
     *
     * @var StreamInterface
     */
    protected $stream;

    /**
     * Si le fichier a été déplacé.
     *
     * @var bool
     */
    protected $moved = false;

    /**
     * Construit un fichier.
     *
     * @param string|ressource|StreamInterface $file
     * @param string|null                      $name
     * @param int|null                         $size
     * @param string|null                      $type
     * @param int                              $error
     */
    public function __construct(
        $file,
        $name = null,
        $size = null,
        $type = null,
        $error = UPLOAD_ERR_OK
    ) {
        $this->name  = $this->filterName($name);
        $this->size  = $this->filterSize($size);
        $this->type  = $this->filterType($type);
        $this->error = $this->filterError($error);
        if (!$this->isError()) {
            $this->filterFile($file);
        }
    }

    /**
     * Créer un fichier à partir d'un tableau de données.
     *
     * @param array $file Doit contenir la clé 'tmp_name' au minimum.
     *
     * @throws \InvalidArgumentException La clé tmp_name est requise.
     * @return UploadedFileInterface
     */
    public static function create(array $file)
    {
        if (!isset($file[ 'tmp_name' ])) {
            throw new \InvalidArgumentException('The tmp_name key is required.');
        }

        return new UploadedFile(
            $file[ 'tmp_name' ],
            isset($file[ 'name' ])
            ? $file[ 'name' ]
            : null,
            isset($file[ 'size' ])
            ? $file[ 'size' ]
            : null,
            isset($file[ 'type' ])
            ? $file[ 'type' ]
            : null,
            isset($file[ 'error' ])
            ? $file[ 'error' ]
            : UPLOAD_ERR_OK
        );
    }

    /**
     * Récupérer un flux représentant le fichier téléchargé.
     *
     * Cette méthode DOIT renvoyer une instance StreamInterface, représentant le
     * fichier téléchargé.
     *
     * @throws \RuntimeException Dans les cas où aucun flux n'est disponible ou peut être
     *                           créé.
     * @return StreamInterface   Stream représentation du fichier téléchargé.
     */
    public function getStream()
    {
        if ($this->isError()) {
            throw new \RuntimeException('A download error prevents recovery of the stream.');
        }

        if ($this->moved) {
            throw new \RuntimeException('The file has already been moved.');
        }

        if (empty($this->stream)) {
            $this->stream = new Stream(fopen($this->file, 'r'));
        }

        return $this->stream;
    }

    /**
     * Déplacez le fichier téléchargé vers un nouvel emplacement.
     *
     * Cette méthode est garantie de travailler dans les environnements SAPI et non-SAPI.
     *
     * @see http://php.net/is_uploaded_file
     * @see http://php.net/move_uploaded_file
     *
     * @param string $targetPath Chemin vers lequel déplacer le fichier téléchargé.
     *
     * @throws \RuntimeException         sur toute erreur lors de l'opération de déplacement, ou sur
     *                                   le deuxième ou suivant appel à la méthode.
     * @throws \InvalidArgumentException Si le $targetPath spécifié n'est pas valide.
     * @throws \InvalidArgumentException Une erreur est survenue.
     */
    public function moveTo($targetPath)
    {
        if ($this->moved) {
            throw new \RuntimeException('The file has already been moved.');
        }
        if (!is_string($targetPath) || $targetPath === '') {
            throw new \InvalidArgumentException('Target is incorrect.');
        }
        if ($this->file) {
            if (!file_exists($this->file) && (file_exists($targetPath) || !is_writable($targetPath))) {
                throw new \InvalidArgumentException('An error has occurred.');
            }
            $this->moved = php_sapi_name() == 'cli'
                ? $this->moveToSapi($targetPath)
                : $this->moveToNoSapi($targetPath);
        } else {
            $stream = fopen($targetPath, 'w');

            fwrite($stream, $this->stream->getContents());
            fclose($stream);

            $this->moved = true;
        }
    }

    /**
     * Récupérer la taille du fichier à partir de la clé "taille" du tableau $_FILES.
     *
     * @return int|null Taille du fichier en octets ou null si inconnu.
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Récupérer l'erreur associée au fichier téléchargé à partir de la clé "error" du tableau $_FILES.
     *
     * La valeur de retour DOIT être l'une des constantes UPLOAD_ERR_XXX de PHP.
     *
     * @see http://php.net/manual/fr/features.file-upload.errors.php
     *
     * @return int Une des constantes UPLOAD_ERR_XXX de PHP.
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * Récupérer le nom de fichier envoyé par le client à partir de la clé "name" du tableau $_FILES.
     *
     * Ne faites pas confiance à la valeur renvoyée par cette méthode. Un client pourrait envoyer
     * un nom de fichier malveillant dans l'intention de corrompre ou de pirater votre
     * application.
     *
     * @return string|null Nom de fichier envoyé par le client ou null si aucun.
     *                     a été fourni.
     */
    public function getClientFilename()
    {
        return $this->name;
    }

    /**
     * Récupérer le type de média envoyé par le client à partir de la clé "type" du tableau $_FILES.
     *
     * Ne faites pas confiance à la valeur renvoyée par cette méthode. Un client pourrait envoyer
     * un type de média malveillant avec l'intention de corrompre ou de pirater votre
     * application.
     *
     * @return string|null Le type de média envoyé par le client ou null si aucun
     *                     a été fourni.
     */
    public function getClientMediaType()
    {
        return $this->type;
    }

    /**
     * Déclenche une exception si le fichier n'est pas valide.
     *
     * @param string|ressource|StreamInterface $file Le fichier.
     *
     * @throws \InvalidArgumentException La ressource de fichier n'est pas lisible.
     */
    protected function filterFile($file)
    {
        if (is_string($file)) {
            $this->file = $file;
        } elseif (is_resource($file)) {
            $this->stream = new Stream($file);
        } elseif ($file instanceof StreamInterface) {
            $this->stream = $file;
        } else {
            throw new \InvalidArgumentException('The file resource is not readable.');
        }
    }

    /**
     * Déclenche une exception si le nom du fichier n'est pas valide.
     *
     * @param string $name Nom du fichier
     *
     * @throws \InvalidArgumentException Le nom du fichier doit être une chaine de caractère ou null.
     * @return string                    Nom du fichier filtré.
     */
    protected function filterName($name)
    {
        if (!is_string($name) && $name !== null) {
            throw new \InvalidArgumentException('The file name must be a string or null.');
        }

        return $name;
    }

    /**
     * Déclenche une exception si la taille du fichier n'est pas valide.
     *
     * @param int $size Taille du fichier.
     *
     * @throws \InvalidArgumentException La taille du fichier doit-être un nombre entier ou null
     * @return int                       Taille du fichier filtré.
     */
    protected function filterSize($size)
    {
        if (!is_int($size) && $size !== null) {
            throw new \InvalidArgumentException('The file size must be a integer or null');
        }

        return $size;
    }

    /**
     * Déclenche une exception si le type du fichier n'est pas valide.
     *
     * @param string $type Type du fichier
     *
     * @throws \InvalidArgumentException Le type du fichier doit être une chaine de caractère ou null.
     * @return string                    Type du fichier filtré.
     */
    protected function filterType($type)
    {
        if (!is_string($type) && $type !== null) {
            throw new \InvalidArgumentException('The file type must be a string or null.');
        }

        return $type;
    }

    /**
     * Déclence une exception si le type d'error n'est pas valide.
     *
     * @param int $error Type d'erreur.
     *
     * @throws \InvalidArgumentException Le type d'erreur n'est pas valide.
     * @return int                       Type d'erreur filtré.
     */
    protected function filterError($error)
    {
        if (!in_array($error, $this->errors, true)) {
            throw new \InvalidArgumentException('The type of error is invalid.');
        }

        return $error;
    }

    /**
     * Déplace le fichier dans un environnement SAPI.
     *
     * @param string $targetPath Cible du fichier.
     *
     * @return bool
     */
    private function moveToSapi($targetPath)
    {
        return rename($this->file, $targetPath);
    }

    /**
     * Déplace le fichier dans un environnement non SAPI.
     *
     * @param type $targetPath Cible du fichier.
     *
     * @throws \RuntimeException Le fichier n'a pas été téléchargé par HTTP POST.
     * @throws \RuntimeException Une erreur est survenue dans le déplacement du fichier.
     * @return bool
     */
    private function moveToNoSapi($targetPath)
    {
        if (!is_uploaded_file($this->file)) {
            throw new \RuntimeException('The file was not downloaded by HTTP POST.');
        }
        if (!move_uploaded_file($this->file, $targetPath)) {
            throw new \RuntimeException('An error occurred while moving the file.');
        }

        return true;
    }

    /**
     * Si le fichier ne contient pas le code UPLOAD_ERR_OK.
     *
     * @return bool
     */
    private function isError()
    {
        return $this->error !== UPLOAD_ERR_OK;
    }
}
