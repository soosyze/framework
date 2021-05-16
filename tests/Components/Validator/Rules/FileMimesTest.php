<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class FileMimesTest extends RuleFile
{
    public function testMimes(): void
    {
        $this->object->setInputs([
            'file_mimes'              => $this->uplaod_img,
            'not_file_mimes'          => $this->uplaod_txt,
            'file_mimes_required'     => $this->uplaod_img,
            'file_mimes_not_required' => ''
        ])->setRules([
            'file_mimes'              => 'file_mimes:png',
            'not_file_mimes'          => '!file_mimes:png',
            'file_mimes_required'     => 'required|file_mimes:png',
            'file_mimes_not_required' => '!required|file_mimes:png'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'file_mimes_error'     => $this->uplaod_error,
            'file_mimes'           => $this->uplaod_img,
            'file_mimes_not'       => $this->uplaod_img,
            'file_mimes_error_ext' => $this->uplaod_img
        ])->setRules([
            'file_mimes_error'     => 'file_mimes:gif',
            'file_mimes'           => 'file_mimes:txt',
            'file_mimes_not'       => '!file_mimes:png',
            'file_mimes_error_ext' => 'file_mimes:error'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(4, $this->object->getErrors());
    }
}
