<?php

namespace Soosyze\Tests\Components\Template;

use Soosyze\Components\Template\Template;

class TemplateTest extends \PHPUnit\Framework\TestCase
{
    private const PATH_TEMLPATE = __DIR__ . '/../../Resources/Template/';

    /**
     * @var Template
     */
    protected $object;

    protected function setUp(): void
    {
        $this->object = new Template('testTemplate.php', self::PATH_TEMLPATE);
    }

    public function testGetBlock(): void
    {
        $block = new Template('testBlock', self::PATH_TEMLPATE);
        $this->object->addBlock('test', $block);

        $this->assertEquals($block, $this->object->getBlock('test'));
    }

    public function testGetBlockException(): void
    {
        $this->expectException(\Exception::class);
        $this->object->getBlock('error');
    }

    public function testGetBlockMulti(): void
    {
        $block1 = new Template('testBlock1', self::PATH_TEMLPATE);
        $block2 = new Template('testBlock2', self::PATH_TEMLPATE);

        $block1->addBlock('testBlock2', $block2);
        $this->object->addBlock('testBlock1', $block1);

        $this->assertEquals($block2, $this->object->getBlock('testBlock2'));
    }

    public function testAddFilterVar(): void
    {
        $function = function ($html): string {
            return strtolower($html);
        };

        $this->object->addVar('attr', 'TEST')->addFilterVar('attr', $function);

        $this->assertEquals('test', $this->object->render());
    }

    public function testRenderAttr(): void
    {
        $this->object->addVar('attr', 'test');
        $this->assertEquals('test', $this->object->render());
    }

    public function testRenderBlock(): void
    {
        $this->object->addVar('attr', 'Test')
            ->addBlock('page', new Template('testBlock.php', self::PATH_TEMLPATE))
            ->addBlock('title');

        $this->assertEquals('TestHello world !', $this->object->render());
    }

    public function testAddFilterBlock(): void
    {
        $function = function ($html): string {
            return strtolower($html);
        };

        $this->object->addVar('attr', 'Test')
            ->addBlock('page', new Template('testBlock.php', self::PATH_TEMLPATE))
            ->addFilterBlock('page', $function);

        $this->assertEquals('Testhello world !', $this->object->render());
    }

    public function testAddFilterOutput(): void
    {
        $function = function ($html): string {
            return strtolower($html);
        };

        $this->object->addVar('attr', 'Test')
            ->addBlock('page', new Template('testBlock.php', self::PATH_TEMLPATE))
            ->addFilterOutput($function);

        $this->assertEquals('testhello world !', $this->object->render());
    }

    public function testToString(): void
    {
        $this->object->addVar('attr', 'test');
        $this->assertEquals('test', (string) $this->object);
    }

    public function testOverride(): void
    {
        /* Override PATH */
        $page = new Template('testBlock.php', self::PATH_TEMLPATE);
        $page->addPathOverride(self::PATH_TEMLPATE . 'theme/');
        $this->object->addVar('attr', 'Test')
            ->addBlock('page', $page)
            ->addBlock('title');

        $this->assertEquals('TestHello world PathOverride !', $this->object->render());

        /* Override NAME */
        $page = new Template('testBlock.php', self::PATH_TEMLPATE);
        $page->addNameOverride('testBlock_1.php');
        $this->object->addVar('attr', 'Test')
            ->addBlock('page', $page)
            ->addBlock('title');

        $this->assertEquals('TestHello world NameOverride !', $this->object->render());

        /* Override PATH & NAME */
        $page = new Template('testBlock.php', self::PATH_TEMLPATE);
        $page->addPathOverride(self::PATH_TEMLPATE . 'theme/')
            ->addNameOverride('testBlock_1.php');
        $this->object->addVar('attr', 'Test')
            ->addBlock('page', $page)
            ->addBlock('title');

        $this->assertEquals('TestHello world NameOverride & PathOverride !', $this->object->render());
    }
}
