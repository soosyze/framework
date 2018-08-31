<?php

/**
 * Soosyze Framework http://soosyze.com
 *
 * @package Soosyze
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze;

/**
 * Permet de charger les objets en fonction de leur namespace.
 *
 * @see https://www.php-fig.org/psr/psr-4/ Suit les recommandations PSR-4.
 *
 * @author Mathieu NOËL
 */
class Autoload
{
    const DS             = DIRECTORY_SEPARATOR;
    /**
     * Tableau avec comme clés un namespace et en valeur la racine de son arborescence.
     *
     * @var array
     */
    protected $lib = [];

    /**
     * Liste les répertoires à parcourir pour le chargement.
     *
     * @var array
     */
    protected $map = [];

    /**
     * Créer notre autoload à partir de la liste des namespace.
     *
     * @param array $lib
     */
    public function __construct(array $lib = [])
    {
        $this->lib = $lib;
    }

    /**
     * Ajoute une liste de namespace.
     *
     * @param array $lib
     *
     * @return $this
     */
    public function setLib(array $lib)
    {
        $this->lib = $lib;

        return $this;
    }

    /**
     * Ajoute une map à parcourir pour y trouver des classes.
     *
     * @param array $map
     *
     * @return $this
     */
    public function setMap(array $map)
    {
        $this->map = $map;

        return $this;
    }

    /**
     * Appel l'autoload register.
     */
    public function register()
    {
        spl_autoload_register([ __CLASS__, 'autoload' ]);
    }

    /**
     * Pour tous les fichiers de la librairie, on cherche le fichier requit.
     * Le nom de l'objet, le namespace, l'emplacement doit respecter les recommandations PSR-4.
     *
     * @see http://www.php-fig.org/psr/psr-4/
     *
     * @param string|bool $class le nom de la classe appelée
     */
    public function autoload($class)
    {
        /* On explose la classe par '\' */
        $parts = preg_split('#\\\#', $class);

        /* On extrait le dernier element. */
        $className = array_pop($parts);

        /* On créé le chemin vers la classe */
        $path = implode(self::DS, $parts);
        $file = $className . '.php';

        /*
         * Si la classe recherchée est à la racine du namespace le chemin sera
         * égale à la clé. Cette condition peut éviter la boucle.
         */
        if (isset($this->lib[ $path ])) {
            $filepath = $this->relplaceSlash($this->lib[ $path ] . self::DS . $file);

            if ($this->requireFile($filepath)) {
                return $filepath;
            }
        }

        /**
         * Recherche une correspondance entre le namespace fournit en librairie
         * et la classe instanciée.
         */
        foreach ($this->lib as $nameSpace => $src) {
            $filepath = $this->relplaceSlash(str_replace($nameSpace, $src, $class) . '.php');

            if ($this->requireFile($filepath)) {
                return $filepath;
            }
        }

        /**
         * Si le namespace n'est pas précisé en librairie, on parcoure les répertoires
         * pour chercher une correspondance avec l'arborescence.
         */
        foreach ($this->map as $map) {
            $filepath = $this->relplaceSlash($map . self::DS . $path . self::DS . $file);

            if ($this->requireFile($filepath)) {
                return $filepath;
            }
        }

        return false;
    }

    /**
     * Si le fichier existe alors l'appel et retourne true,
     * sinon retourne false.
     *
     * @param string $file Chemin d'un fichier.
     *
     * @return bool
     */
    protected function requireFile($file)
    {
        if (file_exists($file)) {
            require_once $file;

            return true;
        }

        return false;
    }

    /**
     * Remplace les doubles anti-slash par un simple slash.
     *
     * @param string $str Chaine à remplacer.
     *
     * @return string
     */
    protected function relplaceSlash($str)
    {
        return str_replace('\\', '/', $str);
    }
}
