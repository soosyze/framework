<?php

namespace Soosyze\Tests\Components\Validator\Rules;

use Soosyze\Components\Http\UploadedFile;

class FileExtensionTest extends RuleFile
{
    public function testExtensions(): void
    {
        $this->object->setInputs([
            'file_ext'              => $this->uplaod_img,
            'not_file_ext'          => $this->uplaod_txt,
            'file_ext_required'     => $this->uplaod_img,
            'file_ext_not_required' => ''
        ])->setRules([
            'file_ext'              => 'file_extensions:png,jpg,gif',
            'not_file_ext'          => '!file_extensions:png,jpg,gif',
            'file_ext_required'     => 'required|file_extensions:png,jpg,gif',
            'file_ext_not_required' => '!required|file_extensions:txt'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'file_ext'       => $this->uplaod_img,
            'not_file_ext'   => $this->uplaod_txt,
            'file_ext_error' => new UploadedFile($this->file_txt, '', 1, '', UPLOAD_ERR_INI_SIZE)
        ])->setRules([
            'file_ext'       => 'file_extensions:txt,jpg,gif',
            'not_file_ext'   => '!file_extensions:txt,jpg,gif',
            'file_ext_error' => 'file_extensions:png,jpg,gif'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(3, $this->object->getErrors());
    }
}
