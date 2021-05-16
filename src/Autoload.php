<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze;

/**
 * Permet de charger les fichiers en fonction de leur namespace.
 *
 * @see https://www.php-fig.org/psr/psr-4/ Suit les recommandations PSR-4.
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class Autoload
{
    private const DS = DIRECTORY_SEPARATOR;

    /**
     * Tableau avec comme clés un namespace et en valeur la racine de son arborescence.
     *
     * @var array
     */
    protected $lib = [];

    /**
     * Liste les répertoires à parcourir pour le chargement.
     *
     * @var string[]
     */
    protected $map = [];

    /**
     * Liste directement les fichiers à la racine de leur namespace.
     *
     * @var array
     */
    protected $prefix = [];

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
    public function setLib(array $lib): self
    {
        $this->lib = $lib;

        return $this;
    }

    /**
     * Ajoute une map à parcourir pour y trouver des classes.
     *
     * @param string[] $map
     *
     * @return $this
     */
    public function setMap(array $map): self
    {
        $this->map = $map;

        return $this;
    }

    /**
     * Ajoute une liste de prefix pour trouver des classes.
     *
     * @param array $prefix
     *
     * @return $this
     */
    public function setPrefix(array $prefix): self
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Appel l'autoload register.
     *
     * @return void
     */
    public function register(): void
    {
        spl_autoload_register(function ($class) {
            $this->loader($class);
        });
    }

    /**
     * Pour tous les fichiers de la librairie, on cherche le fichier requit.
     * Le nom de l'objet, le namespace, l'emplacement doit respecter les recommandations PSR-4.
     *
     * @see http://www.php-fig.org/psr/psr-4/
     *
     * @param string $class Nom de la classe appelée.
     *
     * @return string|null Nom de la classe appelée.
     */
    public function loader(string $class): ?string
    {
        /* On explose la classe par '\' */
        $parts = explode('\\', $class);

        /* On extrait le dernier element. */
        $className = array_pop($parts);

        /* On créé le chemin vers la classe */
        $path = implode('\\', $parts);
        $file = $className . '.php';

        /*
         * Si la classe recherchée est à la racine du namespace le chemin sera
         * égale à la clé. Cette condition peut éviter la boucle.
         */
        if (isset($this->prefix[ $path ])) {
            $filepath = $this->relplaceSlash(
                $this->prefix[ $path ] . self::DS . $file
            );

            if ($this->requireFile($filepath)) {
                return $filepath;
            }
        }

        /*
         * Recherche une correspondance entre le namespace fournit en librairie
         * et la classe instanciée.
         */
        foreach ($this->lib as $nameSpace => $src) {
            $filepath = $this->relplaceSlash(
                str_replace($nameSpace, $src, $class) . '.php'
            );

            if ($this->requireFile($filepath)) {
                return $filepath;
            }
        }

        /*
         * Si le namespace n'est pas précisé en librairie, on parcoure les répertoires
         * pour chercher une correspondance avec l'arborescence.
         */
        foreach ($this->map as $map) {
            $filepath = $this->relplaceSlash(
                $map . self::DS . $path . self::DS . $file
            );

            if ($this->requireFile($filepath)) {
                return $filepath;
            }
        }

        return null;
    }

    /**
     * Si le fichier existe alors l'appel et retourne TRUE,
     * sinon retourne FALSE.
     *
     * @param string $file Chemin d'un fichier.
     *
     * @return bool
     */
    private function requireFile(string $file): bool
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
    private function relplaceSlash($str): string
    {
        return str_replace('\\', self::DS, $str);
    }
}
