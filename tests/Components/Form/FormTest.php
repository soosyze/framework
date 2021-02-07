<?php

namespace Soosyze\Tests\Components\Form;

use Soosyze\Components\Form\FormBuilder;

class FormTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var FormBuilder
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        @session_start([
                'cookie_httponly' => true,
                'cookie_secure'   => true
        ]);
        parent::setUp();
        $this->object = new FormBuilder([ 'method' => 'post', 'action' => 'http://localhost/' ]);
    }

    public function testInputBasic()
    {
        $this->object->inputBasic('text', 'textName', [
            'required' => 'required',
            'value'    => 'lorem ipsum'
        ]);

        $form   = $this->object->form_input('textName');
        $result = '<input name="textName" type="text" required value="lorem ipsum" id="textName">' . PHP_EOL;

        $this->assertEquals($form, $result);
    }

    public function testInputText()
    {
        $this->object->text('textName', [
            'required' => 'required',
            'value'    => 'lorem ipsum'
        ]);

        $form   = $this->object->form_input('textName');
        $result = '<input name="textName" type="text" required value="lorem ipsum" id="textName">' . PHP_EOL;

        $this->assertEquals($form, $result);
    }

    public function testInputPassword()
    {
        $this->object->password('passwordName', [
            'required' => 'required',
            'value'    => 'lorem ipsum'
        ]);

        $form   = $this->object->form_input('passwordName');
        $result = '<input name="passwordName" type="password" required value="lorem ipsum" id="passwordName">' . PHP_EOL;

        $this->assertEquals($form, $result);
    }

    public function testInputEmail()
    {
        $this->object->email('email', [
            'required' => 'required',
            'value'    => 'lorem ipsum'
        ]);

        $form   = $this->object->form_input('email');
        $result = '<input name="email" type="email" required value="lorem ipsum" id="email">' . PHP_EOL;

        $this->assertEquals($form, $result);
    }

    public function testInputCheckbox()
    {
        $this->object->checkbox('checkboxName');

        $form   = $this->object->form_input('checkboxName');
        $result = '<input name="checkboxName" type="checkbox" id="checkboxName">' . PHP_EOL;

        $this->assertEquals($form, $result);
    }

    public function testInputRadio()
    {
        $this->object->radio('radioName');

        $form   = $this->object->form_input('radioName');
        $result = '<input name="radioName" type="radio" id="radioName">' . PHP_EOL;

        $this->assertEquals($form, $result);
    }

    public function testInputSubmit()
    {
        $this->object->submit('inputSubmit', 'Enregistrer');

        $form   = $this->object->form_input('inputSubmit');
        $result = '<input name="inputSubmit" type="submit" id="inputSubmit" value="Enregistrer">' . PHP_EOL;

        $this->assertEquals($form, $result);
    }

    public function testInputSelect()
    {
        $options = [
            [ 'label' => 'hello', 'value' => 0 ],
            [ 'label' => 'world', 'value' => 1 ],
        ];
        $this->object->select('inputSelect', $options, [ ':selected' => 0 ]);

        $form   = $this->object->form_select('inputSelect');
        $result = '<select name="inputSelect" id="inputSelect">' . PHP_EOL
            . '<option value="0" selected>hello</option>' . PHP_EOL
            . '<option value="1">world</option>' . PHP_EOL
            . '</select>' . PHP_EOL;

        $this->assertEquals($form, $result);
    }

    public function testInputSelectGroup()
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

        $form   = $this->object->form_select('inputSelect');
        $result = '<select name="inputSelect" id="inputSelect">' . PHP_EOL
            . '<option value="0">hello</option>' . PHP_EOL
            . '<optgroup label="world">'
            . '<option value="1" selected>hello</option>' . PHP_EOL
            . '<option value="2">world</option>' . PHP_EOL
            . '</optgroup>' . PHP_EOL
            . '</select>' . PHP_EOL;

        $this->assertEquals($form, $result);
    }

    public function testInputTextarea()
    {
        $this->object->textarea('textareaName', 'lorem ipsum');

        $form   = $this->object->form_textarea('textareaName');
        $result = '<textarea name="textareaName" id="textareaName">lorem ipsum</textarea>' . PHP_EOL;

        $this->assertEquals($form, $result);
    }

    /**
     * @expectedException \Exception
     */
    public function testInputException()
    {
        $this->object->error('textareaName', 'lorem ipsum');
    }

    public function testGroup()
    {
        $this->object->group('group', 'div', function () {
        });

        $form   = $this->object->form_group('group');
        $result = '<div>' . PHP_EOL . '</div>' . PHP_EOL;

        $this->assertEquals($form, $result);

        $form   = $this->object->form_group('group', [ ':tag' => 'error' ]);
        $result = '<div>' . PHP_EOL . '</div>' . PHP_EOL;

        $this->assertEquals($form, $result);
    }

    public function testFormToken()
    {
        $this->object->token('test');

        $form   = $this->object->form_token('test');
        $result = '<input name="test" type="hidden" value="' . $_SESSION[ 'token' ][ 'test' ] . '">' . PHP_EOL;

        $this->assertEquals($form, $result);
    }

    public function testFormOpen()
    {
        $form   = $this->object->form_open();
        $result = '<form method="post" action="http://localhost/">' . PHP_EOL;

        $this->assertEquals($form, $result);
    }

    public function testFormClose()
    {
        $form   = $this->object->form_close();
        $result = '</form>' . PHP_EOL;

        $this->assertEquals($form, $result);
    }

    public function testFormLabel()
    {
        $this->object->label('label-test', 'lorem ipsum');

        $form   = $this->object->form_label('label-test');
        $result = '<label>lorem ipsum</label>' . PHP_EOL;

        $this->assertEquals($form, $result);
    }

    public function testFormLabelFor()
    {
        $this->object
            ->label('label-test-require', 'lorem ipsum')
            ->text('name');

        $form   = $this->object->form_label('label-test-require');
        $result = '<label for="name">lorem ipsum</label>' . PHP_EOL;

        $this->assertEquals($form, $result);
    }

    public function testFormLabelForManuel()
    {
        $this->object
            ->label('label-test-require', 'lorem ipsum', [ 'for' => 'id-for' ])
            ->text('name');

        $form   = $this->object->form_label('label-test-require');
        $result = '<label for="id-for">lorem ipsum</label>' . PHP_EOL;

        $this->assertEquals($form, $result);
    }

    public function testFormLabelForRequire()
    {
        $this->object
            ->label('label-test-require', 'lorem ipsum')
            ->text('name', [ 'required' => 'required' ]);

        $form   = $this->object->form_label('label-test-require');
        $result = '<label for="name">lorem ipsum<span class="form-required">*</span></label>' . PHP_EOL;

        $this->assertEquals($form, $result);
    }

    public function testFormLegend()
    {
        $this->object->legend('legend-test', 'lorem ipsum');

        $form   = $this->object->form_legend('legend-test');
        $result = '<legend>lorem ipsum</legend>' . PHP_EOL;

        $this->assertEquals($form, $result);
    }

    public function testAddAttrs()
    {
        $this->object
            ->text('textName1')
            ->text('textName2')
            ->addAttr('textName1', [ 'required' => 'required' ]);

        $input1  = $this->object->form_input('textName1');
        $result1 = '<input name="textName1" type="text" id="textName1" required>' . PHP_EOL;
        $this->assertEquals($input1, $result1);

        $this->object->addAttrs([ 'textName1', 'textName2' ], [ 'value' => 'lorem ipsum' ]);

        $input1  = $this->object->form_input('textName1');
        $input2  = $this->object->form_input('textName2');
        $result1 = '<input name="textName1" type="text" id="textName1" required value="lorem ipsum">' . PHP_EOL;
        $result2 = '<input name="textName2" type="text" id="textName2" value="lorem ipsum">' . PHP_EOL;

        $this->assertEquals($input1, $result1);
        $this->assertEquals($input2, $result2);
    }

    public function testAddAttrGroup()
    {
        $this->object
            ->group('group', 'div', function ($form) {
                $form->text('textName1');
            })
            ->addAttr('textName1', [ 'required' => 'required' ]);

        $input1  = $this->object->form_input('textName1');
        $result1 = '<input name="textName1" type="text" id="textName1" required>' . PHP_EOL;
        $this->assertEquals($input1, $result1);
    }

    public function testAddAttrMulti()
    {
        $this->object
            ->checkbox('grp[1]')
            ->checkbox('grp[2]');

        $input1  = $this->object->form_input('grp[1]');
        $input2  = $this->object->form_input('grp[2]');
        $result1 = '<input name="grp[1]" type="checkbox" id="grp[1]">' . PHP_EOL;
        $result2 = '<input name="grp[2]" type="checkbox" id="grp[2]">' . PHP_EOL;

        $this->assertEquals($input1, $result1);
        $this->assertEquals($input2, $result2);

        $this->object->addAttrs([ 'grp' => [ 1, 2 ] ], [ 'required' => 'required' ]);

        $input1  = $this->object->form_input('grp[1]');
        $input2  = $this->object->form_input('grp[2]');
        $result1 = '<input name="grp[1]" type="checkbox" id="grp[1]" required>' . PHP_EOL;
        $result2 = '<input name="grp[2]" type="checkbox" id="grp[2]" required>' . PHP_EOL;

        $this->assertEquals($input1, $result1);
        $this->assertEquals($input2, $result2);
    }

    public function testAddAttrMultiSup()
    {
        $this->object
            ->checkbox('grp[1][test]')
            ->checkbox('grp[2][test2]');

        $input1  = $this->object->form_input('grp[1][test]');
        $input2  = $this->object->form_input('grp[2][test2]');
        $result1 = '<input name="grp[1][test]" type="checkbox" id="grp[1][test]">' . PHP_EOL;
        $result2 = '<input name="grp[2][test2]" type="checkbox" id="grp[2][test2]">' . PHP_EOL;

        $this->assertEquals($input1, $result1);
        $this->assertEquals($input2, $result2);

        $this->object->addAttrs(
            [ 'grp' => [ 1 => [ 'test' ], 2 => [ 'test2' ] ] ],
            [ 'required' => 'required' ]
        );

        $input1  = $this->object->form_input('grp[1][test]');
        $input2  = $this->object->form_input('grp[2][test2]');
        $result1 = '<input name="grp[1][test]" type="checkbox" id="grp[1][test]" required>' . PHP_EOL;
        $result2 = '<input name="grp[2][test2]" type="checkbox" id="grp[2][test2]" required>' . PHP_EOL;

        $this->assertEquals($input1, $result1);
        $this->assertEquals($input2, $result2);
    }

    public function testGetItem()
    {
        $item = $this->object
            ->text('textName1')
            ->getItem('textName1');

        $this->assertEquals(
            [ 'type' => 'text', 'attr' => [ 'id' => 'textName1' ] ],
            $item
        );
    }

    public function testGetItemGroup()
    {
        $item = $this->object
            ->group('group', 'div', function ($form) {
                $form->text('textName1');
            })
            ->getItem('textName1');

        $this->assertEquals(
            [ 'type' => 'text', 'attr' => [ 'id' => 'textName1' ] ],
            $item
        );
    }

    /**
     * @expectedException \Exception
     */
    public function testGetItemException()
    {
        $this->object->text('textName1')->getItem('error');
    }

    public function testBefore()
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
            (string) $this->object,
            '<form method="post" action="http://localhost/">' . PHP_EOL .
            '<input name="1" type="text" id="1">' . PHP_EOL .
            '<input name="4" type="text" id="4">' . PHP_EOL .
            '<input name="3" type="text" id="3">' . PHP_EOL .
            '<input name="2" type="text" id="2">' . PHP_EOL .
            '</form>' . PHP_EOL
        );
    }

    public function testBeforeGroup()
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
            (string) $this->object,
            '<form method="post" action="http://localhost/">' . PHP_EOL .
            '<div>' . PHP_EOL .
            '<input name="1" type="text" id="1">' . PHP_EOL .
            '<input name="4" type="text" id="4">' . PHP_EOL .
            '<input name="3" type="text" id="3">' . PHP_EOL .
            '<input name="2" type="text" id="2">' . PHP_EOL .
            '</div>' . PHP_EOL .
            '</form>' . PHP_EOL
        );
    }

    public function testAfter()
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
            (string) $this->object,
            '<form method="post" action="http://localhost/">' . PHP_EOL .
            '<input name="1" type="text" id="1">' . PHP_EOL .
            '<input name="4" type="text" id="4">' . PHP_EOL .
            '<input name="3" type="text" id="3">' . PHP_EOL .
            '<input name="2" type="text" id="2">' . PHP_EOL .
            '</form>' . PHP_EOL
        );
    }

    public function testAfterGroup()
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
            (string) $this->object,
            '<form method="post" action="http://localhost/">' . PHP_EOL .
            '<div>' . PHP_EOL .
            '<input name="1" type="text" id="1">' . PHP_EOL .
            '<input name="4" type="text" id="4">' . PHP_EOL .
            '<input name="3" type="text" id="3">' . PHP_EOL .
            '<input name="2" type="text" id="2">' . PHP_EOL .
            '</div>' . PHP_EOL .
            '</form>' . PHP_EOL
        );
    }

    /**
     * @expectedException \Exception
     */
    public function testBeforeException()
    {
        $this->object->before('error', function () {
        });
    }

    /**
     * @expectedException \Exception
     */
    public function testAfterException()
    {
        $this->object->after('error', function () {
        });
    }

    public function testPrepend()
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
            (string) $this->object,
            '<form method="post" action="http://localhost/">' . PHP_EOL .
            '<div>' . PHP_EOL .
            '<input name="4" type="text" id="4">' . PHP_EOL .
            '<input name="3" type="text" id="3">' . PHP_EOL .
            '<input name="1" type="text" id="1">' . PHP_EOL .
            '<input name="2" type="text" id="2">' . PHP_EOL .
            '</div>' . PHP_EOL .
            '</form>' . PHP_EOL
        );
    }

    public function testAppend()
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
            (string) $this->object,
            '<form method="post" action="http://localhost/">' . PHP_EOL .
            '<div>' . PHP_EOL .
            '<input name="1" type="text" id="1">' . PHP_EOL .
            '<input name="2" type="text" id="2">' . PHP_EOL .
            '<input name="3" type="text" id="3">' . PHP_EOL .
            '<input name="4" type="text" id="4">' . PHP_EOL .
            '</div>' . PHP_EOL .
            '</form>' . PHP_EOL
        );
    }

    public function testAppendGroup()
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
            (string) $this->object,
            '<form method="post" action="http://localhost/">' . PHP_EOL .
            '<div>' . PHP_EOL .
            '<div>' . PHP_EOL .
            '<input name="1" type="text" id="1">' . PHP_EOL .
            '<input name="2" type="text" id="2">' . PHP_EOL .
            '<input name="3" type="text" id="3">' . PHP_EOL .
            '<input name="4" type="text" id="4">' . PHP_EOL .
            '</div>' . PHP_EOL .
            '</div>' . PHP_EOL .
            '</form>' . PHP_EOL
        );
    }

    /**
     * @expectedException \Exception
     */
    public function testPreprendException()
    {
        $this->object->prepend('error', function () {
        });
    }

    /**
     * @expectedException \Exception
     */
    public function testappendException()
    {
        $this->object->append('error', function () {
        });
    }

    public function testHtml()
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
            $this->object->form_html('image'),
            '<img src="/files/logo.png" alt="Logo" id="image"/>' . PHP_EOL
        );
        $this->assertEquals(
            $this->object->form_html('paragraph'),
            '<p id="test">Logo</p>' . PHP_EOL
        );
    }

    public function testSubformInLabel()
    {
        $this->object->label('test', function ($form) {
            $form->checkbox('check');
        }, [ 'id' => 'test' ]);

        $this->assertEquals(
            (string) $this->object,
            '<form method="post" action="http://localhost/">' . PHP_EOL .
            '<label id="test"><input name="check" type="checkbox" id="check">' . PHP_EOL .
            '</label>' . PHP_EOL .
            '</form>' . PHP_EOL
        );
    }
}
