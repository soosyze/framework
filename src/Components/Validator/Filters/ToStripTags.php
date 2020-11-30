<?php

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
    /**
     * Filtre les balises autorisées dans une valeur.
     *
     * @param string $key   Identifiant de la valeur.
     * @param string $value Valeur à filtrer.
     * @param string $arg   Liste des balise HTML autorisés.
     *
     * @throws \InvalidArgumentException La valeur time n'est pas numérique.
     *
     * @return string
     */
    protected function clean(
        $key,
        $value,
        $arg = '<h1><h2><h3><h4><h5><h6><p><span><b><i><u><a><table><thead><tbody><tfoot><tr><th><td><ul><ol><li><dl><dt><dd><img><br><hr>'
    ) {
        if (!is_string($value)) {
            throw new \InvalidArgumentException(htmlspecialchars(
                "The value of the $key field is not a string"
            ));
        }

        return strip_tags($value, $arg);
    }
}
