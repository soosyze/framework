<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @package Soosyze
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze;

use Soosyze\Components\Util\Util;

/**
 * Enregistre et restitue dans des fichiers de configuration de l'application.
 *
 * @author Mathieu NOËL
 */
class Config implements \ArrayAccess
{
    /**
     * Le chemin des fichiers de configuration.
     *
     * @var string
     */
    private $path = '';

    /**
     * Les données de la configurations.
     *
     * @var mixed[]
     */
    private $data = [];

    /**
     * Déclare le chemin des fichiers de configuration à partir
     * d'un chemin de base + un chemin en fonction de l'environnements.
     *
     * @param string $pathConfig Chemin de base des fichiers de configuration
     * @param string $pathEnv    Chemin des fichiers de configuration par environnement.
     */
    public function __construct($pathConfig, $pathEnv = '')
    {
        $this->path = Util::cleanPath($pathConfig) . Util::DS;
        $this->path .= $pathEnv !== ''
            ? Util::cleanPath($pathEnv) . Util::DS
            : '';
    }

    /**
     * Si l'élément existe dans la configuration.
     *
     * @param string $strKey "nom_fichier" OU "nom_fichier.nom_clé".
     *
     * @return bool
     */
    public function has($strKey)
    {
        list($file, $key) = $this->prepareKey($strKey);
        $this->loadConfig($file);

        return $key
            ? isset($this->data[ $file ][ $key ])
            : isset($this->data[ $file ]);
    }

    /**
     * Récupère un élément de configuration en fonction
     * de l'emplacement de l'application, l'environnement, du fichier et d'une clé.
     *
     * @param string     $strKey  "nom_fichier" OU "nom_fichier.nom_clé".
     * @param mixed|null $default Valeur par défaut si aucune valeur n'est trouvé.
     *
     * @return array|mixed|null Tableau des paramètres ou le paramètre si la clé est renseignée ou null.
     */
    public function get($strKey, $default = null)
    {
        list($file, $key) = $this->prepareKey($strKey);
        $this->loadConfig($file);

        if ($key) {
            return isset($this->data[ $file ][ $key ])
                ? $this->data[ $file ][ $key ]
                : $default;
        }

        return isset($this->data[ $file ])
            ? $this->data[ $file ]
            : $default;
    }

    /**
     * Enregistre un élément de configuration.
     *
     * @param string $strKey "nom_fichier.nom_clé".
     * @param mixed  $value  Valeur à stocker.
     *
     * @throws \InvalidArgumentException La clé est invalide, elle doit être composée de 2 parties séparées par un point.
     * @return this
     */
    public function set($strKey, $value)
    {
        list($file, $key) = $this->prepareKey($strKey);
        $this->loadConfig($file);
        $hasFile = isset($this->data[ $file ]);

        if ($key) {
            $this->data[ $file ][ $key ] = $value;
        } else {
            $this->data[ $file ] = !is_array($value)
                ? [ $value ]
                : $value;
        }
        if ($hasFile) {
            Util::saveJson($this->path, $file, $this->data[ $file ]);
        } else {
            Util::createJson($this->path, $file, $this->data[ $file ]);
        }

        return $this;
    }

    /**
     * Supprime un élément de configuration.
     *
     * @param string $strKey "nom_fichier.nom_clé".
     *
     * @throws \InvalidArgumentException
     * @return this
     */
    public function del($strKey)
    {
        list($file, $key) = $this->prepareKey($strKey);
        $this->loadConfig($file);

        if (!isset($this->data[ $file ])) {
            return $this;
        }

        if ($key) {
            unset($this->data[ $file ][ $key ]);
            Util::saveJson($this->path, $file, $this->data[ $file ]);
        } else {
            unset($this->data[ $file ]);
            unlink($this->path . $file . '.json');
        }

        return $this;
    }

    /**
     * Retourne le chemin des fichiers de configuration.
     *
     * @codeCoverageIgnore getter
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Indique si une position existe dans un tableau.
     *
     * @see https://www.php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset Position à vérifier.
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return $this->has($offset);
    }

    /**
     * Position à lire.
     *
     * @see https://www.php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Assigne une valeur à une position donnée.
     *
     * @see https://www.php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * Supprime un élément à une position donnée.
     *
     * @see https://www.php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        $this->del($offset);
    }

    /**
     * Sépare le nom du fichier de la clé.
     *
     * @param string $strKey Nom de la clé.
     *
     * @throws \InvalidArgumentException The key must be a non-empty string.
     *
     * @return array
     */
    protected function prepareKey($strKey)
    {
        if (!is_string($strKey) || $strKey === '') {
            throw new \InvalidArgumentException(
                'The key must be a non-empty string.'
            );
        }

        $str        = trim($strKey, '.');
        $split[ 0 ] = $str;
        if (strpos($str, '.') !== false) {
            $split[ 0 ] = strstr($str, '.', true);
            $split[ 1 ] = trim(strstr($str, '.'), '.');
        }

        return isset($split[ 1 ])
            ? [ $split[ 0 ], $split[ 1 ] ]
            : [ $split[ 0 ], null ];
    }

    /**
     * Charge et garde en mémoire les données de configuration.
     *
     * @param string $nameConfig Nom du fichier de configuration
     *
     * @return void
     */
    protected function loadConfig($nameConfig)
    {
        if (isset($this->data[ $nameConfig ])) {
            return;
        }

        $file = $this->path . $nameConfig . '.json';

        if (file_exists($file)) {
            $this->data[ $nameConfig ] = Util::getJson($file);
        }
    }
}
