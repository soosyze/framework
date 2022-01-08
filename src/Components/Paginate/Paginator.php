<?php

declare(strict_types=1);

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
     * @var string
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
    public function __construct(
        int $count,
        int $limit,
        int $current,
        string $link
    ) {
        $this->count  = $count;
        $this->limit  = $limit;
        $this->setCurrent($current);
        $this->link   = $link;
        $this->nbPage = $limit > 0
            ? (int) ceil($count / $limit)
            : 0;
    }

    /**
     * Génère le code HTML de la pagination.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->render();
    }

    /**
     * Change la page courante.
     *
     * @param int $current
     *
     * @throws \InvalidArgumentException La page courante doit être
     *                                   un nombre numérique supérieur à 1.
     *
     * @return $this
     */
    public function setCurrent(int $current): self
    {
        if ($current < 1) {
            throw new \InvalidArgumentException('The current page must be a numeric number greater than 1.');
        }
        $this->current = $current;

        return $this;
    }

    /**
     * Change le nombre de page maximum à afficher.
     *
     * @param int $max
     *
     * @throws \InvalidArgumentException Le nombre de page à afficher doit être
     *                                   supérieur ou égale à trois.
     *
     * @return $this
     */
    public function setMaxPage(int $max = 3): self
    {
        if ($max < 3) {
            throw new \InvalidArgumentException('The number of pages to display must be greater than or equal to three.');
        }
        $this->nbMaxPage = $max;

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
    public function setKey(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Génère le code HTML de la pagination.
     *
     * @return string
     */
    public function render(): string
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

        return $out !== ''
            ? '<ul class="pagination">' . PHP_EOL . $out . '</ul>' . PHP_EOL
            : '';
    }

    /**
     * Retourne la liste des pages.
     *
     * @return array
     */
    public function getPages(): array
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
    public function getNextPage(): ?int
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
    public function getPreviousPage(): ?int
    {
        return $this->current > 1
            ? $this->current - 1
            : null;
    }

    /**
     * Retourne l'url de la page suivante ou null si elle n'existe pas.
     *
     * @return string|null
     */
    public function getNextUrl(): ?string
    {
        return $this->getUrl($this->getNextPage());
    }

    /**
     * Retourne l'url de la page précédente ou null si elle n'existe pas.
     *
     * @return string|null
     */
    public function getPreviousUrl(): ?string
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
    protected function getPage(int $key): array
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
    protected function getUrl(?int $key): ?string
    {
        return $key === null
            ? null
            : str_replace($this->key, (string) $key, $this->link);
    }
}
