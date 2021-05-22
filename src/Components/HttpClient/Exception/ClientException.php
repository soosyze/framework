<?php

declare(strict_types=1);

namespace Soosyze\Components\HttpClient\Exception;

use Psr\Http\Client\ClientExceptionInterface;

class ClientException extends \Exception implements ClientExceptionInterface
{
}
