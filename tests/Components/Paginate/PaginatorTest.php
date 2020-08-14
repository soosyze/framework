<?php

namespace Soosyze\Tests\Components\Paginate;

use Soosyze\Components\Paginate\Paginator;

class PaginatorTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
    }

    public function testPaginatorSimple()
    {
        $paginator = new Paginator(10, 5, 1, 'page/:id');
        $html      = '<ul class="pagination">' . PHP_EOL;
        $html      .= '<li><a href="page/2"> &raquo;</a></li>' . PHP_EOL;
        $html      .= '</ul>' . PHP_EOL;
        $this->assertEquals($paginator, $html);

        $paginator1 = new Paginator(10, 5, 2, 'page/:id');
        $html      = '<ul class="pagination">' . PHP_EOL;
        $html      .= '<li><a href="page/1">&laquo;</a></li>' . PHP_EOL;
        $html      .= '</ul>' . PHP_EOL;
        $this->assertEquals($paginator1, $html);
        
        $paginator2 = new Paginator(10, 0, 1, 'page/:id');
        $this->assertEquals($paginator2, '');
    }

    public function testPaginatorMultiple()
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
        $this->assertEquals($paginator, $html);

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
        $this->assertEquals($paginator, $html);

        $paginator = new Paginator(20, 3, 7, 'page/:id');
        $html      = '<ul class="pagination">' . PHP_EOL;
        $html      .= '<li><a href="page/6">&laquo;</a></li>' . PHP_EOL;
        $html      .= '<li class=""><a href="page/3">3</a></li>' . PHP_EOL;
        $html      .= '<li class=""><a href="page/4">4</a></li>' . PHP_EOL;
        $html      .= '<li class=""><a href="page/5">5</a></li>' . PHP_EOL;
        $html      .= '<li class=""><a href="page/6">6</a></li>' . PHP_EOL;
        $html      .= '<li class="active"><span aria-current="page">7</span></li>' . PHP_EOL;
        $html      .= '</ul>' . PHP_EOL;
        $this->assertEquals($paginator, $html);

        $paginator = new Paginator(20, 6, 1, 'page/:id');
        $html      = '<ul class="pagination">' . PHP_EOL;
        $html      .= '<li class="active"><span aria-current="page">1</span></li>' . PHP_EOL;
        $html      .= '<li class=""><a href="page/2">2</a></li>' . PHP_EOL;
        $html      .= '<li class=""><a href="page/3">3</a></li>' . PHP_EOL;
        $html      .= '<li class=""><a href="page/4">4</a></li>' . PHP_EOL;
        $html      .= '<li><a href="page/2"> &raquo;</a></li>' . PHP_EOL;
        $html      .= '</ul>' . PHP_EOL;
        $this->assertEquals($paginator, $html);
    }

    /**
     * @expectedException \Exception
     */
    public function testCurrentException()
    {
        new Paginator(20, 6, 'error', 'page/:id');
    }

    public function testSetMaxPage()
    {
        $paginator = new Paginator(20, 3, 1, 'page/:id');

        $paginator->setMaxPage(3);
        $html = '<ul class="pagination">' . PHP_EOL;
        $html .= '<li class="active"><span aria-current="page">1</span></li>' . PHP_EOL;
        $html .= '<li class=""><a href="page/2">2</a></li>' . PHP_EOL;
        $html .= '<li class=""><a href="page/3">3</a></li>' . PHP_EOL;
        $html .= '<li><a href="page/2"> &raquo;</a></li>' . PHP_EOL;
        $html .= '</ul>' . PHP_EOL;
        $this->assertEquals($paginator, $html);
    }

    /**
     * @expectedException \Exception
     */
    public function testSetMaxPageException()
    {
        $paginator = new Paginator(20, 3, 4, 'page/:id');
        $paginator->setMaxPage('error');
    }

    public function testSetKey()
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
        $this->assertEquals($paginator, $html);
    }

    /**
     * @expectedException \Exception
     */
    public function testSetKeyException()
    {
        $paginator = new Paginator(20, 3, 4, 'page/:id');
        $paginator->setKey(1);
    }
}
