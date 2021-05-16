<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Router\Exception;

/**
 * Exception levée lorsqu'un paramètre de la route ne remplit pas la condition.
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class RouteArgumentException extends \Exception
{
    /**
     * Construit l'exception à partir des données de la route.
     *
     * @param string          $param     Clé paramétrable de la route.
     * @param string          $condition Condition pour que la route soit valide (regex).
     * @param string          $path      L'URL appelée.
     * @param int             $code      Code de l'exception.
     * @param \Exception|null $previous  Exception précédente.
     */
    public function __construct(
        string $param,
        string $condition,
        string $path,
        int $code = 0,
        ?\Exception $previous = null
    ) {
        $msg = sprintf(
            'The parameter %s of the %s route does not fulfill the %s condition.',
            $param,
            $path,
            $condition
        );
        parent::__construct(htmlspecialchars($msg), $code, $previous);
    }
}
