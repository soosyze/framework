<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze;

use Soosyze\Components\Util\Util;

/**
 * Enregistre et restitue dans des fichiers de configuration de l'application.
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 * @implements \ArrayAccess<string, scalar>
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
     * @var array<string, array<string, array|null|scalar>>
     */
    private $data = [];

    /**
     * Déclare le chemin des fichiers de configuration à partir
     * d'un chemin de base + un chemin en fonction de l'environnements.
     *
     * @param string $pathConfig Chemin de base des fichiers de configuration
     * @param string $pathEnv    Chemin des fichiers de configuration par environnement.
     */
    public function __construct(string $pathConfig, string $pathEnv = '')
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
    public function has(string $strKey): bool
    {
        [ $file, $key ] = $this->prepareKey($strKey);
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
    public function get(string $strKey, $default = null)
    {
        [ $file, $key ] = $this->prepareKey($strKey);
        $this->loadConfig($file);

        if ($key) {
            return $this->data[ $file ][ $key ] ?? $default;
        }

        return $this->data[ $file ] ?? $default;
    }

    /**
     * Enregistre un élément de configuration.
     *
     * @param string $strKey "nom_fichier.nom_clé".
     * @param mixed  $value  Valeur à stocker.
     *
     * @throws \InvalidArgumentException La clé est invalide, elle doit être composée de 2 parties séparées par un point.
     * @return $this
     */
    public function set(string $strKey, $value): self
    {
        [ $file, $key ] = $this->prepareKey($strKey);
        $this->loadConfig($file);

        if ($key) {
            $this->data[ $file ][ $key ] = $value;
        } else {
            $this->data[ $file ] = is_array($value)
                ? $value
                : [ $value ];
        }
        if (isset($this->data[ $file ])) {
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
     * @return $this
     */
    public function del(string $strKey): self
    {
        [ $file, $key ] = $this->prepareKey($strKey);
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
    public function getPath(): string
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
    public function offsetExists($offset): bool
    {
        if (!is_string($offset)) {
            throw new \InvalidArgumentException(
                sprintf('The key of must be of type string: %s given', gettype($offset))
            );
        }

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
        if (!is_string($offset)) {
            throw new \InvalidArgumentException(
                sprintf('The key of must be of type string: %s given', gettype($offset))
            );
        }

        return $this->get($offset);
    }

    /**
     * Assigne une valeur à une position donnée.
     *
     * @see https://www.php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        if (!is_string($offset)) {
            throw new \InvalidArgumentException(
                sprintf('The key of must be of type string: %s given', gettype($offset))
            );
        }
        $this->set($offset, $value);
    }

    /**
     * Supprime un élément à une position donnée.
     *
     * @see https://www.php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset
     *
     * @return void
     */
    public function offsetUnset($offset): void
    {
        if (!is_string($offset)) {
            throw new \InvalidArgumentException(
                sprintf('The key of must be of type string: %s given', gettype($offset))
            );
        }
        $this->del($offset);
    }

    /**
     * Sépare le nom du fichier de la clé.
     *
     * @param string $strKey Nom de la clé.
     *
     * @return array
     * @phpstan-return array{string, string|null}
     */
    protected function prepareKey(string $strKey): array
    {
        $file = strstr($strKey, '.');
        if ($file !== false) {
            return [ trim($strKey, $file . '.'), trim($file, '.') ];
        }

        return [ $strKey, null ];
    }

    /**
     * Charge et garde en mémoire les données de configuration.
     *
     * @param string $nameConfig Nom du fichier de configuration
     *
     * @return void
     */
    protected function loadConfig(string $nameConfig): void
    {
        if (isset($this->data[ $nameConfig ])) {
            return;
        }

        $file = $this->path . $nameConfig . '.json';

        if (file_exists($file)) {
            /** @phpstan-ignore-next-line */
            $this->data[ $nameConfig ] = Util::getJson($file);
        }
    }
}
