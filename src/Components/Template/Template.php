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
    protected $template;

    /**
     * Chemin de la template.
     * 
     * @var string
     */
    protected $templatePath;

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
     * @param string $tplName Nom du fichier.
     * @param string $tplPath Chemin du fichier.
     */
    public function __construct( $tplName, $tplPath )
    {
        $this->template     = $tplName;
        $this->templatePath = $tplPath;
    }

    /**
     * Ajoute une variable pour la template.
     *
     * @param string $key Clé unique de la variable.
     * @param mixed $var Valeur de la variable.
     * 
     * @return $this
     */
    public function addVar( $key, $var )
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
    public function addVars( array $vars )
    {
        foreach( $vars as $key => $var )
        {
            $this->addVar($key, $var);
        }
        return $this;
    }

    /**
     * Ajoute un bloc sous template.
     *
     * @param string $key Clé unique du bloc.
     * @param Template $tpl Sous template.
     *
     * @return $this
     */
    public function addBlock( $key, Template $tpl = null )
    {
        $this->blocks[ $key ] = $tpl;
        return $this;
    }

    /**
     * Retourne un bloc de la template à partir de sa clé.
     *
     * @param string $key nom de la template recherchée
     *
     * @return Template
     * 
     * @throws \Exception Le bloc n'existe pas.
     */
    public function getBlock( $key )
    {
        if( ($find = $this->searchBlock($key)) !== null )
        {
            return $find;
        }

        throw new \Exception('The block ' . htmlspecialchars($key) . ' does not exist.');
    }

    /**
     * Recherche récursive d'un bloc de la template à partir de sa clé.
     * 
     * @param string $key Clé unique.
     * 
     * @return Template|null
     */
    private function searchBlock( $key )
    {
        if( !empty($this->blocks[ $key ]) )
        {
            return $this->blocks[ $key ];
        }

        foreach( $this->blocks as $block )
        {
            if( ($find = $block->searchBlock($key)) !== null )
            {
                return $find;
            }
        }
        return null;
    }

    /**
     * Ajoute une fonction de filtre pour le rendu de la template.
     *
     * @param callable $function
     *
     * @return $this
     */
    public function addfilter( callable $function )
    {
        $this->filters[] = $function;
        return $this;
    }

    /**
     * Compile la template, ses sous templates et ses variables.
     *
     * @return string La template compilée.
     */
    public function render()
    {
        $block = [];
        foreach( $this->blocks as $key => &$subTpl )
        {
            $block[ $key ] = !is_null($subTpl)
                ? $subTpl->render()
                : '';
        }

        foreach( $this->vars as $key => $value )
        {
            $$key = $value;
        }

        ob_start();
        require( $this->templatePath . $this->template );
        $html = ob_get_clean();

        foreach( $this->filters as $filter )
        {
            $html = $filter($html);
        }

        return $html;
    }

    /**
     * Retourne le nom de la template.
     * 
     * @return string
     */
    public function getName()
    {
        return $this->template;
    }

    /**
     * Retourne le chemin de la template.
     * 
     * @return string
     */
    public function getPath()
    {
        return $this->templatePath;
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
}