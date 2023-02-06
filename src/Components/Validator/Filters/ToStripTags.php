<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator\Filters;

/**
 * {@inheritdoc}
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class ToStripTags extends \Soosyze\Components\Validator\Filter
{
    private const ALLOWABLE_TAGS = '<h1><h2><h3><h4><h5><h6><p><span><b><i><u><a><table><thead><tbody><tfoot><tr><th><td><ul><ol><li><dl><dt><dd><img><br><hr>';

    /**
     * Filtre les balises autorisées dans une valeur.
     *
     * @param string $key   Identifiant de la valeur.
     * @param mixed  $value Valeur à filtrer.
     * @param mixed  $args  Liste des balise HTML autorisés.
     *
     * @throws \InvalidArgumentException The type must be validated before being filtered.
     */
    protected function clean(string $key, $value, $args = self::ALLOWABLE_TAGS): string
    {
        if (!is_string($value)) {
            throw new \InvalidArgumentException('The type must be validated before being filtered.');
        }
        if (!is_string($args)) {
            throw new \InvalidArgumentException(
                sprintf('The argument must be of type string: %s given', gettype($args))
            );
        }

        return strip_tags($value, $args);
    }
}
