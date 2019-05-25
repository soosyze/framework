<?php

namespace Soosyze\Tests\Components\Validator;

use Soosyze\Components\Http\UploadedFile;
use Soosyze\Components\Validator\Validator;

class ValidatorFileTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Validator
     */
    protected $object;

    /**
     * @var resource
     */
    protected $file_txt = 'testUplaodFile.txt';

    /**
     * Image de dimensions 28 x 18
     *
     * @var resource
     */
    protected $file_img = 'testUplaodFile.png';

    /**
     * @var resource
     */
    protected $file_xml = 'testUploadFileError.xml';
    
    /**
     * @var resource
     */
    protected $file_error = 'testUploadFile.gif';

    /**
     * @var \UploadedFile
     */
    protected $uplaod_txt;

    /**
     * @var \UploadedFile
     */
    protected $uplaod_img;
    
    /**
     * @var \UploadedFile
     */
    protected $upload_xml;

    /**
     * @var \UploadedFile
     */
    protected $uplaod_error;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Validator;

        $stream = fopen($this->file_txt, 'w');
        fwrite($stream, 'test content');
        fclose($stream);
        
        $stream_js = fopen($this->file_xml, 'w');
        fwrite($stream_js, '<?xml version="1.0" encoding="UTF-8"?>'
            . '<note>'
            . '<to>Tove</to>'
            . '<from>Jani</from>'
            . '<heading>Reminder</heading>'
            . '<body>Don\'t forget me this weekend!</body>'
            . '</note>');
        fclose($stream_js);

        $data = 'iVBORw0KGgoAAAANSUhEUgAAABwAAAASCAMAAAB/2U7WAAAABl'
            . 'BMVEUAAAD///+l2Z/dAAAASUlEQVR4XqWQUQoAIAxC2/0vXZDr'
            . 'EX4IJTRkb7lobNUStXsB0jIXIAMSsQnWlsV+wULF4Avk9fLq2r'
            . '8a5HSE35Q3eO2XP1A1wQkZSgETvDtKdQAAAABJRU5ErkJggg==';
        $data = base64_decode($data);
        $im   = imagecreatefromstring($data);
        imagepng($im, $this->file_img);
        
        $stream_err = fopen($this->file_error, 'w');
        fwrite($stream_err, '<?php echo "hello"; ?>');
        fclose($stream_err);

        /* Indroduction volontaire d'erreur dans la taille et le mine type. */
        $this->uplaod_txt   = new UploadedFile($this->file_txt, 'test.txt', 1024, 'error/mine');
        $this->uplaod_img   = new UploadedFile($this->file_img, 'test.png', 1, 'error/mine');
        $this->upload_xml    = new UploadedFile($this->file_xml, 'test.xml', 1, 'error/mine');
        $this->uplaod_error = new UploadedFile($this->file_error, 'test.gif', 1, 'error/mine');
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        /* Supprime le fichier du test */
        if (file_exists($this->file_txt)) {
            $this->uplaod_txt->getStream()->close();
            unlink($this->file_txt);
        }
        if (file_exists($this->file_img)) {
            $this->uplaod_img->getStream()->close();
            unlink($this->file_img);
        }
        if (file_exists($this->file_xml)) {
            $this->upload_xml->getStream()->close();
            unlink($this->file_xml);
        }
        if (file_exists($this->file_error)) {
            $this->uplaod_error->getStream()->close();
            unlink($this->file_error);
        }
    }

    public function testValidFile()
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

    public function testValidFileError()
    {
        $this->object->setInputs([
            'field_file_ini_size'   => new UploadedFile($this->file_txt, '', 1, '', UPLOAD_ERR_INI_SIZE),
            'field_file_form_size'  => new UploadedFile($this->file_txt, '', 1, '', UPLOAD_ERR_FORM_SIZE),
            'field_file_partial'    => new UploadedFile($this->file_txt, '', 1, '', UPLOAD_ERR_PARTIAL),
            'field_file_no_file'    => new UploadedFile($this->file_txt, '', 1, '', UPLOAD_ERR_NO_FILE),
            'field_file_no_tmp_dir' => new UploadedFile($this->file_txt, '', 1, '', UPLOAD_ERR_NO_TMP_DIR),
            'field_file_cant_write' => new UploadedFile($this->file_txt, '', 1, '', UPLOAD_ERR_CANT_WRITE),
            'field_file_extension'  => new UploadedFile($this->file_txt, '', 1, '', UPLOAD_ERR_EXTENSION)
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

    public function testValidMax()
    {
        $this->object->setInputs([
            'field_file_max'              => $this->uplaod_txt,
            'field_not_file_max'          => $this->uplaod_img,
            'field_file_max_required'     => $this->uplaod_txt,
            'field_file_max_not_required' => '',
            'field_file_ext_error'        => new UploadedFile($this->file_txt, '', 1, '', UPLOAD_ERR_INI_SIZE)
        ])->setRules([
            'field_file_max'              => 'max:15',
            'field_not_file_max'          => '!max:15',
            'field_file_max_required'     => 'required|max:15',
            'field_file_max_not_required' => '!required|max:15',
            'field_file_ext_error'        => 'max:15'
        ]);

        $this->assertTrue($this->object->isValid());
    }

    public function testValidMin()
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

    public function testValidBetween()
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

    public function testValiExtensions()
    {
        $this->object->setInputs([
            'field_file_ext'              => $this->uplaod_img,
            'field_not_file_ext'          => $this->uplaod_txt,
            'field_file_ext_required'     => $this->uplaod_img,
            'field_file_ext_not_required' => ''
        ])->setRules([
            'field_file_ext'              => 'file_extensions:png,jpg,gif',
            'field_not_file_ext'          => '!file_extensions:png,jpg,gif',
            'field_file_ext_required'     => 'required|file_extensions:png,jpg,gif',
            'field_file_ext_not_required' => '!required|file_extensions:txt'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_file_ext'       => $this->uplaod_img,
            'field_not_file_ext'   => $this->uplaod_txt,
            'field_file_ext_error' => new UploadedFile($this->file_txt, '', 1, '', UPLOAD_ERR_INI_SIZE)
        ])->setRules([
            'field_file_ext'       => 'file_extensions:txt,jpg,gif',
            'field_not_file_ext'   => '!file_extensions:txt,jpg,gif',
            'field_file_ext_error' => 'file_extensions:png,jpg,gif'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(3, $this->object->getErrors());
    }

    public function testValiMimetypes()
    {
        $this->object->setInputs([
            'field_file_minetypes'              => $this->uplaod_img,
            'field_not_file_minetypes'          => $this->uplaod_txt,
            'field_file_minetypes_required'     => $this->uplaod_img,
            'field_file_minetypes_not_required' => ''
        ])->setRules([
            'field_file_minetypes'              => 'file_mimetypes:image/png',
            'field_not_file_minetypes'          => '!file_mimetypes:image/png',
            'field_file_minetypes_required'     => 'required|file_mimetypes:image/png',
            'field_file_minetypes_not_required' => '!required|file_mimetypes:image/png'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_file_minetypes'       => $this->uplaod_img,
            'field_not_file_minetypes'   => $this->uplaod_txt,
            'field_file_minetypes_error' => new UploadedFile($this->file_txt, '', 1, '', UPLOAD_ERR_INI_SIZE)
        ])->setRules([
            'field_file_minetypes'       => 'file_mimetypes:text/plain',
            'field_not_file_minetypes'   => '!file_mimetypes:text/plain',
            'field_file_minetypes_error' => '!file_mimetypes:text/plain'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(3, $this->object->getErrors());
    }

    public function testValidMimes()
    {
        $this->object->setInputs([
            'field_file_mimes'              => $this->uplaod_img,
            'field_not_file_mimes'          => $this->uplaod_txt,
            'field_file_mimes_required'     => $this->uplaod_img,
            'field_file_mimes_not_required' => ''
        ])->setRules([
            'field_file_mimes'              => 'file_mimes:png',
            'field_not_file_mimes'          => '!file_mimes:png',
            'field_file_mimes_required'     => 'required|file_mimes:png',
            'field_file_mimes_not_required' => '!required|file_mimes:png'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_file_mimes_error'     => $this->uplaod_error,
            'field_file_mimes'           => $this->uplaod_img,
            'field_file_mimes_not'       => $this->uplaod_img,
            'field_file_mimes_error_ext' => $this->uplaod_img
        ])->setRules([
            'field_file_mimes_error'     => 'file_mimes:gif',
            'field_file_mimes'           => 'file_mimes:txt',
            'field_file_mimes_not'       => '!file_mimes:png',
            'field_file_mimes_error_ext' => 'file_mimes:error'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(4, $this->object->getErrors());
    }

    public function testValidImage()
    {
        $this->object->setInputs([
            'field_image'               => $this->uplaod_img,
            'field_not_image'           => $this->uplaod_txt,
            'field_image_required'      => $this->uplaod_img,
            'field_image_not_required'  => new UploadedFile('', '', 1, '', UPLOAD_ERR_NO_FILE),
            'field_image_not_required2' => ''
        ])->setRules([
            'field_image'               => 'image',
            'field_not_image'           => '!image',
            'field_image_required'      => 'required|image',
            'field_image_not_required'  => '!required|image',
            'field_image_not_required2' => '!required|image'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_image'          => $this->uplaod_txt,
            'field_not_image'      => $this->uplaod_img,
            'field_image_required' => new UploadedFile('', '', 1, '', UPLOAD_ERR_NO_FILE)
        ])->setRules([
            'field_image'          => 'image',
            'field_not_image'      => '!image',
            'field_image_required' => 'image'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(3, $this->object->getErrors());
    }

    /**
     * @expectedException \Exception
     */
    public function testValidImageException()
    {
        $this->object->setInputs([
            'field_image'               => $this->uplaod_txt
        ])->setRules([
            'field_image'               => 'image:txt'
        ]);

        $this->object->isValid();
    }
    
    /**
     * @expectedException \Exception
     */
    public function testValidImageExceptionMultiple()
    {
        $this->object->setInputs([
            'field_image'               => $this->upload_xml
        ])->setRules([
            'field_image'               => 'image:xml'
        ]);

        $this->object->isValid();
    }

    public function testImageDimensionsHeight()
    {
        $this->object->setInputs([
            'field_image_dimensions'              => $this->uplaod_img,
            'field_image_dimensions_required'     => $this->uplaod_img,
            'field_image_dimensions_not_required' => ''
        ])->setRules([
            'field_image_dimensions'              => 'image_dimensions_height:0,20',
            'field_image_dimensions_required'     => 'required|image_dimensions_height:0,20',
            'field_image_dimensions_not_required' => '!required|image_dimensions_height:0,20'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_image_dimensions'              => $this->uplaod_txt,
            'field_image_dimensions_required'     => $this->uplaod_img,
            'field_image_dimensions_not_required' => $this->uplaod_img
        ])->setRules([
            'field_image_dimensions'              => 'image_dimensions_height:0,20',
            'field_image_dimensions_required'     => 'image_dimensions_height:0,10',
            'field_image_dimensions_not_required' => '!image_dimensions_height:0,20'
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
            ->addInput('field_image_dimensions', $this->uplaod_img)
            ->addRule('field_image_dimensions', 'image_dimensions_height')
            ->isValid();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testImageDimensionsTypeMin()
    {
        $this->object
            ->addInput('field_image_dimensions', $this->uplaod_img)
            ->addRule('field_image_dimensions', 'image_dimensions_height:error,5')
            ->isValid();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testImageDimensionsTypeMax()
    {
        $this->object
            ->addInput('field_image_dimensions', $this->uplaod_img)
            ->addRule('field_image_dimensions', 'image_dimensions_height:5,error')
            ->isValid();
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testImageDimensionsUpperMax()
    {
        $this->object
            ->addInput('field_image_dimensions', $this->uplaod_img)
            ->addRule('field_image_dimensions', 'image_dimensions_height:10,5')
            ->isValid();
    }

    public function testImageDimensionsWidth()
    {
        $this->object->setInputs([
            'field_image_dimensions'              => $this->uplaod_img,
            'field_image_dimensions_required'     => $this->uplaod_img,
            'field_image_dimensions_not_required' => ''
        ])->setRules([
            'field_image_dimensions'              => 'image_dimensions_width:0,30',
            'field_image_dimensions_required'     => 'required|image_dimensions_width:0,30',
            'field_image_dimensions_not_required' => '!required|image_dimensions_width:0,30'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'field_image_dimensions'              => $this->uplaod_txt,
            'field_image_dimensions_required'     => $this->uplaod_img,
            'field_image_dimensions_not_required' => $this->uplaod_img
        ])->setRules([
            'field_image_dimensions'              => 'image_dimensions_width:0,30',
            'field_image_dimensions_required'     => 'image_dimensions_width:0,10',
            'field_image_dimensions_not_required' => '!image_dimensions_width:0,30'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(3, $this->object->getErrors());
    }
}
