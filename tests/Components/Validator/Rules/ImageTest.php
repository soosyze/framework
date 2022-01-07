<?php

namespace Soosyze\Tests\Components\Validator\Rules;

use Soosyze\Components\Http\UploadedFile;

class ImageTest extends RuleFile
{
    public function testImage(): void
    {
        $this->object->setInputs([
            'image'               => $this->uplaod_img,
            'not_image'           => $this->uplaod_txt,
            'image_required'      => $this->uplaod_img,
            'image_not_required'  => new UploadedFile('', '', 1, '', UPLOAD_ERR_NO_FILE),
            'image_not_required2' => ''
        ])->setRules([
            'image'               => 'image',
            'not_image'           => '!image',
            'image_required'      => 'required|image',
            'image_not_required'  => '!required|image',
            'image_not_required2' => '!required|image'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'image'          => $this->uplaod_txt,
            'not_image'      => $this->uplaod_img,
            'image_required' => new UploadedFile('', '', 1, '', UPLOAD_ERR_NO_FILE)
        ])->setRules([
            'image'          => 'image',
            'not_image'      => '!image',
            'image_required' => 'image'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(3, $this->object->getErrors());
    }

    public function testImageException(): void
    {
        $this->expectException(\Exception::class);
        $this->object
            ->addInput('image', $this->uplaod_txt)
            ->addRule('image', 'image:txt')
            ->isValid();
    }

    public function testImageExceptionMultiple(): void
    {
        $this->expectException(\Exception::class);
        $this->object
            ->addInput('image', $this->upload_xml)
            ->addRule('image', 'image:xml')
            ->isValid();
    }

    public function testExceptionArg(): void
    {
        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('The argument must be a string.');
        $this->object
            ->addInput('args', 1)
            ->addInput('field', '1')
            ->addRule('field', 'fontawesome:@args')
            ->isValid();
    }
}
