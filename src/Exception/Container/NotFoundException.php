<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @package Soosyze\Exception\App
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Exception\Container;

use Psr\Container\NotFoundExceptionInterface;

/**
 * Exception levée lorsqu'un service appelé n'existe pas.
 */
class NotFoundException extends \Exception implements NotFoundExceptionInterface
{
}
