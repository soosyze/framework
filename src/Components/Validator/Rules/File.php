<?php

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator\Rules;

use Psr\Http\Message\UploadedFileInterface;

/**
 * {@inheritdoc}
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
class File extends \Soosyze\Components\Validator\Rule
{
    /**
     * Test si la valeur est un fichier.
     *
     * @param string                $key   Clé du test.
     * @param UploadedFileInterface $value Valeur à tester.
     * @param string                $arg   Argument de test.
     * @param bool                  $not   Inverse le test.
     */
    protected function test($key, $value, $arg, $not)
    {
        if (!($value instanceof UploadedFileInterface) && $not) {
            $this->addReturn($key, 'must');
        } elseif ($value instanceof UploadedFileInterface && !$not) {
            $this->addReturn($key, 'not');

            return;
        }

        if ($value instanceof UploadedFileInterface) {
            $this->checkErrorFile($key, $value);
        }

        if ($this->hasErrors()) {
            $this->stopPropagation();
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        return [
            'must'        => 'The :label field is not a file.',
            'not'         => 'The :label field must not be a file.',
            'ini_size'    => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
            'form_size'   => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
            'err_partial' => 'The uploaded file was only partially uploaded.',
            'no_file'     => 'No file was uploaded.',
            'no_tmp_dir'  => 'Missing a temporary folder.',
            'cant_write'  => 'Failed to write file to disk.',
            'extension'   => 'A PHP extension stopped the file upload.'
        ];
    }

    /**
     * Vérifie si le fichier ne contient pas d'erreur.
     *
     * @param string                $key   Clé du test.
     * @param UploadedFileInterface $value
     */
    protected function checkErrorFile($key, UploadedFileInterface $value)
    {
        switch ($value->getError()) {
            case UPLOAD_ERR_INI_SIZE:
                $this->addReturn($key, 'ini_size');

                break;
            case UPLOAD_ERR_FORM_SIZE:
                $this->addReturn($key, 'form_size');

                break;
            case UPLOAD_ERR_PARTIAL:
                $this->addReturn($key, 'err_partial');

                break;
            case UPLOAD_ERR_NO_FILE:
                $this->addReturn($key, 'no_file');

                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $this->addReturn($key, 'no_tmp_dir');

                break;
            case UPLOAD_ERR_CANT_WRITE:
                $this->addReturn($key, 'cant_write');

                break;
            case UPLOAD_ERR_EXTENSION:
                $this->addReturn($key, 'extension');

                break;
        }
    }

    /**
     * Retourne le mimetype du fichier.
     *
     * @param UploadedFileInterface $upload
     *
     * @return string|false Mimetype ou FALSE si une erreur s'est produite.
     */
    protected function getMime(UploadedFileInterface $upload)
    {
        $file = $upload->getStream()->getMetadata('uri');

        return (new \finfo(FILEINFO_MIME_TYPE))->file($file);
    }

    /**
     * Retourne l'extension du fichier.
     *
     * @param UploadedFileInterface $upload
     *
     * @return @return string|false Extension du fichier ou FALSE si une erreur s'est produite.
     */
    protected function getExtension(UploadedFileInterface $upload)
    {
        $filename = $upload->getClientFilename();

        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }
}
