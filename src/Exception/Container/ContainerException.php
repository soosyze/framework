<?php

/**
 * Soosyze Framework http://soosyze.com
 *
 * @package Soosyze\Exception\App
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Exception\Container;

use Psr\Container\ContainerExceptionInterface;

/**
 * Exception levée lorsqu'une erreur est survenue dans le container.
 */
class ContainerException extends \Exception implements ContainerExceptionInterface
{
}
