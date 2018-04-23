<?php

/**
 * Soosyze Framework http://soosyze.com
 * 
 * @package Soosyze\Components\Util
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Util;

/**
 * Ensemble de méthodes aide au développement.
 *
 * @author Mathieu NOËL
 */
class Util
{

    /**
     * Lit un fichier de type JSON et retourne un tableau associatif.
     *
     * @param string $file Chemin + nom du fichier + extension.
     * @param bool $assoc Si true l'objet retourné sera converti en un tableau associatif.
     *
     * @return array|object
     * 
     * @throws FileNotFoundException Le fichier est manquant.
     * @throws ExtensionNotLoadedException L'extension JSON n'est pas chargé.
     * @throws \Exception Le fichier n'est pas au format JSON.
     */
    public static function getJson( $file, $assoc = true )
    {
        if( !file_exists($file) )
        {
            throw new \Exception('The ' . htmlspecialchars($file) . ' file is missing.');
        }
        /* @codeCoverageIgnoreStart */
        if( !extension_loaded('json') )
        {
            throw new \Exception('The JSON extension is not loaded.');
        }
        /* @codeCoverageIgnoreEnd */
        if( strrchr($file, '.') != '.json' )
        {
            throw new \Exception('The ' . htmlspecialchars($file) . ' is not in JSON format.');
        }

        $json   = file_get_contents($file);
        $return = json_decode($json, $assoc);

        if( $return === null )
        {
            throw new \Exception('The ' . htmlspecialchars($file) . ' is not in JSON format.');
        }
        return $return;
    }

    /**
     * Créer un fichier au format JSON si celui si n'existe pas.
     *
     * @param string $path Chemin du fichier.
     * @param string $file Nom du fichier.
     * @param array $data Les données.
     *
     * @return bool|null Si le fichier JSON est créé.
     */
    public static function createJson( $path, $file, array $data = [] )
    {
        if( !file_exists($path) )
        {
            mkdir($path, 0775);
        }
        $pathFil = $path . DIRECTORY_SEPARATOR . $file . '.json';
        if( !file_exists($pathFil) )
        {
            $fichier = fopen($pathFil, 'w+');
            fwrite($fichier, json_encode($data));
            return fclose($fichier);
        }
        return null;
    }

    /**
     * Sauvegarde des données dans un fichier au format JSON.
     *
     * @param string $path Chemin du fichier.
     * @param string $file Nom du fichier.
     * @param array $data Les données.
     *
     * @return bool Si le fichier JSON a été sauvegardé.
     */
    public static function saveJson( $path, $file, array $data )
    {
        $fp = fopen($path . DIRECTORY_SEPARATOR . $file . '.json', 'w');
        fwrite($fp, json_encode($data));
        return fclose($fp);
    }

    /**
     * Retourne l'extension d'un fichier passé en paramètre.
     *
     * @param string $pathFile Nom du fichier.
     *
     * @return string Extension du fichier ou une chaine de caractère vide.
     */
    public static function getFileExtension( $pathFile )
    {
        return strtolower(substr(strrchr($pathFile, '.'), 1));
    }

    /**
     * Retourne le nom des dossier contenus dans un répertoire.
     *
     * @param string $dir Nom du répertoire.
     * @param string[] $exclude Liste des repertoire à exclure du retour.
     *
     * @return array Liste des répertoires.
     */
    public static function getFolder( $dir, $exclude = [ '.', '..' ] )
    {
        $folder = [];
        if( ($dh     = opendir($dir) ) )
        {
            while( ($file = readdir($dh)) !== false )
            {
                if( !in_array($file, $exclude) && self::getFileExtension($file) === '' )
                {
                    $folder[] = $file;
                }
            }
            closedir($dh);
        }
        return $folder;
    }

    /**
     * Ajoute un préfixe à chaque élément d'un tableau de string.
     *
     * @param string[] $array Tableau contenant les valeurs à préfixer.
     * @param string $prefix Préfixe à ajouter.
     *
     * @return array Tableau préfixer.
     */
    public static function arrayPrefixValue( $array, $prefix )
    {
        array_walk($array, function(&$item1, $key, $prefix)
        {
            $item1 = $prefix . $item1;
        }, $prefix);
        return $array;
    }

    /**
     * Si une valeur insensible à la case est contenue dans un tableau.
     *
     * @param string $needle Valeur recherché.
     * @param string[] $array Tableau dans lequel chercher.
     *
     * @return bool Si la valeur est trouvé.
     */
    public static function inArrayToLower( $needle, array $array )
    {
        return in_array(strtolower($needle), array_map('strtolower', $array));
    }

    /**
     * Vérifie la présence d'une liste de clés dans un tableau associatif.
     * 
     * @param array $keys Liste des clés.
     * @param array $data Tableau associatif.
     * 
     * @return bool si toutes les clés sont présentes
     */
    public static function arrayKeysExists( array $keys, array $data )
    {
        return count(array_intersect_key(array_flip($keys), $data)) === count($keys);
    }
}