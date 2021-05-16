<?php

namespace Soosyze\Tests\Components\Validator;

use Soosyze\Components\Http\UploadedFile;
use Soosyze\Components\Validator\Validator;

class ValidatorFileTest extends \PHPUnit\Framework\TestCase
{
    use \Soosyze\Tests\Traits\ResourceTrait;

    /**
     * @var string
     */
    private const FILE_TXT = 'testUplaodFile.txt';

    /**
     * Image de dimensions 28 x 18
     *
     * @var string
     */
    private const FILE_IMG = 'testUplaodFile.png';

    /**
     * @var string
     */
    private const FILE_XML = 'testUploadFileError.xml';

    /**
     * @var string
     */
    private const FILE_ERROR = 'testUploadFile.gif';

    /**
     * @var Validator
     */
    protected $object;

    /**
     * @var UploadedFile
     */
    protected $uplaod_txt;

    /**
     * @var UploadedFile
     */
    protected $uplaod_img;

    /**
     * @var UploadedFile
     */
    protected $upload_xml;

    /**
     * @var UploadedFile
     */
    protected $uplaod_error;

    protected function setUp(): void
    {
        $this->object = new Validator;

        $stream = $this->streamFileFactory(self::FILE_TXT, 'test content', 'w');
        fclose($stream);

        $content_xml = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<note>'
            . '<to>Tove</to>'
            . '<from>Jani</from>'
            . '<heading>Reminder</heading>'
            . '<body>Don\'t forget me this weekend!</body>'
            . '</note>';
        $stream_xml  = $this->streamFileFactory(self::FILE_XML, $content_xml, 'w');
        fclose($stream_xml);

        $data = 'iVBORw0KGgoAAAANSUhEUgAAABwAAAASCAMAAAB/2U7WAAAABl'
            . 'BMVEUAAAD///+l2Z/dAAAASUlEQVR4XqWQUQoAIAxC2/0vXZDr'
            . 'EX4IJTRkb7lobNUStXsB0jIXIAMSsQnWlsV+wULF4Avk9fLq2r'
            . '8a5HSE35Q3eO2XP1A1wQkZSgETvDtKdQAAAABJRU5ErkJggg==';
        $data = base64_decode($data);
        $im   = $this->streamImageFactory($data);
        imagepng($im, self::FILE_IMG);

        $stream_err = $this->streamFileFactory(self::FILE_ERROR, '<?php echo "hello"; ?>', 'w');
        fclose($stream_err);

        /* Indroduction volontaire d'erreur dans la taille et le mine type. */
        $this->uplaod_txt   = new UploadedFile(self::FILE_TXT, 'test.txt', 1024, 'error/mine');
        $this->uplaod_img   = new UploadedFile(self::FILE_IMG, 'test.png', 1, 'error/mine');
        $this->upload_xml   = new UploadedFile(self::FILE_XML, 'test.xml', 1, 'error/mine');
        $this->uplaod_error = new UploadedFile(self::FILE_ERROR, 'test.gif', 1, 'error/mine');
    }

    protected function tearDown(): void
    {
        /* Supprime le fichier du test */
        if (file_exists(self::FILE_TXT)) {
            $this->uplaod_txt->getStream()->close();
            unlink(self::FILE_TXT);
        }
        if (file_exists(self::FILE_IMG)) {
            $this->uplaod_img->getStream()->close();
            unlink(self::FILE_IMG);
        }
        if (file_exists(self::FILE_XML)) {
            $this->upload_xml->getStream()->close();
            unlink(self::FILE_XML);
        }
        if (file_exists(self::FILE_ERROR)) {
            $this->uplaod_error->getStream()->close();
            unlink(self::FILE_ERROR);
        }
    }

