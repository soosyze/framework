<?php

declare(strict_types=1);

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
class FileExtensions extends File
{
    /**
     * Test si un fichier possède l'une des extensions fournit dans les arguments.
     *
     * @param string                $key   Clé du test.
     * @param UploadedFileInterface $value Valeur à tester.
     * @param mixed                 $args  Liste d'extensions séparé par une virgule.
     * @param bool                  $not   Inverse le test.
     */
    protected function test(string $key, $value, $args, bool $not): void
    {
        parent::test('file', $value, '', true);

        if ($this->hasErrors()) {
            return;
        }

        if (!is_string($args)) {
            throw new \TypeError('The list of allowed extensions must be a string.');
        }
        $extensions = explode(',', $args);
        $extension  = $this->getExtension($value);

        if (!in_array($extension, $extensions) && $not) {
            $this->addReturn($key, 'ext', [ ':list' => $args ]);
        } elseif (in_array($extension, $extensions) && !$not) {
            $this->addReturn($key, 'not_ext', [ ':list' => $args ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages(): array
    {
        $output              = parent::messages();
        $output[ 'ext' ]     = 'The :label field must be a file of type :list.';
        $output[ 'not_ext' ] = 'The :label field must not be a file of type :list.';

        return $output;
    }
}
