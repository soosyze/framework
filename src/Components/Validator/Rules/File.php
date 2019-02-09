<?php

/**
 * Soosyze Framework http://soosyze.com
 *
 * @package Soosyze\Components\Validator\Rules
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator\Rules;

use Psr\Http\Message\UploadedFileInterface;

/**
 * {@inheritdoc}
 *
 * @author Mathieu NOËL
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
    protected function test($key, $value, $arg, $not = true)
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
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        return [
            'must'        => ':label n\'est pas un fichier.',
            'not'         => ':label ne doit pas être un fichier.',
            'ini_size'    => 'La taille du fichier téléchargé excède la valeur de upload_max_filesize, configurée dans le php.ini',
            'form_size'   => 'La taille du fichier téléchargé excède la valeur de MAX_FILE_SIZE, qui a été spécifiée dans le formulaire HTML.',
            'err_partial' => 'Le fichier n\'a été que partiellement téléchargé.',
            'no_file'     => 'Aucun fichier n\'a été téléchargé.',
            'no_tmp_dir'  => 'Un dossier temporaire est manquant.',
            'cant_write'  => 'Échec de l\'écriture du fichier sur le disque.',
            'extension'   => 'Une extension PHP a arrêté l\'envoi de fichier.'
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
     * @return string|false Minetype ou FALSE si une erreur s'est produite.
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
        $filename = $filename = $upload->getClientFilename();

        return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    }
}