    public function testValidFile(): void
    {
        $this->object->setInputs([
            'field_file'              => $this->uplaod_txt,
            'field_not_file'          => 'noFile',
            'field_file_required'     => $this->uplaod_txt,
            'field_file_not_required' => '',
        ])->setRules([
            'field_file'              => 'file',
            'field_not_file'          => '!file',
            'field_file_required'     => 'required|file',
            'field_file_not_required' => '!required|file',
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_file'     => 'noFile',
            'field_not_file' => $this->uplaod_txt
        ])->setRules([
            'field_file'     => 'file',
            'field_not_file' => '!file'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    public function testValidFileError(): void
    {
        $this->object->setInputs([
            'field_file_ini_size'   => new UploadedFile(self::FILE_TXT, '', 1, '', UPLOAD_ERR_INI_SIZE),
            'field_file_form_size'  => new UploadedFile(self::FILE_TXT, '', 1, '', UPLOAD_ERR_FORM_SIZE),
            'field_file_partial'    => new UploadedFile(self::FILE_TXT, '', 1, '', UPLOAD_ERR_PARTIAL),
            'field_file_no_file'    => new UploadedFile(self::FILE_TXT, '', 1, '', UPLOAD_ERR_NO_FILE),
            'field_file_no_tmp_dir' => new UploadedFile(self::FILE_TXT, '', 1, '', UPLOAD_ERR_NO_TMP_DIR),
            'field_file_cant_write' => new UploadedFile(self::FILE_TXT, '', 1, '', UPLOAD_ERR_CANT_WRITE),
            'field_file_extension'  => new UploadedFile(self::FILE_TXT, '', 1, '', UPLOAD_ERR_EXTENSION)
        ])->setRules([
            'field_file_ini_size'   => 'file',
            'field_file_form_size'  => 'file',
            'field_file_partial'    => 'file',
            'field_file_no_file'    => 'file',
            'field_file_no_tmp_dir' => 'file',
            'field_file_cant_write' => 'file',
            'field_file_extension'  => 'file'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(7, $this->object->getErrors());
    }

    public function testValidMax(): void
    {
        $this->object->setInputs([
            'field_file_max'              => $this->uplaod_txt,
            'field_not_file_max'          => $this->uplaod_img,
            'field_file_max_required'     => $this->uplaod_txt,
            'field_file_max_not_required' => '',
            'field_file_ext_error'        => new UploadedFile(self::FILE_TXT, '', 1, '', UPLOAD_ERR_INI_SIZE)
        ])->setRules([
            'field_file_max'              => 'max:15',
            'field_not_file_max'          => '!max:15',
            'field_file_max_required'     => 'required|max:15',
            'field_file_max_not_required' => '!required|max:15',
            'field_file_ext_error'        => 'max:15'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_file_max_size'     => $this->uplaod_txt,
            'field_not_file_max_size' => $this->uplaod_img
        ])->setRules([
            'field_file_max_size'     => 'max:15B',
            'field_not_file_max_size' => '!max:15B'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            /* Text */
            'field_file_max'     => $this->uplaod_img,
            'field_not_file_max' => $this->uplaod_txt,
        ])->setRules([
            /* Text */
            'field_file_max'     => 'max:15',
            'field_not_file_max' => '!max:15',
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    public function testValidMin(): void
    {
        $this->object->setInputs([
            'field_file_min'              => $this->uplaod_img,
            'field_not_file_min'          => $this->uplaod_txt,
            'field_file_min_required'     => $this->uplaod_img,
            'field_file_min_not_required' => ''
        ])->setRules([
            'field_file_min'              => 'min:15',
            'field_not_file_min'          => '!min:15',
            'field_file_min_required'     => 'required|min:15',
            'field_file_min_not_required' => '!required|min:15'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            /* Text */
            'field_file_min'     => $this->uplaod_txt,
            'field_not_file_min' => $this->uplaod_img
        ])->setRules([
            /* Text */
            'field_file_min'     => 'min:15',
            'field_not_file_min' => '!min:15'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    public function testValidBetween(): void
    {
        $this->object->setInputs([
            'field_file_between_min'          => $this->uplaod_txt,
            'field_file_between_max'          => $this->uplaod_txt,
            'field_not_file_between'          => $this->uplaod_img,
            'field_file_between_required'     => $this->uplaod_txt,
            'field_file_between_not_required' => '',
        ])->setRules([
            'field_file_between_min'          => 'between:5,15',
            'field_file_between_max'          => 'between:5,15',
            'field_not_file_between'          => '!between:5,15',
            'field_file_between_required'     => 'required|between:5,15',
            'field_file_between_not_required' => '!required|between:5,15',
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_file_between_min' => 'Lor',
            'field_file_between_max' => 'Lorem ip'
        ])->setRules([
            'field_file_between_min' => 'between:5,10',
            'field_file_between_max' => '!between:5,10'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }
}
