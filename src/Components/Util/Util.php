<?php

declare(strict_types=1);

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
    public const DS = DIRECTORY_SEPARATOR;

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
     *
     * @return array|object
     */
    public static function getJson(string $strFile, bool $assoc = true)
    {
        // @codeCoverageIgnoreStart
        if (!extension_loaded('json')) {
            throw new \Exception('The JSON extension is not loaded.');
        }
        // @codeCoverageIgnoreEnd
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

        $data = file_get_contents($strFile);
        if ($data === false) {
            throw new \RuntimeException(
                htmlspecialchars("An error is encountered while reading $strFile file.")
            );
        }
        $out = json_decode($data, $assoc, 512, JSON_UNESCAPED_UNICODE);

        if (!is_array($out) && !is_object($out)) {
            throw new \Exception(
                htmlspecialchars("The JSON $strFile file is invalid.")
            );
        }

        return $out;
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
    public static function createJson(
        string $strPath,
        string $strFileName,
        array $data = []
    ): ?bool {
        $cleanPath = self::cleanPath($strPath);

        if (!file_exists($strPath)) {
            mkdir($strPath, 0775);
        }

        $pathFile = $cleanPath . self::DS . $strFileName . '.json';
        if (file_exists($pathFile)) {
            return null;
        }

        $file = self::tryFopen($pathFile, 'w+');

        $jsonEncode = json_encode($data, JSON_UNESCAPED_UNICODE);
        if ($jsonEncode === false) {
            throw new \RuntimeException(
                "An error is encountered while serializing the $pathFile file."
            );
        }
        fwrite($file, $jsonEncode);

        return fclose($file);
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
    public static function saveJson(
        string $strPath,
        string $strFileName,
        array $data
    ): bool {
        $pathFile = self::cleanPath($strPath) . self::DS . $strFileName . '.json';
        $fp = self::tryFopen(
            $pathFile,
            'w'
        );

        $jsonEncode = json_encode($data, JSON_UNESCAPED_UNICODE);
        if ($jsonEncode === false) {
            throw new \RuntimeException(
                "An error is encountered while serializing the $pathFile file."
            );
        }
        fwrite($fp, $jsonEncode);

        return fclose($fp);
    }

    /**
     * Retourne l'extension d'un fichier passé en paramètre.
     *
     * @param string $pathFile Nom du fichier.
     *
     * @return string Extension du fichier ou une chaine de caractère vide.
     */
    public static function getFileExtension(string $pathFile): string
    {
        return strtolower(pathinfo($pathFile, PATHINFO_EXTENSION));
    }

    /**
     * Retourne l'extension d'un fichier passé en paramètre.
     *
     * @param string $pathFile Nom du fichier.
     *
     * @return string
     */
    public static function getFileBasename(string $pathFile): string
    {
        return strtolower(pathinfo($pathFile, PATHINFO_BASENAME));
    }

    /**
     * Retourne le nom des dossier contenus dans un répertoire.
     *
     * @param string   $dir     Nom du répertoire.
     * @param string[] $exclude Liste des repertoire à exclure du retour.
     *
     * @return array Liste des répertoires.
     */
    public static function getFolder(
        string $dir,
        array $exclude = [ '.', '..' ]
    ): array {
        $folder = [];
        foreach (new \DirectoryIterator($dir) as $file) {
            if ($file->isDir() && !\in_array($file->getBasename(), $exclude, true)) {
                $folder[] = $file->getBasename();
            }
        }

        return $folder;
    }

    /**
     * Ajoute un préfixe à chaque élément d'un tableau de string.
     *
     * @param string[] $values Tableau contenant les valeurs à préfixer.
     * @param string   $prefix Préfixe à ajouter.
     *
     * @return array Tableau préfixer.
     */
    public static function arrayPrefixValue(array $values, string $prefix): array
    {
        foreach ($values as &$value) {
            $value = $prefix . $value;
        }
        unset($value);

        return $values;
    }

    /**
     * Si une valeur insensible à la case est contenue dans un tableau.
     *
     * @param string   $needle Valeur recherché.
     * @param string[] $array  Tableau dans lequel chercher.
     *
     * @return bool Si la valeur est trouvé.
     */
    public static function inArrayToLower(string $needle, array $array): bool
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
    public static function arrayKeysExists(array $keys, array $data): bool
    {
        return count(array_intersect_key(array_flip($keys), $data)) === count($keys);
    }

    /**
     * Néttoye la chaine pour correspondre au chemin d'une requête.
     *
     * @param string $path          Chemin à nettoyer.
     * @param string $characterMask Liste de caractères à supprimer en début et fin de chaîne.
     *
     * @return string Chemin nettoyé.
     */
    public static function cleanPath(
        string $path,
        string $characterMask = "/ \t\n\r\0\x0B"
    ): string {
        $str = str_replace('\\', '/', $path);
        $str = preg_replace('/\/+/', '/', $str) ?? '';

        return rtrim($str, $characterMask);
    }

    /**
     * Néttoye la chaine pour correspondre au chemin du système.
     *
     * @param string $dir           Chemin à nettoyer.
     * @param string $characterMask Liste de caractères à supprimer en début et fin de chaîne.
     *
     * @return string Chemin nettoyé.
     */
    public static function cleanDir(
        string $dir,
        string $characterMask = "/ \t\n\r\0\x0B"
    ): string {
        $str = self::cleanPath($dir, $characterMask);

        return str_replace('/', DIRECTORY_SEPARATOR, $str);
    }

    /**
     * Recherche une chaine dans une autre et l'entoure d'une balise avec une classe CSS.
     *
     * @param string $needle         Chaîne recherché.
     * @param string $haystack       Chaine d'entrée
     * @param string $classHighlight Classe CSS de la surbrillance
     *
     * @return string
     */
    public static function strHighlight(
        string $needle,
        string $haystack,
        string $classHighlight = 'highlight'
    ): string {
        return $needle === ''
            ? $haystack
            : preg_replace('/' . preg_quote($needle, '/') . '/i', "<span class=\"$classHighlight\">$0</span>", $haystack) ?? $haystack;
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
    public static function strReplaceFirst(
        string $search,
        string $replace,
        string $subject
    ): string {
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
    public static function strReplaceLast(
        string $search,
        string $replace,
        string $subject
    ): string {
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
        int $length = 20,
        string $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_'
    ): string {
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
     * @param string $separator Caractère de remplacement.
     * @param string $ignore    Caractères ignorés par le remplacement.
     *
     * @return string
     */
    public static function strSlug(
        string $str,
        string $separator = '_',
        string $ignore = ''
    ): string {
        $output = mb_strtolower($str, 'UTF-8');
        $output = str_replace(self::$search, self::$replace, $output);
        $output = preg_replace('/([^\w' . $ignore . ']|_)+/i', $separator, $output) ?? '';

        return trim($output, $separator);
    }

    /**
     * À partir d'une valeur numérique, calcul et retourne son équivalent en taille de fichier.
     *
     * @param int    $size      Valeur numértique.
     * @param int    $precision Le nombre de zéro après la virgule.
     * @param string $default   Valeur par défaut en cas d'abence de valeur.
     *
     * @return string
     */
    public static function strFileSizeFormatted(
        int $size,
        int $precision = 2,
        string $default = ''
    ): string {
        $units  = [ 'b', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb' ];
        $power  = $size > 0
            ? floor(log($size, 1024))
            : 0;
        $number = number_format($size / pow(1024, $power), $precision, '.', ' ');

        return rtrim($number, '.0') !== ''
            ? rtrim($number, '.0') . ' ' . $units[ $power ]
            : $default;
    }

    /**
     * Retourne le nombre d'octet à partir du format de données utilisé par le fichier php.ini
     * Exemple, pour la chaine '1M' il sera retourné 1048576.
     *
     * @param string $shortBytes
     *
     * @return int|null
     */
    public static function getOctetShortBytesPhp(string $shortBytes): ?int
    {
        $unit = null;
        if (preg_match('/(?P<unit>k|m|g)+$/i', trim($shortBytes), $matches) !== false) {
            $unit = strtolower($matches[ 'unit' ] ?? '');
        }

        if (preg_match('/^(?P<value>\d+)+/i', trim($shortBytes), $matches) === false) {
            return null;
        }

        if (!isset($matches[ 'value' ])) {
            return null;
        }
        $value = (int) $matches[ 'value' ];

        switch ($unit) {
            case 'k':
                return $value * 1024;
            case 'm':
                return $value * 1048576;
            case 'g':
                return $value * 1073741824;
            default:
                return $value;
        }
    }

    /**
     * Retourne la quantité de données maximum à l'upload autorisé par votre configuration.
     * Ou null si aucune donnée minimum ne peut-être trouvée.
     * memory_limit: -1 no limit
     */
    public static function getOctetUploadLimit(): ?int
    {
        $limitMin = [];
        foreach ([ 'upload_max_filesize', 'post_max_size', 'memory_limit' ] as $ini) {
            $octet = ini_get($ini);
            $limit = self::getOctetShortBytesPhp(
                $octet === false
                    ? ''
                    : $octet
            );

            if ($limit !== null) {
                $limitMin[] = $limit;
            }
        }

        try {
            /** @var int $min */
            $min = min(...$limitMin);
        } catch (\ArgumentCountError $e) {
            return null;
        }

        return $min;
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
    public static function strHumansTimeDiff(
        \DateTimeInterface $from,
        string $to = 'now'
    ): array {
        $interval = self::tryDateCreate($to)->diff($from);

        if (($value = $interval->y) >= 1) {
            $str = $value > 1
                ? '%s years'
                : '%s year';
        } elseif (($value = $interval->m) >= 1) {
            $str = $value > 1
                ? '%s months'
                : '%s month';
        } elseif (($value = floor($interval->d / 7)) >= 1) {
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

        $suffix = $interval->invert !== 0
            ? ' ago'
            : '';

        return $value > 1
            ? [ $str . $suffix, $value ]
            : [ $str . $suffix, 1 ];
    }

    /**
     * @param string $filename
     * @param string $mode
     *
     * @throws \RuntimeException
     * @return resource
     */
    public static function tryFopen(string $filename, string $mode)
    {
        try {
            /** @var resource $handle */
            $handle = fopen($filename, $mode);
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf(
                'Unable to open "%s" using mode "%s".',
                $filename,
                $mode
            ), 0, $e);
        }

        return $handle;
    }

    /**
     * @return \DateTime|\DateTimeImmutable
     */
    public static function tryDateCreate(string $to = 'now'): \DateTimeInterface
    {
        $handle = \date_create($to);
        if ($handle === false) {
            throw new \InvalidArgumentException('The date must be in valid format.');
        }

        return $handle;
    }
}
