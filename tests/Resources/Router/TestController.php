<?php

namespace Soosyze\Tests\Resources\Router;

use Psr\Http\Message\ServerRequestInterface;

class TestController
{
    public function index(): string
    {
        return 'hello world !';
    }

    public function format(int $id, string $ext): string
    {
        return sprintf('page %d, format %s', $id, $ext);
    }

    /**
     * @param string $ext
     */
    public function optionalFormat(int $id, $ext = 'csv'): string
    {
        return sprintf('page %d, format %s', $id, $ext);
    }

    public function page(int $id = 1): string
    {
        return sprintf('page %d', $id);
    }

    public function request(int $id, int $idRequest, ServerRequestInterface $serverRequest): string
    {
        return sprintf(
            'page %d, request %d to method %s',
            $id,
            $idRequest,
            $serverRequest->getMethod()
        );
    }
}
