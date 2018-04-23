<?php

/**
 * Soosyze Framework http://soosyze.com
 * 
 * @package Soosyze\Exception\App
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Exception\Route;

/**
 * Exception levée lorsqu'un paramètre de la route ne remplit pas la condition.
 */
class RouteArgumentException extends \Exception
{

    /**
     * Construit l'exception à partir des données de la route.
     * 
     * @param string $param Clé paramétrable de la route.
     * @param string $condition Condition pour que la route soit valide (regex).
     * @param string $path L'URL appelée.
     * @param int $code Code de l'exception.
     * @param Exception $previous Exception précédente.
     */
    public function __construct( $param, $condition, $path, $code = 0,
        Exception $previous = null )
    {
        $msg = 'The parameter '
            . $param
            . ' of the '
            . $path
            . ' route does not fulfill the '
            . $condition
            . ' condition.';
        parent::__construct(htmlspecialchars($msg), $code, $previous);
    }
}