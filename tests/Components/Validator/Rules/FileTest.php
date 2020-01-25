<?php

namespace Soosyze\Tests\Components\Validator\Rules;

use Soosyze\Components\Http\UploadedFile;

class FileTest extends RuleFile
{
    public function testFile()
    {
        $this->object->setInputs([
            'file'              => $this->uplaod_txt,
            'not_file'          => 'noFile',
            'file_required'     => $this->uplaod_txt,
            'file_not_required' => '',
        ])->setRules([
            'file'              => 'file',
            'not_file'          => '!file',
            'file_required'     => 'required|file',
            'file_not_required' => '!required|file',
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'file'     => 'noFile',
            'not_file' => $this->uplaod_txt
        ])->setRules([
            'file'     => 'file',
            'not_file' => '!file'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    public function testFileError()
    {
        $this->object->setInputs([
            'file_ini_size'   => new UploadedFile($this->file_txt, '', 1, '', UPLOAD_ERR_INI_SIZE),
            'file_form_size'  => new UploadedFile($this->file_txt, '', 1, '', UPLOAD_ERR_FORM_SIZE),
            'file_partial'    => new UploadedFile($this->file_txt, '', 1, '', UPLOAD_ERR_PARTIAL),
            'file_no_file'    => new UploadedFile($this->file_txt, '', 1, '', UPLOAD_ERR_NO_FILE),
            'file_no_tmp_dir' => new UploadedFile($this->file_txt, '', 1, '', UPLOAD_ERR_NO_TMP_DIR),
            'file_cant_write' => new UploadedFile($this->file_txt, '', 1, '', UPLOAD_ERR_CANT_WRITE),
            'file_extension'  => new UploadedFile($this->file_txt, '', 1, '', UPLOAD_ERR_EXTENSION)
        ])->setRules([
            'file_ini_size'   => 'file',
            'file_form_size'  => 'file',
            'file_partial'    => 'file',
            'file_no_file'    => 'file',
            'file_no_tmp_dir' => 'file',
            'file_cant_write' => 'file',
            'file_extension'  => 'file'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(7, $this->object->getErrors());
    }
}
