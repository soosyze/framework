<?php

namespace Soosyze\Tests\Components\Validator\Rules;

use Soosyze\Components\Http\UploadedFile;

class FileSizeTest extends RuleFile
{
    public function testMax(): void
    {
        $this->object->setInputs([
            'file_max'              => $this->uplaod_txt,
            'not_file_max'          => $this->uplaod_img,
            'file_max_required'     => $this->uplaod_txt,
            'file_max_not_required' => '',
            'file_ext_error'        => new UploadedFile($this->file_txt, '', 1, '', UPLOAD_ERR_INI_SIZE)
        ])->setRules([
            'file_max'              => 'max:15',
            'not_file_max'          => '!max:15',
            'file_max_required'     => 'required|max:15',
            'file_max_not_required' => '!required|max:15',
            'file_ext_error'        => 'max:15'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'file_max_size'     => $this->uplaod_txt,
            'not_file_max_size' => $this->uplaod_img
        ])->setRules([
            'file_max_size'     => 'max:15B',
            'not_file_max_size' => '!max:15B'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            /* Text */
            'file_max'     => $this->uplaod_img,
            'not_file_max' => $this->uplaod_txt,
        ])->setRules([
            /* Text */
            'file_max'     => 'max:15',
            'not_file_max' => '!max:15',
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    public function testMin(): void
    {
        $this->object->setInputs([
            'file_min'              => $this->uplaod_img,
            'not_file_min'          => $this->uplaod_txt,
            'file_min_required'     => $this->uplaod_img,
            'file_min_not_required' => ''
        ])->setRules([
            'file_min'              => 'min:15',
            'not_file_min'          => '!min:15',
            'file_min_required'     => 'required|min:15',
            'file_min_not_required' => '!required|min:15'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            /* Text */
            'file_min'     => $this->uplaod_txt,
            'not_file_min' => $this->uplaod_img
        ])->setRules([
            /* Text */
            'file_min'     => 'min:15B',
            'not_file_min' => '!min:15B'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    public function testBetween(): void
    {
        $this->object->setInputs([
            'file_between_min'          => $this->uplaod_txt,
            'file_between_max'          => $this->uplaod_txt,
            'not_file_between'          => $this->uplaod_img,
            'file_between_required'     => $this->uplaod_txt,
            'file_between_not_required' => '',
        ])->setRules([
            'file_between_min'          => 'between:5,15',
            'file_between_max'          => 'between:5,15',
            'not_file_between'          => '!between:5,15',
            'file_between_required'     => 'required|between:5,15',
            'file_between_not_required' => '!required|between:5,15',
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'file_between_min' => 'Lor',
            'file_between_max' => 'Lorem ip'
        ])->setRules([
            'file_between_min' => 'between:5,10',
            'file_between_max' => '!between:5,10'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }
}
