<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Exception\Container;

use Psr\Container\NotFoundExceptionInterface;

/**
 * Exception levée lorsqu'un service appelé n'existe pas.
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class NotFoundException extends \Exception implements NotFoundExceptionInterface
{
}
