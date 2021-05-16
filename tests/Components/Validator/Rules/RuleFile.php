<?php

namespace Soosyze\Tests\Components\Validator\Rules;

use Soosyze\Components\Http\UploadedFile;
use Soosyze\Components\Validator\Validator;

class RuleFile extends \PHPUnit\Framework\TestCase
{
    use \Soosyze\Tests\Traits\ResourceTrait;

    /**
     * @var Validator
     */
    protected $object;

    /**
     * @var string
     */
    protected $file_txt = 'testUplaodFile.txt';

    /**
     * Image de dimensions 28 x 18
     *
     * @var string
     */
    protected $file_img = 'testUplaodFile.png';

    /**
     * @var string
     */
    protected $file_xml = 'testUploadFileError.xml';

    /**
     * @var string
     */
    protected $file_error = 'testUploadFile.gif';

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

        $stream = $this->streamFileFactory($this->file_txt, 'test content', 'w');
        fclose($stream);

        $content_xml = '<?xml version="1.0" encoding="UTF-8"?>'
            . '<note>'
            . '<to>Tove</to>'
            . '<from>Jani</from>'
            . '<heading>Reminder</heading>'
            . '<body>Don\'t forget me this weekend!</body>'
            . '</note>';
        $stream_xml  = $this->streamFileFactory($this->file_xml, $content_xml, 'w');
        fclose($stream_xml);

        $data = 'iVBORw0KGgoAAAANSUhEUgAAABwAAAASCAMAAAB/2U7WAAAABl'
            . 'BMVEUAAAD///+l2Z/dAAAASUlEQVR4XqWQUQoAIAxC2/0vXZDr'
            . 'EX4IJTRkb7lobNUStXsB0jIXIAMSsQnWlsV+wULF4Avk9fLq2r'
            . '8a5HSE35Q3eO2XP1A1wQkZSgETvDtKdQAAAABJRU5ErkJggg==';
        $data = base64_decode($data);
        $im   = $this->streamImageFactory($data);
        imagepng($im, $this->file_img);

        $stream_err = $this->streamFileFactory($this->file_error, '<?php echo "hello"; ?>', 'w');
        fclose($stream_err);

        /* Indroduction volontaire d'erreur dans la taille et le mine type. */
        $this->uplaod_txt   = new UploadedFile($this->file_txt, 'test.txt', 1024, 'error/mine');
        $this->uplaod_img   = new UploadedFile($this->file_img, 'test.png', 1, 'error/mine');
        $this->upload_xml   = new UploadedFile($this->file_xml, 'test.xml', 1, 'error/mine');
        $this->uplaod_error = new UploadedFile($this->file_error, 'test.gif', 1, 'error/mine');
    }

    protected function tearDown(): void
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
}
