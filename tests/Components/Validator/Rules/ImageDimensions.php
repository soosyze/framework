<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class ImageDimensions extends RuleFile
{
    public function testImageDimensionsHeight()
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

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testImageDimensionsMissingException()
    {
        $this->object
            ->addInput('image_dimensions', $this->uplaod_img)
            ->addRule('image_dimensions', 'image_dimensions_height')
            ->isValid();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testImageDimensionsTypeMin()
    {
        $this->object
            ->addInput('image_dimensions', $this->uplaod_img)
            ->addRule('image_dimensions', 'image_dimensions_height:error,5')
            ->isValid();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testImageDimensionsTypeMax()
    {
        $this->object
            ->addInput('image_dimensions', $this->uplaod_img)
            ->addRule('image_dimensions', 'image_dimensions_height:5,error')
            ->isValid();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testImageDimensionsUpperMax()
    {
        $this->object
            ->addInput('image_dimensions', $this->uplaod_img)
            ->addRule('image_dimensions', 'image_dimensions_height:10,5')
            ->isValid();
    }

    public function testImageDimensionsWidth()
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
