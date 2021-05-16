<?php

namespace Soosyze\Tests\Components\Validator\Rules;

use Soosyze\Components\Http\UploadedFile;

class FileMimetypeTest extends RuleFile
{
    public function testMimetypes(): void
    {
        $this->object->setInputs([
            'file_minetypes'              => $this->uplaod_img,
            'not_file_minetypes'          => $this->uplaod_txt,
            'file_minetypes_required'     => $this->uplaod_img,
            'file_minetypes_not_required' => ''
        ])->setRules([
            'file_minetypes'              => 'file_mimetypes:image',
            'not_file_minetypes'          => '!file_mimetypes:image',
            'file_minetypes_required'     => 'required|file_mimetypes:image',
            'file_minetypes_not_required' => '!required|file_mimetypes:image'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'file_minetypes'       => $this->uplaod_img,
            'not_file_minetypes'   => $this->uplaod_txt,
            'file_minetypes_error' => new UploadedFile($this->file_txt, '', 1, '', UPLOAD_ERR_INI_SIZE)
        ])->setRules([
            'file_minetypes'       => 'file_mimetypes:text',
            'not_file_minetypes'   => '!file_mimetypes:text',
            'file_minetypes_error' => '!file_mimetypes:text'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(3, $this->object->getErrors());
    }
}
