<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Util;

/**
 * Ensemble de méthodes aide au développement.
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class Util
{
    const DS = DIRECTORY_SEPARATOR;

    /**
     * Liste non exaustive des caractères accentués.
     *
     * @var string[]
     */
    protected static $search = [
        '°', '₀', '¹', '₁', '²', '₂', '³', '₃', '⁴', '₄', '⁵', '₅', '⁶', '₆',
        '⁷', '₇', '⁸', '₈', '⁹', '₉',
        'á', 'à', 'â', 'ä', 'ą', 'ⱥ', 'ǎ', 'ȧ', 'ạ', 'ā', 'ã',
        'ć', 'ĉ', 'ç', 'č', 'ċ',
        'é', 'è', 'ê', 'ë', 'ȩ', 'ę', 'ɇ', 'ě', 'ė', 'ẹ', 'ē', 'ẽ',
        'ĝ', 'ğ', 'ġ', 'ģ', 'ĥ',
        'í', 'ì', 'ĩ', 'ị', 'î', 'ï', 'ī', 'į', 'ɨ', 'ǐ', 'ĭ', 'ỉ',
        'ĵ', 'ɉ', 'ǰ',
        'ķ', 'ĸ', 'к', 'κ',
        'ĺ', 'ļ', 'ł', 'ƚ', 'ľ', 'ḷ',
        'ń', 'ǹ', 'ņ', 'ň', 'ṅ', 'ṇ', 'ñ',
        'ɵ', 'ǫ', 'ó', 'ò', 'ỏ', 'õ', 'ȯ', 'ọ', 'ô', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ',
        'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ', 'ø', 'ō', 'ő', 'ŏ', 'ο', 'ὀ', 'ὁ', 'ὂ',
        'ὃ', 'ὄ', 'ὅ', 'ὸ', 'ό', 'о', 'ǒ', 'ǿ',
        'ś', 'ŝ', 'ş', 'š', 'ṡ', 'ṣ',
        'ẗ', 'ţ', 'ⱦ', 'ŧ', 'ť', 'ṫ', 'ṭ',
        'ú', 'ù', 'û', 'ü', 'ų', 'ʉ', 'ǔ', 'ụ', 'ū', 'ũ',
        'ý', 'ỳ', 'ŷ', 'ÿ', 'ɏ', 'ẏ', 'ỵ', 'ȳ', 'ỹ',
        'ź', 'ẑ', 'ƶ', 'ž', 'ż', 'ẓ',
        'æ', 'ǽ', 'œ'
    ];

    /**
     * Liste des caractères de remplacement pour les caractères accentués.
     *
     * @var string[]
     */
    protected static $replace = [
        '0', '0', '1', '1', '2', '2', '3', '3', '4', '4', '5', '5', '6', '6',
        '7', '7', '8', '8', '9', '9',
        'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a',
        'c', 'c', 'c', 'c', 'c',
        'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e',
        'g', 'g', 'g', 'g', 'h',
        'i', 'i', 'i', 'i', 'i', 'i', 'i', 'i', 'i', 'i', 'i', 'i',
        'j', 'j', 'j',
        'k', 'k', 'k', 'k',
        'l', 'l', 'l', 'l', 'l', 'l',
        'n', 'n', 'n', 'n', 'n', 'n', 'n',
        'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
        'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
        'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o',
        's', 's', 's', 's', 's', 's',
        't', 't', 't', 't', 't', 't', 't',
        'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u',
        'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y', 'y',
        'z', 'z', 'z', 'z', 'z', 'z',
        'ae', 'ae', 'oe'
    ];

    /**
     * Lit un fichier de type JSON et retourne un tableau associatif.
     *
     * @param string $strFile Chemin + nom du fichier + extension.
     * @param bool   $assoc   Si true l'objet retourné sera converti en un tableau associatif.
     *
     * @throws \Exception                L'extension JSON n'est pas chargé.
     * @throws \InvalidArgumentException Le fichier est manquant.
     * @throws \InvalidArgumentException L'extension du fichier n'est pas au format JSON.
     * @throws \Exception                Le fichier JSON n'est pas accessible en lecture.
     * @throws \Exception                Le fichier JSON est invalide.
     * @return array|object
     */
    public static function getJson($strFile, $assoc = true)
    {
        // @codeCoverageIgnoreStart
        if (!extension_loaded('json')) {
            throw new \Exception('The JSON extension is not loaded.');
        }
        // @codeCoverageIgnoreEnd
        if (!is_string($strFile)) {
            throw new \Exception(
                htmlspecialchars('The file is not readable.')
            );
        }
        if (!file_exists($strFile)) {
            throw new \InvalidArgumentException(
                htmlspecialchars("The $strFile file is missing.")
            );
        }
        if (self::getFileExtension($strFile) !== 'json') {
            throw new \InvalidArgumentException(
                htmlspecialchars("The $strFile is not in JSON format.")
            );
        }
        if (($return = json_decode(file_get_contents($strFile), $assoc)) === null) {
            throw new \Exception(
                htmlspecialchars("The JSON $strFile file is invalid.")
            );
        }

        return $return;
    }

    /**
     * Créer un fichier au format JSON si celui si n'existe pas.
     *
     * @param string $strPath     Chemin du fichier.
     * @param string $strFileName Nom du fichier.
     * @param array  $data        Les données.
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
     * @param string $strPath     Chemin du fichier.
     * @param string $strFileName Nom du fichier.
     * @param array  $data        Les données.
     *
     * @return bool Si le fichier JSON a été sauvegardé.
     */
    public static function saveJson($strPath, $strFileName, array $data)
    {
        $fp = fopen(
            self::cleanPath($strPath) . self::DS . $strFileName . '.json',
            'w'
        );
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
     * @param string   $dir     Nom du répertoire.
     * @param string[] $exclude Liste des repertoire à exclure du retour.
     *
     * @return array Liste des répertoires.
     */
    public static function getFolder($dir, $exclude = [ '.', '..' ])
    {
        $folder = [];
        foreach (new \DirectoryIterator($dir) as $file) {
            if ($file->isDir() && !in_array($file->getBasename(), $exclude)) {
                $folder[] = $file->getBasename();
            }
        }

        return $folder;
    }

    /**
     * Ajoute un préfixe à chaque élément d'un tableau de string.
     *
     * @param string[] $array  Tableau contenant les valeurs à préfixer.
     * @param string   $prefix Préfixe à ajouter.
     *
     * @return array Tableau préfixer.
     */
    public static function arrayPrefixValue($array, $prefix)
    {
        foreach ($array as &$value) {
            $value = $prefix . $value;
        }

        return $array;
    }

    /**
     * Si une valeur insensible à la case est contenue dans un tableau.
     *
     * @param string   $needle Valeur recherché.
     * @param string[] $array  Tableau dans lequel chercher.
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
     * Néttoye la chaine pour correspondre au chemin d'une requête.
     *
     * @param string $path           Chemin à nettoyer.
     * @param string $character_mask Liste de caractères à supprimer en début et fin de chaîne.
     *
     * @return string Chemin nettoyé.
     */
    public static function cleanPath($path, $character_mask = "/ \t\n\r\0\x0B")
    {
        $str = str_replace('\\', '/', $path);
        $str = preg_replace('/\/+/', '/', $str);

        return rtrim($str, $character_mask);
    }

    /**
     * Néttoye la chaine pour correspondre au chemin du système.
     *
     * @param string $dir            Chemin à nettoyer.
     * @param string $character_mask Liste de caractères à supprimer en début et fin de chaîne.
     *
     * @return string Chemin nettoyé.
     */
    public static function cleanDir($dir, $character_mask = "/ \t\n\r\0\x0B")
    {
        $str = self::cleanPath($dir, $character_mask);

        return str_replace('/', DIRECTORY_SEPARATOR, $str);
    }

    /**
     * Remplace la première occurrence dans une chaine.
     *
     * @param string $search  Chaîne recherché.
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
     * @param string $search  Chaîne recherché.
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

    /**
     * Génère une chaine aléatoire.
     *
     * @param int    $length Longueur de la chaîne à générer.
     * @param string $chars  Liste de caractères utilisés pour la génération aléatoire.
     *
     * @return string
     */
    public static function strRandom(
        $length = 20,
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_'
    ) {
        $str = '';
        for ($i = 0; $i < $length; $i++) {
            $str .= $chars[ rand(0, strlen($chars) - 1) ];
        }

        return $str;
    }

    /**
     * Remplace les caractères accentués par leurs équivalent sans accent,
     * remplace les caractères non alphanumériques hors le tiret par le caractère de séparation
     * et supprime au début et à la fin de la chaine les caractères de séparation et tirets.
     *
     * @param string $str       Chaîne de caractère à traiter.
     * @param char   $separator Caractère de remplacement.
     *
     * @return string
     */
    public static function strSlug($str, $separator = '_', $ignore = '')
    {
        $output = mb_strtolower($str, 'UTF-8');
        $output = str_replace(self::$search, self::$replace, $output);
        $output = preg_replace('/([^\w' . $ignore . ']|_)+/i', $separator, $output);

        return trim($output, $separator);
    }

    /**
     * À partir d'une valeur numérique, calcul et retourne son équivalent en taille de fichier.
     *
     * @param int $size      Valeur numértique.
     * @param int $precision Le nombre de zéro après la virgule.
     *
     * @return string
     */
    public static function strFileSizeFormatted($size, $precision = 2)
    {
        $units  = [ 'b', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb' ];
        $power  = $size > 0
            ? floor(log($size, 1024))
            : 0;
        $number = number_format($size / pow(1024, $power), $precision, '.', ' ');

        return rtrim($number, '.00') . ' ' . $units[ $power ];
    }

    /**
     * Différence entre 2 dates dans un format lisible par l'homme.
     *
     * @param \DateTime $from
     * @param string    $to
     *
     * @return array La première valeur est la chaine de caractère
     *               et la seconde la valeur numérique à remplacer.
     */
    public static function strHumansTimeDiff(\DateTime $from, $to = 'now')
    {
        $interval = \date_create($to)->diff($from);

        if (($value = $interval->y) >= 1) {
            $str = $value > 1
                ? '%s years'
                : '%s year';
        } elseif (($value = $interval->m) >= 1) {
            $str = $value > 1
                ? '%s months'
                : '%s month';
        } elseif (($value = $interval->d / 7) >= 1) {
            $str = $value > 1
                ? '%s weeks'
                : '%s week';
        } elseif (($value = $interval->d) >= 1) {
            $str = $value > 1
                ? '%s days'
                : '%s day';
        } elseif (($value = $interval->h) >= 1) {
            $str = $value > 1
                ? '%s hours'
                : '%s hour';
        } elseif (($value = $interval->i) >= 1) {
            $str = $value > 1
                ? '%s minutes'
                : '%s minute';
        } else {
            $value = $interval->s;
            $str   = $value > 1
                ? '%s seconds'
                : '%s second';
        }

        $suffix = $interval->invert
            ? ' ago'
            : '';

        return $value > 1
            ? [ $str . $suffix, $value ]
            : [ $str . $suffix, 1 ];
    }
}
