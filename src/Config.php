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
     * @var array
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
        $this->path = Util::cleanPath(sprintf('%s/%s', $pathConfig, $pathEnv));
    }

    /**
     * Si l'élément existe dans la configuration.
     *
     * @param string $strKey "nom_fichier" OU "nom_fichier.nom_clé".
     */
    public function has(string $strKey): bool
    {
        $keys = $this->prepareKey($strKey);
        $this->loadConfig($keys[0]);

        $array = $this->data;
        foreach ($keys as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return false;
            }
        }

        return true;
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
        $keys = self::prepareKey($strKey);
        $this->loadConfig($keys[0]);

        $array = $this->data;
        foreach ($keys as $segment) {
            if (is_array($array) && array_key_exists($segment, $array)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
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
        $keys = self::prepareKey($strKey);
        $file = $this->loadConfig($keys[0]);

        if (!isset($this->data[$file])) {
            Util::createJson($this->path, $file, []);
        }

        $array = &$this->data;
        foreach ($keys as $i => $key) {
            if (count($keys) === 1) {
                break;
            }

            unset($keys[$i]);

            $array = &$array[$key];
        }

        $array[array_shift($keys)] = $value;
        $this->data[$file]= (array) $this->data[$file];

        Util::saveJson($this->path, $file, $this->data[$file]);

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
        $keys = self::prepareKey($strKey);
        $file = $this->loadConfig($keys[0]);
        
        if (!isset($this->data[ $file ])) {
            return $this;
        }

        if (count($keys) === 1) {
            unset($this->data[$file]);
            unlink($this->getPathname($file));

            return $this;
        }

        $array = &$this->data;
        foreach ($keys as $i => $key) {
            if (count($keys) === 1) {
                break;
            }

            unset($keys[$i]);

            if (!isset($array[$key]) || !is_array($array[$key])) {
                break;
            }

            $array = &$array[$key];
        }

        unset($array[array_shift($keys)]);
        Util::saveJson($this->path, $file, $this->data[$file]);

        return $this;
    }

    /**
     * Retourne le chemin des fichiers de configuration.
     *
     * @codeCoverageIgnore getter
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
     * @return array<string>
     */
    protected static function prepareKey(string $strKey): array
    {
        return explode('.', trim($strKey, '.'));
    }

    /**
     * Charge et garde en mémoire les données de configuration.
     *
     * @param string $nameConfig Nom du fichier de configuration
     */
    protected function loadConfig(string $nameConfig): string
    {
        if (isset($this->data[$nameConfig])) {
            return $nameConfig;
        }

        $file =  $this->getPathname($nameConfig);

        if (file_exists($file)) {
            $this->data[$nameConfig] = Util::getJson($file);
        }

        return $nameConfig;
    }

    private function getPathname(string $finename): string
    {
        return sprintf('%s/%s.json', $this->path, $finename);
    }
}
