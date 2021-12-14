<?php

namespace Soosyze\Tests\Components\Form;

use Soosyze\Components\Form\FormBuilder;

class FormTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FormBuilder
     */
    protected $object;

    protected function setUp(): void
    {
        @session_start([
                'cookie_httponly' => true,
                'cookie_secure'   => true
        ]);
        parent::setUp();
        $this->object = new FormBuilder([ 'method' => 'post', 'action' => 'http://localhost/' ]);
    }

    /**
     * @dataProvider providerInput
     */
    public function testInput(string $type, array $attr, string $expectedHtml): void
    {
        $this->object->$type("input$type", $attr);

        $form = $this->object->form_input("input$type");

        $this->assertEquals($expectedHtml, $form);
    }

    /**
     * @dataProvider providerInput
     */
    public function testInputBasic(
        string $type,
        array $attr,
        string $expectedHtml
    ): void {
        $this->object->inputBasic($type, "input$type", $attr);

        $form = $this->object->form_input("input$type");

        $this->assertEquals($expectedHtml, $form);
    }

    public function providerInput(): \Generator
    {
        yield [
            'text',
            [ 'required' => 'required', 'value' => 'lorem ipsum' ],
            '<input name="inputtext" type="text" required value="lorem ipsum" id="inputtext">' . PHP_EOL
        ];
        yield [
            'password',
            [ 'required' => 'required', 'value' => 'lorem ipsum' ],
            '<input name="inputpassword" type="password" required value="lorem ipsum" id="inputpassword">' . PHP_EOL
        ];
        yield [
            'email',
            [ 'required' => 'required', 'value' => 'lorem ipsum' ],
            '<input name="inputemail" type="email" required value="lorem ipsum" id="inputemail">' . PHP_EOL
        ];
        yield [
            'checkbox',
            [],
            '<input name="inputcheckbox" type="checkbox" id="inputcheckbox">' . PHP_EOL
        ];
        yield [
            'radio',
            [],
            '<input name="inputradio" type="radio" id="inputradio">' . PHP_EOL
        ];
    }

    public function testInputNumber()
    {
        $this->object->number('inputNumber', [ ':actions' => 1 ]);

        $this->assertEquals(
            '<input name="inputNumber" type="number" id="inputNumber">' . PHP_EOL,
            $this->object->form_input('inputNumber')
        );
        $this->assertEquals(
            '<button class="btn input-number-decrement" data-target="#inputNumber" type="button" id="inputNumber-decrement">'
            . '<i class="fa fa-minus" aria-hidden="true"></i>'
            . '</button>' . PHP_EOL,
            $this->object->form_html('inputNumber-decrement')
        );
        $this->assertEquals(
            '<button class="btn input-number-increment" data-target="#inputNumber" type="button" id="inputNumber-increment">'
            . '<i class="fa fa-plus" aria-hidden="true"></i>'
            . '</button>' . PHP_EOL,
            $this->object->form_html('inputNumber-increment')
        );
    }

    public function testInputSubmit(): void
    {
        $this->object->submit('inputSubmit', 'Enregistrer');

        $this->assertEquals(
            '<input name="inputSubmit" type="submit" id="inputSubmit" value="Enregistrer">' . PHP_EOL,
            $this->object->form_input('inputSubmit')
        );
    }

    public function testInputSelect(): void
    {
        $options = [
            [ 'label' => 'hello', 'value' => 0, 'attr' => [ 'data-link' => 'https://soosyze.com' ] ],
            [ 'label' => 'world', 'value' => 1 ],
        ];
        $this->object->select('inputSelect', $options, [ ':selected' => 0 ]);

        $this->assertEquals(
            '<select name="inputSelect" id="inputSelect">' . PHP_EOL
            . '<option value="0" data-link="https://soosyze.com" selected>hello</option>' . PHP_EOL
            . '<option value="1">world</option>' . PHP_EOL
            . '</select>' . PHP_EOL,
            $this->object->form_select('inputSelect')
        );
    }

    public function testInputSelectGroup(): void
    {
        $options = [
            [ 'label' => 'hello', 'value' => 0 ],
            [ 'label' => 'world', 'value' => [
                    [ 'label' => 'hello', 'value' => 1 ],
                    [ 'label' => 'world', 'value' => 2 ]
                ]
            ]
        ];
        $this->object->select('inputSelect', $options, [ ':selected' => 1 ]);

        $this->assertEquals(
            '<select name="inputSelect" id="inputSelect">' . PHP_EOL
            . '<option value="0">hello</option>' . PHP_EOL
            . '<optgroup label="world">'
            . '<option value="1" selected>hello</option>' . PHP_EOL
            . '<option value="2">world</option>' . PHP_EOL
            . '</optgroup>' . PHP_EOL
            . '</select>' . PHP_EOL,
            $this->object->form_select('inputSelect')
        );
    }

    public function testInputTextarea(): void
    {
        $this->object->textarea('inputTextarea', 'lorem ipsum');

        $this->assertEquals(
            '<textarea name="inputTextarea" id="inputTextarea">lorem ipsum</textarea>' . PHP_EOL,
            $this->object->form_textarea('inputTextarea')
        );
    }

    public function testInputException(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage('The error type field does not exist');
        $this->object->error('inputTextarea', 'lorem ipsum');
    }

    public function testGroup(): void
    {
        $this->object->group('group', 'div', function () {
        });

        $this->assertEquals(
            '<div>' . PHP_EOL . '</div>' . PHP_EOL,
            $this->object->form_group('group')
        );

        $this->assertEquals(
            '<div>' . PHP_EOL . '</div>' . PHP_EOL,
            $this->object->form_group('group', [ ':tag' => 'error' ])
        );
    }

    public function testFormToken(): void
    {
        $this->object->token('test');

        $this->assertEquals(
            '<input name="test" type="hidden" value="' . $_SESSION[ 'token' ][ 'test' ] . '">' . PHP_EOL,
            $this->object->form_token('test')
        );
    }

    public function testFormOpen(): void
    {
        $this->assertEquals(
            '<form method="post" action="http://localhost/">' . PHP_EOL,
            $this->object->form_open()
        );
    }

    public function testFormClose(): void
    {
        $this->assertEquals(
            '</form>' . PHP_EOL,
            $this->object->form_close()
        );
    }

    public function testFormLabel(): void
    {
        $this->object
            ->label('label-test', 'lorem ipsum', [ 'data-tooltip' => 'Field name'])
            ->text('name');

        $this->assertEquals(
            '<label data-tooltip="Field name" for="name">lorem ipsum <i class="fa fa-info-circle"></i></label>' . PHP_EOL,
            $this->object->form_label('label-test')
        );
    }

    public function testFormLabelForManuel(): void
    {
        $this->object
            ->label('label-test', 'lorem ipsum', [ 'for' => 'id-for' ])
            ->text('name');

        $this->assertEquals(
            '<label for="id-for">lorem ipsum</label>' . PHP_EOL,
            $this->object->form_label('label-test')
        );
    }

    public function testFormLabelForRequire(): void
    {
        $this->object
            ->label('label-test', 'lorem ipsum')
            ->text('name', [ 'required' => 'required' ]);

        $this->assertEquals(
            '<label for="name">lorem ipsum<span class="form-required">*</span></label>' . PHP_EOL,
            $this->object->form_label('label-test')
        );
    }

    public function testFormLegend(): void
    {
        $this->object->legend('legend-test', 'lorem ipsum');

        $this->assertEquals(
            '<legend>lorem ipsum</legend>' . PHP_EOL,
            $this->object->form_legend('legend-test')
        );
    }

    public function testAddAttrs(): void
    {
        $this->object
            ->text('textName1')
            ->text('textName2')
            ->addAttr('textName1', [ 'required' => 'required' ]);

        $this->assertEquals(
            '<input name="textName1" type="text" id="textName1" required>' . PHP_EOL,
            $this->object->form_input('textName1')
        );

        $this->object->addAttrs([ 'textName1', 'textName2' ], [ 'value' => 'lorem ipsum' ]);

        $this->assertEquals(
            '<input name="textName1" type="text" id="textName1" required value="lorem ipsum">' . PHP_EOL,
            $this->object->form_input('textName1')
        );
        $this->assertEquals(
            '<input name="textName2" type="text" id="textName2" value="lorem ipsum">' . PHP_EOL,
            $this->object->form_input('textName2')
        );
    }

    public function testAddAttrGroup(): void
    {
        $this->object
            ->group('group', 'div', function ($form) {
                $form->text('textName1');
            })
            ->addAttr('textName1', [ 'required' => 'required' ]);

        $this->assertEquals(
            '<input name="textName1" type="text" id="textName1" required>' . PHP_EOL,
            $this->object->form_input('textName1')
        );
    }

    public function testAddAttrMulti(): void
    {
        $this->object
            ->checkbox('grp[1]')
            ->checkbox('grp[2]');

        $this->assertEquals(
            '<input name="grp[1]" type="checkbox" id="grp[1]">' . PHP_EOL,
            $this->object->form_input('grp[1]')
        );
        $this->assertEquals(
            '<input name="grp[2]" type="checkbox" id="grp[2]">' . PHP_EOL,
            $this->object->form_input('grp[2]')
        );

        $this->object->addAttrs([ 'grp' => [ 1, 2 ] ], [ 'required' => 'required' ]);

        $this->assertEquals(
            '<input name="grp[1]" type="checkbox" id="grp[1]" required>' . PHP_EOL,
            $this->object->form_input('grp[1]')
        );
        $this->assertEquals(
            '<input name="grp[2]" type="checkbox" id="grp[2]" required>' . PHP_EOL,
            $this->object->form_input('grp[2]')
        );
    }

    public function testAddAttrMultiSup(): void
    {
        $this->object
            ->checkbox('grp[1][test]')
            ->checkbox('grp[2][test2]');

        $this->assertEquals(
            '<input name="grp[1][test]" type="checkbox" id="grp[1][test]">' . PHP_EOL,
            $this->object->form_input('grp[1][test]')
        );
        $this->assertEquals(
            '<input name="grp[2][test2]" type="checkbox" id="grp[2][test2]">' . PHP_EOL,
            $this->object->form_input('grp[2][test2]')
        );

        $this->object->addAttrs(
            [ 'grp' => [ 1 => [ 'test' ], 2 => [ 'test2' ] ] ],
            [ 'required' => 'required' ]
        );

        $this->assertEquals(
            '<input name="grp[1][test]" type="checkbox" id="grp[1][test]" required>' . PHP_EOL,
            $this->object->form_input('grp[1][test]')
        );
        $this->assertEquals(
            '<input name="grp[2][test2]" type="checkbox" id="grp[2][test2]" required>' . PHP_EOL,
            $this->object->form_input('grp[2][test2]')
        );
    }

    public function testGetItem(): void
    {
        $item = $this->object->text('textName1');

        $this->assertEquals(
            [ 'type' => 'text', 'attr' => [ 'id' => 'textName1' ] ],
            $item->getItem('textName1')
        );
    }

    public function testGetItemGroup(): void
    {
        $item = $this->object
            ->group('group', 'div', function ($form) {
                $form->text('textName1');
            });

        $this->assertEquals(
            [ 'type' => 'text', 'attr' => [ 'id' => 'textName1' ] ],
            $item->getItem('textName1')
        );
    }

    public function testGetItemException(): void
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionMessage('The item error was not found.');
        $this->object->text('textName1')->getItem('error');
    }

    public function testBefore(): void
    {
        $this->object
            ->text('1')
            ->text('2')
            ->before('2', function ($form) {
                $form->text('3');
            })
            ->before('3', function ($form) {
                $form->text('4');
            });

        $this->assertEquals(
            '<form method="post" action="http://localhost/">' . PHP_EOL .
            '<input name="1" type="text" id="1">' . PHP_EOL .
            '<input name="4" type="text" id="4">' . PHP_EOL .
            '<input name="3" type="text" id="3">' . PHP_EOL .
            '<input name="2" type="text" id="2">' . PHP_EOL .
            '</form>' . PHP_EOL,
            (string) $this->object
        );
    }

    public function testBeforeGroup(): void
    {
        $this->object
            ->group('group', 'div', function ($form) {
                $form->text('1')
                ->text('2');
            })
            ->before('2', function ($form) {
                $form->text('3');
            })
            ->before('3', function ($form) {
                $form->text('4');
            });

        $this->assertEquals(
            '<form method="post" action="http://localhost/">' . PHP_EOL .
            '<div>' . PHP_EOL .
            '<input name="1" type="text" id="1">' . PHP_EOL .
            '<input name="4" type="text" id="4">' . PHP_EOL .
            '<input name="3" type="text" id="3">' . PHP_EOL .
            '<input name="2" type="text" id="2">' . PHP_EOL .
            '</div>' . PHP_EOL .
            '</form>' . PHP_EOL,
            (string) $this->object
        );
    }

    public function testAfter(): void
    {
        $this->object
            ->text('1')
            ->text('2')
            ->after('1', function ($form) {
                $form->text('3');
            })
            ->after('1', function ($form) {
                $form->text('4');
            });

        $this->assertEquals(
            '<form method="post" action="http://localhost/">' . PHP_EOL .
            '<input name="1" type="text" id="1">' . PHP_EOL .
            '<input name="4" type="text" id="4">' . PHP_EOL .
            '<input name="3" type="text" id="3">' . PHP_EOL .
            '<input name="2" type="text" id="2">' . PHP_EOL .
            '</form>' . PHP_EOL,
            (string) $this->object
        );
    }

    public function testAfterGroup(): void
    {
        $this->object
            ->group('group', 'div', function ($form) {
                $form->text('1')
                ->text('2');
            })
            ->after('1', function ($form) {
                $form->text('3');
            })
            ->after('1', function ($form) {
                $form->text('4');
            });

        $this->assertEquals(
            '<form method="post" action="http://localhost/">' . PHP_EOL .
            '<div>' . PHP_EOL .
            '<input name="1" type="text" id="1">' . PHP_EOL .
            '<input name="4" type="text" id="4">' . PHP_EOL .
            '<input name="3" type="text" id="3">' . PHP_EOL .
            '<input name="2" type="text" id="2">' . PHP_EOL .
            '</div>' . PHP_EOL .
            '</form>' . PHP_EOL,
            (string) $this->object
        );
    }

    public function testBeforeException(): void
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionMessage('The item error was not found.');
        $this->object->before('error', function () {
        });
    }

    public function testAfterException(): void
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionMessage('The item error was not found.');
        $this->object->after('error', function () {
        });
    }

    public function testPrepend(): void
    {
        $this->object
            ->group('group', 'div', function ($form) {
                $form->text('1')
                ->text('2');
            })
            ->prepend('group', function ($form) {
                $form->text('3');
            })
            ->prepend('group', function ($form) {
                $form->text('4');
            });

        $this->assertEquals(
            '<form method="post" action="http://localhost/">' . PHP_EOL .
            '<div>' . PHP_EOL .
            '<input name="4" type="text" id="4">' . PHP_EOL .
            '<input name="3" type="text" id="3">' . PHP_EOL .
            '<input name="1" type="text" id="1">' . PHP_EOL .
            '<input name="2" type="text" id="2">' . PHP_EOL .
            '</div>' . PHP_EOL .
            '</form>' . PHP_EOL,
            (string) $this->object
        );
    }

    public function testAppend(): void
    {
        $this->object
            ->group('group', 'div', function ($form) {
                $form->text('1')
                ->text('2');
            })
            ->append('group', function ($form) {
                $form->text('3');
            })
            ->append('group', function ($form) {
                $form->text('4');
            });

        $this->assertEquals(
            '<form method="post" action="http://localhost/">' . PHP_EOL .
            '<div>' . PHP_EOL .
            '<input name="1" type="text" id="1">' . PHP_EOL .
            '<input name="2" type="text" id="2">' . PHP_EOL .
            '<input name="3" type="text" id="3">' . PHP_EOL .
            '<input name="4" type="text" id="4">' . PHP_EOL .
            '</div>' . PHP_EOL .
            '</form>' . PHP_EOL,
            (string) $this->object
        );
    }

    public function testAppendGroup(): void
    {
        $this->object
            ->group('group', 'div', function ($form) {
                $form->group('group_2', 'div', function ($form) {
                    $form->text('1')
                    ->text('2');
                });
            })
            ->append('group_2', function ($form) {
                $form->text('3');
            })
            ->append('group_2', function ($form) {
                $form->text('4');
            });

        $this->assertEquals(
            '<form method="post" action="http://localhost/">' . PHP_EOL .
            '<div>' . PHP_EOL .
            '<div>' . PHP_EOL .
            '<input name="1" type="text" id="1">' . PHP_EOL .
            '<input name="2" type="text" id="2">' . PHP_EOL .
            '<input name="3" type="text" id="3">' . PHP_EOL .
            '<input name="4" type="text" id="4">' . PHP_EOL .
            '</div>' . PHP_EOL .
            '</div>' . PHP_EOL .
            '</form>' . PHP_EOL,
            (string) $this->object
        );
    }

    public function testPreprendException(): void
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionMessage('The item error was not found.');
        $this->object->prepend('error', function () {
        });
    }

    public function testappendException(): void
    {
        $this->expectException(\OutOfBoundsException::class);
        $this->expectExceptionMessage('The item error was not found.');
        $this->object->append('error', function () {
        });
    }

    public function testHtml(): void
    {
        $this->object
            ->html('image', '<img:attr/>', [
                'src' => '/files/logo.png',
                'alt' => 'Logo'
            ])
            ->html('paragraph', '<p:attr>:content</p>', [
                'id'       => 'test',
                ':content' => 'Logo'
        ]);

        $this->assertEquals(
            '<img src="/files/logo.png" alt="Logo" id="image"/>' . PHP_EOL,
            $this->object->form_html('image')
        );
        $this->assertEquals(
            '<p id="test">Logo</p>' . PHP_EOL,
            $this->object->form_html('paragraph')
        );
    }

    public function testSubformInLabel(): void
    {
        $this->object->label('test', function ($form) {
            $form->checkbox('check');
        }, [ 'id' => 'test' ]);

        $this->assertEquals(
            '<form method="post" action="http://localhost/">' . PHP_EOL .
            '<label id="test"><input name="check" type="checkbox" id="check">' . PHP_EOL .
            '</label>' . PHP_EOL .
            '</form>' . PHP_EOL,
            (string) $this->object
        );
    }
}
