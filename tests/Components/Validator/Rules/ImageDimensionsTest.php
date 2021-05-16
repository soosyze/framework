<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class ImageDimensionsTest extends RuleFile
{
    public function testImageDimensionsHeight(): void
    {
        $this->object->setInputs([
            'image_dimensions'              => $this->uplaod_img,
            'image_dimensions_required'     => $this->uplaod_img,
            'image_dimensions_not_required' => ''
        ])->setRules([
            'image_dimensions'              => 'image_dimensions_height:0,20',
            'image_dimensions_required'     => 'required|image_dimensions_height:0,20',
            'image_dimensions_not_required' => '!required|image_dimensions_height:0,20'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'image_dimensions'              => $this->uplaod_txt,
            'image_dimensions_required'     => $this->uplaod_img,
            'image_dimensions_not_required' => $this->uplaod_img
        ])->setRules([
            'image_dimensions'              => 'image_dimensions_height:0,20',
            'image_dimensions_required'     => 'image_dimensions_height:0,10',
            'image_dimensions_not_required' => '!image_dimensions_height:0,20'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(3, $this->object->getErrors());
    }

    public function testImageDimensionsMissingException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->object
            ->addInput('image_dimensions', $this->uplaod_img)
            ->addRule('image_dimensions', 'image_dimensions_height')
            ->isValid();
    }

    public function testImageDimensionsTypeMin(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->object
            ->addInput('image_dimensions', $this->uplaod_img)
            ->addRule('image_dimensions', 'image_dimensions_height:error,5')
            ->isValid();
    }

    public function testImageDimensionsTypeMax(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->object
            ->addInput('image_dimensions', $this->uplaod_img)
            ->addRule('image_dimensions', 'image_dimensions_height:5,error')
            ->isValid();
    }

    public function testImageDimensionsUpperMax(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->object
            ->addInput('image_dimensions', $this->uplaod_img)
            ->addRule('image_dimensions', 'image_dimensions_height:10,5')
            ->isValid();
    }

    public function testImageDimensionsWidth(): void
    {
        $this->object->setInputs([
            'image_dimensions'              => $this->uplaod_img,
            'image_dimensions_required'     => $this->uplaod_img,
            'image_dimensions_not_required' => ''
        ])->setRules([
            'image_dimensions'              => 'image_dimensions_width:0,30',
            'image_dimensions_required'     => 'required|image_dimensions_width:0,30',
            'image_dimensions_not_required' => '!required|image_dimensions_width:0,30'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'image_dimensions'              => $this->uplaod_txt,
            'image_dimensions_required'     => $this->uplaod_img,
            'image_dimensions_not_required' => $this->uplaod_img
        ])->setRules([
            'image_dimensions'              => 'image_dimensions_width:0,30',
            'image_dimensions_required'     => 'image_dimensions_width:0,10',
            'image_dimensions_not_required' => '!image_dimensions_width:0,30'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(3, $this->object->getErrors());
    }
}
