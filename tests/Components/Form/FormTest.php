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
        @session_start();
        parent::setUp();
        $this->object = new FormBuilder([ 'method' => 'post', 'action' => 'http://localhost/' ]);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        
    }

    public function testInputBasic()
    {
        $this->object->inputBasic('text', 'textName', 'textId', [
            'required' => 'required',
            'value'    => 'lorem ipsum'
        ]);

        $form   = $this->object->form_input('textName');
        $result = '<input name="textName" type="text" id="textId" required value="lorem ipsum">' . "\r\n";

        $this->assertEquals($form, $result);
    }

    public function testInputText()
    {
        $this->object->text('textName', 'textId', [
            'required' => 'required',
            'value'    => 'lorem ipsum'
        ]);

        $form   = $this->object->form_input('textName');
        $result = '<input name="textName" type="text" id="textId" required value="lorem ipsum">' . "\r\n";

        $this->assertEquals($form, $result);
    }

    public function testInputPassword()
    {
        $this->object->password('passwordName', 'passwordId', [
            'required' => 'required',
            'value'    => 'lorem ipsum'
        ]);

        $form   = $this->object->form_input('passwordName');
        $result = '<input name="passwordName" type="password" id="passwordId" required value="lorem ipsum">' . "\r\n";

        $this->assertEquals($form, $result);
    }

    public function testInputEmail()
    {
        $this->object->email('email', 'email', [
            'required' => 'required',
            'value'    => 'lorem ipsum'
        ]);

        $form   = $this->object->form_input('email');
        $result = '<input name="email" type="email" id="email" required value="lorem ipsum">' . "\r\n";

        $this->assertEquals($form, $result);
    }

    public function testInputCheckbox()
    {
        $this->object->checkbox('checkboxName', 'checkboxId');

        $form   = $this->object->form_input('checkboxName');
        $result = '<input name="checkboxName" type="checkbox" id="checkboxId">' . "\r\n";

        $this->assertEquals($form, $result);
    }

    public function testInputRadio()
    {
        $this->object->radio('radioName', 'radioId');

        $form   = $this->object->form_input('radioName');
        $result = '<input name="radioName" type="radio" id="radioId">' . "\r\n";

        $this->assertEquals($form, $result);
    }

    public function testInputSubmit()
    {
        $this->object->submit('inputSubmit', 'Enregistrer');

        $form   = $this->object->form_input('inputSubmit');
        $result = '<input name="inputSubmit" type="submit" value="Enregistrer">' . "\r\n";

        $this->assertEquals($form, $result);
    }

    public function testInputSelect()
    {
        $this->object->select('inputSelect', [
            [ 'value' => 0, 'label' => 'hello' ],
            [ 'value' => 1, 'label' => 'world' ]
            ], [ 'selected' => 0 ]);

        $form   = $this->object->form_select('inputSelect');
        $result = '<select name="inputSelect">' . "\r\n"
            . '<option value="0" selected>hello</option>' . "\r\n"
            . '<option value="1" >world</option>' . "\r\n"
            . '</select>' . "\r\n";

        $this->assertEquals($form, $result);
    }

    public function testInputTextarea()
    {
        $this->object->textarea('textareaName', 'lorem ipsum');

        $form   = $this->object->form_textarea('textareaName');
        $result = '<textarea name="textareaName">lorem ipsum</textarea>' . "\r\n";

        $this->assertEquals($form, $result);
    }

    /**
     * @expectedException Exception
     */
    public function testInputException()
    {
        $this->object->error('textareaName', 'lorem ipsum');
    }

    public function testGroup()
    {
        $this->object->group('group', 'div', function()
        {
            
        });

        $form   = $this->object->form_group('group');
        $result = '<div>' . "\r\n" . '</div>' . "\r\n";

        $this->assertEquals($form, $result);

        $form   = $this->object->form_group('group', [ 'balise' => 'error' ]);
        $result = '<div>' . "\r\n" . '</div>' . "\r\n";

        $this->assertEquals($form, $result);
    }

    public function testForm_token()
    {
        $this->object->token();

        $form   = $this->object->form_token();
        $result = '<input name="token" type="hidden" value="' . $_SESSION[ 'token' ] . '">' . "\r\n";

        $this->assertEquals($form, $result);
    }

    public function testForm_open()
    {
        $form   = $this->object->form_open();
        $result = '<form method="post" action="http://localhost/">' . "\r\n";

        $this->assertEquals($form, $result);
    }

    public function testForm_close()
    {
        $form   = $this->object->form_close();
        $result = '</form>' . "\r\n";

        $this->assertEquals($form, $result);
    }

    public function testForm_label()
    {
        $this->object->label('label-test', 'lorem ipsum');

        $form   = $this->object->form_label('label-test');
        $result = '<label>lorem ipsum</label>' . "\r\n";

        $this->assertEquals($form, $result);
    }

    public function testForm_label_for()
    {
        $this->object->label('label-test-require', 'lorem ipsum')
            ->text('name', 'name');

        $form   = $this->object->form_label('label-test-require');
        $result = '<label for="name">lorem ipsum</label>' . "\r\n";

        $this->assertEquals($form, $result);
    }

    public function testForm_label_forManuel()
    {
        $this->object->label('label-test-require', 'lorem ipsum', [ 'for' => 'id-for' ])
            ->text('name', 'id-for');

        $form   = $this->object->form_label('label-test-require');
        $result = '<label for="id-for">lorem ipsum</label>' . "\r\n";

        $this->assertEquals($form, $result);
    }

    public function testForm_label_forRequire()
    {
        $this->object->label('label-test-require', 'lorem ipsum')
            ->text('name', 'name', [ 'required' => 'required' ]);

        $form   = $this->object->form_label('label-test-require');
        $result = '<label for="name">lorem ipsum<span class="form-required">*</span></label>' . "\r\n";

        $this->assertEquals($form, $result);
    }

    public function testForm_legend()
    {
        $this->object->legend('legend-test', 'lorem ipsum');

        $form   = $this->object->form_legend('legend-test');
        $result = '<legend>lorem ipsum</legend>' . "\r\n";

        $this->assertEquals($form, $result);
    }

    public function testAddAttr()
    {
        $this->object->text('textName1', 'textId');
        $this->object->text('textName2', 'textId');
        $this->object->addAttr('textName1', [ 'required' => 'required' ]);

        $input1  = $this->object->form_input('textName1');
        $result1 = '<input name="textName1" type="text" id="textId" required>' . "\r\n";
        $this->assertEquals($input1, $result1);

        $this->object->addAttrs([ 'textName1', 'textName2' ], [ 'value' => 'lorem ipsum' ]);

        $input1  = $this->object->form_input('textName1');
        $input2  = $this->object->form_input('textName2');
        $result1 = '<input name="textName1" type="text" id="textId" required value="lorem ipsum">' . "\r\n";
        $result2 = '<input name="textName2" type="text" id="textId" value="lorem ipsum">' . "\r\n";

        $this->assertEquals($input1, $result1);
        $this->assertEquals($input2, $result2);
    }

    public function testAddAttrGroup()
    {
        $this->object->group('group', 'div', function($form)
        {
            $form->text('textName1', 'textId');
        });
        $this->object->addAttr('textName1', [ 'required' => 'required' ]);

        $input1  = $this->object->form_input('textName1');
        $result1 = '<input name="textName1" type="text" id="textId" required>' . "\r\n";
        $this->assertEquals($input1, $result1);
    }

    /**
     * @expectedException Exception
     */
    public function testAddAttrException()
    {
        $this->object->text('textName1', 'textId');
        $this->object->addAttr('error', [ 'required' => 'required' ]);
    }

    public function testGetItem()
    {
        $this->object->text('textName1', 'textId');
        $item = $this->object->getItem('textName1');

        $this->assertEquals([ 'type' => 'text',
            'name' => 'textName1',
            'attr' => [ 'id' => 'textId' ]
            ], $item);
    }

    public function testGetItemGroup()
    {
        $this->object->group('group', 'div', function($form)
        {
            $form->text('textName1', 'textId');
        });
        $item = $this->object->getItem('textName1');

        $this->assertEquals([ 'type' => 'text',
            'name' => 'textName1',
            'attr' => [ 'id' => 'textId' ]
            ], $item);
    }

    /**
     * @expectedException Exception
     */
    public function testGetItemException()
    {
        $this->object->text('textName1', 'textId');
        $this->object->getItem('error');
    }
}