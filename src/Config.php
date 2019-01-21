<?php

/**
 * Soosyze Framework http://soosyze.com
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
class Config
{
    /**
     * Le chemin des fichiers de configuration.
     *
     * @var string
     */
    private $path = '';

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
        $str   = rtrim($strKey, '.');
        $split = explode('.', $str);

        $file = $this->path . $split[ 0 ] . '.json';

        if (!isset($split[ 1 ])) {
            return file_exists($file);
        }

        $key      = Util::strReplaceFirst($split[ 0 ] . '.', '', $str);
        $settings = Util::getJson($file);

        return isset($settings[ $key ]);
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
        $str   = rtrim($strKey, '.');
        $split = explode('.', $str);

        $file = $this->path . $split[ 0 ] . '.json';

        try {
            $settings = Util::getJson($file);
        } catch (\Exception $e) {
            return $default;
        }

        if (!isset($split[ 1 ])) {
            return $settings;
        }

        $key = Util::strReplaceFirst($split[ 0 ] . '.', '', $str);

        return isset($settings[ $key ])
            ? $settings[ $key ]
            : $default;
    }

    /**
     * Enregistre un élément de configuration.
     *
     * @param string $strKey "nom_fichier.nom_clé".
     * @param mixed  $value  Valeur à stocker.
     *
     * @throws \InvalidArgumentException La clé est invalide, elle doit être composée de 2 parties séparées par un point.
     * @return bool                      L'élément est bien enregistré.
     */
    public function set($strKey, $value)
    {
        $str   = rtrim($strKey, '.');
        $split = explode('.', $str);
        if (!isset($split[ 1 ]) || in_array('', $split)) {
            throw new \InvalidArgumentException(htmlspecialchars(
                "Key $strKey is invalid, it must be composed of 2 parts separated by a point."
            ));
        }

        $path = $this->path;
        $file = $split[ 0 ];

        $key = Util::strReplaceFirst($split[ 0 ] . '.', '', $str);

        if (!file_exists($path . $file . '.json')) {
            $data[ $key ] = $value;

            return Util::createJson($path, $file, $data);
        }
        $data         = Util::getJson($path . $file . '.json');
        $data[ $key ] = $value;

        return Util::saveJson($path, $file, $data);
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
}
