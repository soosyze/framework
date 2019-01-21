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
    const DS = DIRECTORY_SEPARATOR;

    /**
     * Lit un fichier de type JSON et retourne un tableau associatif.
     *
     * @param string $file Chemin + nom du fichier + extension.
     * @param bool $assoc Si true l'objet retourné sera converti en un tableau associatif.
     *
     * @return array|object
     *
     * @throws \Exception L'extension JSON n'est pas chargé.
     * @throws \InvalidArgumentException Le fichier est manquant.
     * @throws \InvalidArgumentException L'extension du fichier n'est pas au format JSON.
     * @throws \Exception Le fichier JSON n'est pas accessible en lecture.
     * @throws \Exception Le fichier JSON est invalide.
     */
    public static function getJson($file, $assoc = true)
    {
        // @codeCoverageIgnoreStart
        if (!extension_loaded('json')) {
            throw new \Exception('The JSON extension is not loaded.');
        }
        // @codeCoverageIgnoreEnd
        if (!file_exists($file)) {
            throw new \InvalidArgumentException(htmlspecialchars("The $file file is missing."));
        }
        if (strrchr($file, '.') != '.json') {
            throw new \InvalidArgumentException(htmlspecialchars("The $file is not in JSON format."));
        }
        if (($json = file_get_contents($file)) === null) {
            throw new \Exception(htmlspecialchars("The $file file is not readable."));
        }
        if (($return = json_decode($json, $assoc)) === null) {
            throw new \Exception(htmlspecialchars("The JSON $file file is invalid."));
        }

        return $return;
    }

    /**
     * Créer un fichier au format JSON si celui si n'existe pas.
     *
     * @param string $strPath Chemin du fichier.
     * @param string $strFileName Nom du fichier.
     * @param array $data Les données.
     *
     * @return bool|null Si le fichier JSON est créé.
     */
    public static function createJson($strPath, $strFileName, array $data = [])
    {
        $cleanPath = self::cleanPath($strPath);

        if (!file_exists($strPath)) {
            mkdir($strPath, 0775);
        }

        $pathFile = $cleanPath . self::DS . $strFileName . '.json';

        if (!file_exists($pathFile)) {
            $file = fopen($pathFile, 'w+');
            fwrite($file, json_encode($data));

            return fclose($file);
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
    public static function saveJson($path, $file, array $data)
    {
        $fp = fopen(self::cleanPath($path) . self::DS . $file . '.json', 'w');
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
    public static function getFileExtension($pathFile)
    {
        return strtolower(pathinfo($pathFile, PATHINFO_EXTENSION));
    }

    /**
     * Retourne le nom des dossier contenus dans un répertoire.
     *
     * @param string $dir Nom du répertoire.
     * @param string[] $exclude Liste des repertoire à exclure du retour.
     *
     * @return array Liste des répertoires.
     */
    public static function getFolder($dir, $exclude = [ '.', '..' ])
    {
        $folder = [];
        if (($dh     = opendir($dir))) {
            while (($file = readdir($dh)) !== false) {
                if (!in_array($file, $exclude) && self::getFileExtension($file) === '') {
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
    public static function arrayPrefixValue($array, $prefix)
    {
        array_walk($array, function (&$item1, $key, $prefix) {
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
    public static function inArrayToLower($needle, array $array)
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
    public static function arrayKeysExists(array $keys, array $data)
    {
        return count(array_intersect_key(array_flip($keys), $data)) === count($keys);
    }

    /**
     * Remplace les barres obliques et barres obliques inversées par le séparateur du système
     * et supprime les espaces et slash en début et fin de chaîne.
     *
     * @param string $path Chemin nettoyé.
     * @param string $character_mask Liste de caractères à supprimer en début et fin de chaîne.
     *
     * @return string Chemin nettoyé.
     */
    public static function cleanPath($path, $character_mask = "\// \t\n\r\0\x0B/")
    {
        $str = str_replace([ '\\', '/' ], self::DS, $path);

        return rtrim($str, $character_mask);
    }

    /**
     * Remplace la première occurrence dans une chaine.
     *
     * @param string $search Chaîne recherché.
     * @param string $replace Chaîne de remplacement.
     * @param string $subject Chaîne d'entrée.
     *
     * @return string
     */
    public static function strReplaceFirst($search, $replace, $subject)
    {
        if (($pos = strpos($subject, $search)) !== false) {
            return substr_replace($subject, $replace, $pos, strlen($search));
        }

        return $subject;
    }

    /**
     * Remplace la dernière occurrence dans une chaine.
     *
     * @param string $search Chaîne recherché.
     * @param string $replace Chaîne de remplacement.
     * @param string $subject Chaîne d'entrée.
     *
     * @return string
     */
    public static function strReplaceLast($search, $replace, $subject)
    {
        if (($pos = strrpos($subject, $search)) !== false) {
            return substr_replace($subject, $replace, $pos, strlen($search));
        }

        return $subject;
    }
}
