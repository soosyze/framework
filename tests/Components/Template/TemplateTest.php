<?php

namespace Soosyze\Tests\Components\Template;

use Soosyze\Components\Template\Template;

/**
 * @coversDefaultClass \Soosyze\Components\Template\Template
 */
class TemplateTest extends \PHPUnit\Framework\TestCase
{
    /**
     *
     * @var Template
     */
    protected $object;

    protected $pathTemplate = __DIR__ . DIRECTORY_SEPARATOR;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Template('testTemplate.php', $this->pathTemplate);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    public function testAddVar()
    {
        $this->object->addVar('attr', 'test');
        $this->assertAttributeSame([ 'attr' => 'test' ], 'vars', $this->object);
    }

    public function testAddVars()
    {
        $this->object->addVars([ 'attr', 'test', 'attr2' => 'test2' ]);
        $this->assertAttributeSame([ 'attr', 'test', 'attr2' => 'test2' ], 'vars', $this->object);
    }

    public function testAddBlock()
    {
        $block = new Template('testBlock', $this->pathTemplate);
        $this->object->addBlock('test', $block);
        $this->assertAttributeSame([ 'test' => $block ], 'blocks', $this->object);
    }

    public function testGetBlock()
    {
        $block = new Template('testBlock', $this->pathTemplate);
        $this->object->addBlock('test', $block);

        $this->assertEquals($block, $this->object->getBlock('test'));
    }

    /**
     * @expectedException Exception
     */
    public function testGetBlockException()
    {
        $this->object->getBlock('error');
    }

    public function testGetBlockMulti()
    {
        $block1 = new Template('testBlock1', $this->pathTemplate);
        $block2 = new Template('testBlock2', $this->pathTemplate);

        $block1->addBlock('testBlock2', $block2);
        $this->object->addBlock('testBlock1', $block1);

        $this->assertEquals($block2, $this->object->getBlock('testBlock2'));
    }

    public function testAddFilter()
    {
        $function = function($html)
        {
            return strtolower($html);
        };
        $this->object->addfilter($function);
        $this->assertAttributeSame([ $function ], 'filters', $this->object);
    }

    public function testRenderAttr()
    {
        $this->object->addVar('attr', 'test');
        $this->assertEquals('test', $this->object->render());
    }

    public function testRenderBlock()
    {
        $this->object->addVar('attr', 'Test')
            ->addBlock('page', new Template('testBlock.php', $this->pathTemplate))
            ->addBlock('title');

        $this->assertEquals('Test    Hello world !', $this->object->render());
    }

    public function testFilter()
    {
        $this->object->addVar('attr', 'TEST')
            ->addfilter(function($html)
            {
                return strtolower($html);
            });
        $this->assertEquals('test', $this->object->render());
    }

    public function testGetName()
    {
        $this->assertEquals('testTemplate.php', $this->object->getName());
    }

    public function testGetPath()
    {
        $this->assertEquals($this->pathTemplate, $this->object->getPath());
    }

    public function testToString()
    {
        $this->object->addVar('attr', 'test');
        $this->assertEquals('test', ( string ) $this->object);
    }
}