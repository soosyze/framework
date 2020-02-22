<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Exception\Container;

use Psr\Container\ContainerExceptionInterface;

/**
 * Exception levée lorsqu'une erreur est survenue dans le container.
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class ContainerException extends \Exception implements ContainerExceptionInterface
{
}
