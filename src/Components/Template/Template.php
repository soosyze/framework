<?php

/**
 * Soosyze Framework http://soosyze.com
 *
 * @package Soosyze\Components\Template
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Template;

/**
 * Générer l'affichage d'une application web à partir de fichier PHP.
 *
 * @author Mathieu NOËL
 */
class Template
{
    /**
     * Le nom de la template.
     *
     * @var string
     */
    protected $name;

    /**
     * Chemin de la template.
     *
     * @var string
     */
    protected $path;

    /**
     * Les sous templates.
     *
     * @var array
     */
    protected $blocks = [];

    /**
     * Les variables.
     *
     * @var array
     */
    protected $vars = [];

    /**
     * Les fonctions de filtre.
     *
     * @var array
     */
    protected $filters = [];

    /**
     * Charge une template à partir de son nom et son chemin.
     *
     * @param string $name Nom du fichier.
     * @param string $path Chemin du fichier.
     */
    public function __construct($name, $path)
    {
        $this->name = $name;
        $this->path = $path;
    }

    /**
     * Retourne le rendu de la template.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Ajoute une fonction pour filtrer une variable.
     *
     * @param string   $key      Nom de la variable.
     * @param callable $function Fonction de filtre.
     *
     * @return $this
     */
    public function addFilterVar($key, callable $function)
    {
        return $this->addfilter('var.' . $key, $function);
    }

    /**
     * Ajoute une fonction pour filtrer un block.
     *
     * @param string   $key      Nom du block.
     * @param callable $function Fonction de filtre.
     *
     * @return $this
     */
    public function addFilterBlock($key, callable $function)
    {
        return $this->addfilter('block.' . $key, $function);
    }

    /**
     * Ajoute une fonction pour filtrer la sortie de la template.
     *
     * @param callable $function Fonction de filtre.
     *
     * @return $this
     */
    public function addFilterOutput(callable $function)
    {
        return $this->addfilter('output', $function);
    }

    /**
     * Ajoute une variable pour la template.
     *
     * @param string $key Clé unique de la variable.
     * @param mixed  $var Valeur de la variable.
     *
     * @return $this
     */
    public function addVar($key, $var)
    {
        $this->vars[ $key ] = $var;

        return $this;
    }

    /**
     * Ajoute des variables pour la template.
     *
     * @param array $vars Tableau associatif de variables.
     *
     * @return $this
     */
    public function addVars(array $vars)
    {
        foreach ($vars as $key => $var) {
            $this->addVar($key, $var);
        }

        return $this;
    }

    /**
     * Ajoute un bloc sous template avec la variable id_block par défaut.
     *
     * @param string   $key Clé unique du bloc.
     * @param Template $tpl Sous template.
     *
     * @return $this
     */
    public function addBlock($key, Template $tpl = null)
    {
        $this->blocks[ $key ] = $tpl !== null
            ? $tpl->addVar('id_block', "block-$key")
            : null;

        return $this;
    }

    /**
     * Retourne le contenu d'une variable à partir de son nom.
     *
     * @codeCoverageIgnore getter
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getVar($key)
    {
        return $this->vars[ $key ];
    }

    /**
     * Retourne toutes les variables de la template.
     *
     * @codeCoverageIgnore getter
     *
     * @return array
     */
    public function getVars()
    {
        return $this->vars;
    }

    /**
     * Retourne un bloc de la template à partir de sa clé.
     *
     * @param string $key Nom de la template recherchée
     *
     * @throws \Exception Le bloc n'existe pas.
     * @return Template
     */
    public function getBlock($key)
    {
        if (($find = $this->searchBlock($key)) !== null) {
            return $find;
        }

        throw new \Exception(htmlspecialchars("The block $key does not exist."));
    }

    /**
     * Retourne tous les blocs de la template.
     *
     * @codeCoverageIgnore getter
     *
     * @return Template[]
     */
    public function getBlocks()
    {
        return $this->blocks;
    }

    /**
     * Retourne le nom de la template.
     *
     * @codeCoverageIgnore getter
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Retourne le chemin de la template.
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
     * Change le nom de la template.
     *
     * @codeCoverageIgnore setter
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Compile la template, ses sous templates et ses variables.
     *
     * @return string La template compilée.
     */
    public function render()
    {
        require_once 'functions_include.php';
        $block = [];
        foreach ($this->blocks as $key => &$subTpl) {
            $block[ $key ] = !is_null($subTpl)
                ? $this->filter('block.' . $key, $subTpl->render())
                : '';
        }

        foreach ($this->vars as $key => $value) {
            $$key = $this->filter('var.' . $key, $value);
        }

        ob_start();
        require $this->path . $this->name;
        $html = ob_get_clean();

        return $this->filter('output', $html);
    }

    /**
     * Ajoute une fonction de filtre pour le rendu de la template.
     *
     * @param string   $key      Description
     * @param callable $function
     *
     * @return $this
     */
    protected function addFilter($key, callable $function)
    {
        $this->filters[ $key ][] = $function;

        return $this;
    }

    /**
     * Recherche récursive d'un bloc de la template à partir de sa clé.
     *
     * @param string $key Clé unique.
     *
     * @return Template|null
     */
    private function searchBlock($key)
    {
        if (!empty($this->blocks[ $key ])) {
            return $this->blocks[ $key ];
        }

        foreach ($this->blocks as $block) {
            if (($find = $block->searchBlock($key)) !== null) {
                return $find;
            }
        }

        return null;
    }

    /**
     * Exécute les fonctions de filtre.
     *
     * @param string $key   Nom du filtre.
     * @param string $value Valeur à filtrer.
     *
     * @return string
     */
    private function filter($key, $value)
    {
        if (isset($this->filters[ $key ])) {
            foreach ($this->filters[ $key ] as $filter) {
                $value = $filter($value);
            }
        }

        return $value;
    }
}
