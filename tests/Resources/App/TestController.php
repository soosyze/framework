<?php

namespace Soosyze\Tests\Resources\App;

use Psr\Http\Message\ResponseInterface;

class TestController extends \Soosyze\Controller
{
    public function index(): string
    {
        return 'ok';
    }

    public function getApi(): ResponseInterface
    {
        return $this->json(200, [ 'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5 ]);
    }
}
