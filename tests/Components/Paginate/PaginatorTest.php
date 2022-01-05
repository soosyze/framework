<?php

namespace Soosyze\Tests\Components\Paginate;

use Soosyze\Components\Paginate\Paginator;

class PaginatorTest extends \PHPUnit\Framework\TestCase
{
    public function testPaginatorSimple(): void
    {
        $paginator = new Paginator(10, 5, 1, 'page/:id');
        $html      = '<ul class="pagination">' . PHP_EOL;
        $html      .= '<li><a href="page/2"> &raquo;</a></li>' . PHP_EOL;
        $html      .= '</ul>' . PHP_EOL;
        $this->assertEquals($html, $paginator);

        $paginator1 = new Paginator(10, 5, 2, 'page/:id');
        $html       = '<ul class="pagination">' . PHP_EOL;
        $html       .= '<li><a href="page/1">&laquo;</a></li>' . PHP_EOL;
        $html       .= '</ul>' . PHP_EOL;
        $this->assertEquals($html, $paginator1);

        $paginator2 = new Paginator(10, 0, 1, 'page/:id');
        $this->assertEquals('', $paginator2);
    }

    public function testPaginatorMultiple(): void
    {
        $paginator = new Paginator(20, 3, 1, 'page/:id');
        $html      = '<ul class="pagination">' . PHP_EOL;
        $html      .= '<li class="active"><span aria-current="page">1</span></li>' . PHP_EOL;
        $html      .= '<li class=""><a href="page/2">2</a></li>' . PHP_EOL;
        $html      .= '<li class=""><a href="page/3">3</a></li>' . PHP_EOL;
        $html      .= '<li class=""><a href="page/4">4</a></li>' . PHP_EOL;
        $html      .= '<li class=""><a href="page/5">5</a></li>' . PHP_EOL;
        $html      .= '<li><a href="page/2"> &raquo;</a></li>' . PHP_EOL;
        $html      .= '</ul>' . PHP_EOL;
        $this->assertEquals($html, $paginator);

        $paginator = new Paginator(20, 3, 4, 'page/:id');
        $html      = '<ul class="pagination">' . PHP_EOL;
        $html      .= '<li><a href="page/3">&laquo;</a></li>' . PHP_EOL;
        $html      .= '<li class=""><a href="page/2">2</a></li>' . PHP_EOL;
        $html      .= '<li class=""><a href="page/3">3</a></li>' . PHP_EOL;
        $html      .= '<li class="active"><span aria-current="page">4</span></li>' . PHP_EOL;
        $html      .= '<li class=""><a href="page/5">5</a></li>' . PHP_EOL;
        $html      .= '<li class=""><a href="page/6">6</a></li>' . PHP_EOL;
        $html      .= '<li><a href="page/5"> &raquo;</a></li>' . PHP_EOL;
        $html      .= '</ul>' . PHP_EOL;
        $this->assertEquals($html, $paginator);

        $paginator = new Paginator(20, 3, 7, 'page/:id');
        $html      = '<ul class="pagination">' . PHP_EOL;
        $html      .= '<li><a href="page/6">&laquo;</a></li>' . PHP_EOL;
        $html      .= '<li class=""><a href="page/3">3</a></li>' . PHP_EOL;
        $html      .= '<li class=""><a href="page/4">4</a></li>' . PHP_EOL;
        $html      .= '<li class=""><a href="page/5">5</a></li>' . PHP_EOL;
        $html      .= '<li class=""><a href="page/6">6</a></li>' . PHP_EOL;
        $html      .= '<li class="active"><span aria-current="page">7</span></li>' . PHP_EOL;
        $html      .= '</ul>' . PHP_EOL;
        $this->assertEquals($html, $paginator);

        $paginator = new Paginator(20, 6, 1, 'page/:id');
        $html      = '<ul class="pagination">' . PHP_EOL;
        $html      .= '<li class="active"><span aria-current="page">1</span></li>' . PHP_EOL;
        $html      .= '<li class=""><a href="page/2">2</a></li>' . PHP_EOL;
        $html      .= '<li class=""><a href="page/3">3</a></li>' . PHP_EOL;
        $html      .= '<li class=""><a href="page/4">4</a></li>' . PHP_EOL;
        $html      .= '<li><a href="page/2"> &raquo;</a></li>' . PHP_EOL;
        $html      .= '</ul>' . PHP_EOL;
        $this->assertEquals($html, $paginator);
    }

    public function testSetMaxPage(): void
    {
        $paginator = new Paginator(20, 3, 1, 'page/:id');

        $paginator->setMaxPage(3);
        $html = '<ul class="pagination">' . PHP_EOL;
        $html .= '<li class="active"><span aria-current="page">1</span></li>' . PHP_EOL;
        $html .= '<li class=""><a href="page/2">2</a></li>' . PHP_EOL;
        $html .= '<li class=""><a href="page/3">3</a></li>' . PHP_EOL;
        $html .= '<li><a href="page/2"> &raquo;</a></li>' . PHP_EOL;
        $html .= '</ul>' . PHP_EOL;
        $this->assertEquals($html, $paginator);
    }

    /**
     * @dataProvider providerSetMaxPageException
     *
     * @param class-string<\Throwable> $exceptionClass
     */
    public function testSetMaxPageException(
        int $max,
        string $exceptionClass,
        string $exceptionMessage
    ): void {
        $paginator = new Paginator(20, 3, 4, 'page/:id');

        $this->expectException($exceptionClass);
        $this->expectExceptionMessage($exceptionMessage);
        $paginator->setMaxPage($max);
    }

    public function providerSetMaxPageException(): \Generator
    {
        yield [
            2,
            \InvalidArgumentException::class,
            'The number of pages to display must be greater than or equal to three.'
        ];
    }

    public function testSetKey(): void
    {
        $paginator = new Paginator(20, 3, 1, 'page/:num');

        $paginator->setKey(':num');
        $html = '<ul class="pagination">' . PHP_EOL;
        $html .= '<li class="active"><span aria-current="page">1</span></li>' . PHP_EOL;
        $html .= '<li class=""><a href="page/2">2</a></li>' . PHP_EOL;
        $html .= '<li class=""><a href="page/3">3</a></li>' . PHP_EOL;
        $html .= '<li class=""><a href="page/4">4</a></li>' . PHP_EOL;
        $html .= '<li class=""><a href="page/5">5</a></li>' . PHP_EOL;
        $html .= '<li><a href="page/2"> &raquo;</a></li>' . PHP_EOL;
        $html .= '</ul>' . PHP_EOL;
        $this->assertEquals($html, $paginator);
    }
}
