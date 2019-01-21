<?php

/**
 * Soosyze Framework http://soosyze.com
 *
 * @package Soosyze\Components\Validator\Rules
 * @author  Mathieu NOËL <mathieu@soosyze.com>
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator\Rules;

/**
 * {@inheritdoc}
 *
 * @author Mathieu NOËL
 */
class FileExtensions extends File
{

    /**
     * Test si un fichier possède l'une des extensions fournit dans les arguments.
     *
     * @param string $key Clé du test.
     * @param UploadedFileInterface $value Valeur à tester.
     * @param string $arg Liste d'extensions séparé par une virgule.
     * @param bool $not Inverse le test.
     *
     * @return int 1 erreur de fichier.
     */
    protected function test($key, $value, $arg, $not = true)
    {
        parent::test('file', $value, false);

        if ($this->hasErrors()) {
            return 1;
        }

        $extensions = explode(',', $arg);
        $extension  = $this->getExtension($value);

        if (!in_array($extension, $extensions) && $not) {
            $this->addReturn($key, 'ext', [ ':list' => $arg ]);
        } elseif (in_array($extension, $extensions) && !$not) {
            $this->addReturn($key, 'not_ext', [ ':list' => $arg ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages()
    {
        $output              = parent::messages();
        $output[ 'ext' ]     = 'La valeur :label n\'est pas dans la liste des extensions autorisées : :list.';
        $output[ 'not_ext' ] = 'La valeur de :label ne doit pas être dans la liste des extensions : :list.';

        return $output;
    }
}
