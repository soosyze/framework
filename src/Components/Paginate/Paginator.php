<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Paginate;

/**
 * Construit et génère une pagination.
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class Paginator
{
    /**
     * Identifiant incrémental contenu dans le lien.
     *
     * @var int
     */
    public $key = ':id';

    /**
     * Nombre d'item.
     *
     * @var int
     */
    protected $count;

    /**
     * Nombre d'item à afficher au maximum.
     *
     * @var int
     */
    protected $limit;

    /**
     * Numéro de l'item courant.
     *
     * @var int
     */
    protected $current;

    /**
     * Lien à incrémenter.
     *
     * @var string
     */
    protected $link;

    /**
     * Nombre de page total.
     *
     * @var int
     */
    protected $nbPage = 0;

    /**
     * Nombre maximum de page à afficher.
     *
     * @var int
     */
    protected $nbMaxPage = 5;

    /**
     * Construit une pagniation.
     *
     * @param int    $count   Nombre d'item.
     * @param int    $limit   Nombre d'item à afficher au maximum.
     * @param int    $current Numéro de l'item courant.
     * @param string $link    Lien à incrémenter.
     */
    public function __construct($count, $limit, $current, $link)
    {
        $this->count  = $count;
        $this->limit  = $limit;
        $this->setCurrent($current);
        $this->link   = $link;
        $this->nbPage = $limit != 0
            ? (int) ceil($count / $limit)
            : 0;
    }

    /**
     * Génère le code HTML de la pagination.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * Change la page courante.
     *
     * @param numeric $current
     *
     * @throws \InvalidArgumentException La page courante doit être
     *                                   un nombre numérique supérieur à 1.
     *
     * @return $this
     */
    public function setCurrent($current)
    {
        if (!is_numeric($current) || $current < 1) {
            throw new \InvalidArgumentException('The current page must be a numeric number greater than 1.');
        }
        $this->current = (int) $current + 0;

        return $this;
    }

    /**
     * Change le nombre de page maximum à afficher.
     *
     * @param numeric $max
     *
     * @throws \InvalidArgumentException Le nombre de page à afficher doit être
     *                                   supérieur ou égale à trois.
     *
     * @return $this
     */
    public function setMaxPage($max = 3)
    {
        if (!is_numeric($max) || $max < 3) {
            throw new \InvalidArgumentException('The number of pages to display must be greater than or equal to three.');
        }
        $this->nbMaxPage = (int) $max + 0;

        return $this;
    }

    /**
     * Change la clé du lien.
     *
     * @param string $key
     *
     * @throws \InvalidArgumentException La clé du lien doit être
     *                                   une chaine de caractère non null.
     *
     * @return $this
     */
    public function setKey($key)
    {
        if (!\is_string($key) || empty($key)) {
            throw new \InvalidArgumentException('The link key must be a non-null string.');
        }
        $this->key = $key;

        return $this;
    }

    /**
     * Génère le code HTML de la pagination.
     *
     * @return string
     */
    public function render()
    {
        $out = '';
        if ($url = $this->getPreviousUrl()) {
            $out .= "<li><a href=\"{$url}\">&laquo;</a></li>" . PHP_EOL;
        }
        foreach ($this->getPages() as $page) {
            $out .= "<li class=\"{$page[ 'current' ]}\">";
            $out .= $page[ 'link' ] === null
                ? "<span aria-current=\"page\">{$page[ 'title' ]}</span>"
                : "<a href=\"{$page[ 'link' ]}\">{$page[ 'title' ]}</a>";
            $out .= '</li>' . PHP_EOL;
        }
        if ($url = $this->getNextUrl()) {
            $out .= "<li><a href=\"{$url}\"> &raquo;</a></li>" . PHP_EOL;
        }

        return $out
            ? '<ul class="pagination">' . PHP_EOL . $out . '</ul>' . PHP_EOL
            : '';
    }

    /**
     * Retourne la liste des pages.
     *
     * @return array
     */
    public function getPages()
    {
        $pages = [];
        if ($this->nbPage <= 2) {
            return [];
        }
        if ($this->nbPage <= $this->nbMaxPage) {
            for ($i = 1; $i <= $this->nbPage; ++$i) {
                $pages[] = $this->getPage($i);
            }

            return $pages;
        }

        /* Positionne la page courante au milieu de la pagination. */
        $middle = (int) ceil($this->nbMaxPage / 2);

        /* Calcule pour le début de la liste. */
        $start = ($this->current - $this->nbMaxPage + $middle) > 1
            ? $this->current - $this->nbMaxPage + $middle
            : 1;

        /* Calcule pour la fin de la liste. */
        $start = $start > $this->nbPage - $this->nbMaxPage
            ? $this->nbPage - $this->nbMaxPage + 1
            : $start;

        $end = $start + $this->nbMaxPage <= $this->nbPage
            ? $start + $this->nbMaxPage - 1
            : $this->nbPage;

        for ($i = $start; $i <= $end; ++$i) {
            $pages[] = $this->getPage($i);
        }

        return $pages;
    }

    /**
     * Retourne le numéro de la page suivante ou null si elle n'existe pas.
     *
     * @return int|null
     */
    public function getNextPage()
    {
        return $this->current < $this->nbPage
            ? $this->current + 1
            : null;
    }

    /**
     * Retourne le numéro de la page précédente ou null si elle n'existe pas.
     *
     * @return int|null
     */
    public function getPreviousPage()
    {
        return $this->current > 1
            ? $this->current - 1
            : null;
    }

    /**
     * Retourne l'url de la page suivante ou null si elle n'existe pas.
     *
     * @return array|null
     */
    public function getNextUrl()
    {
        return $this->getUrl($this->getNextPage());
    }

    /**
     * Retourne l'url de la page précédente ou null si elle n'existe pas.
     *
     * @return array|null
     */
    public function getPreviousUrl()
    {
        return $this->getUrl($this->getPreviousPage());
    }

    /**
     * Retourne les données d'une page à partir de sa clé.
     *
     * @param int $key Numéro de la page.
     *
     * @return array
     */
    protected function getPage($key)
    {
        return [
            'title'   => $key,
            'link'    => $key === $this->current
            ? null
            : $this->getUrl($key),
            'current' => $key === $this->current
            ? 'active'
            : ''
        ];
    }

    /**
     * Retourne l'url d'une page à partir de sa clé ou null.
     *
     * @param int|null $key Numéro de la page.
     *
     * @return string|null
     */
    protected function getUrl($key)
    {
        return $key === null
            ? null
            : str_replace($this->key, $key, $this->link);
    }
}
