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
class FileMimetypes extends File
{
    /**
     * Test si un fichier est d'un type de mime(application, image, font, text, video...)
     *
     * @param string                $key   Clé du test.
     * @param UploadedFileInterface $value Valeur à tester.
     * @param string                $args  Mime exacte ou le type du mime du fichier.
     * @param bool                  $not   Inverse le test.
     */
    protected function test(string $key, $value, $args, bool $not): void
    {
        parent::test('file', $value, '', true);

        if ($this->hasErrors()) {
            return;
        }

        $mtype = $this->getMime($value);

        if (strpos($mtype, $args) !== 0 && $not) {
            $this->addReturn($key, 'mime_types', [ ':list' => $args ]);
        } elseif (strpos($mtype, $args) === 0 && !$not) {
            $this->addReturn($key, 'not_mime_types', [ ':list' => $args ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function messages(): array
    {
        $output                     = parent::messages();
        $output[ 'mime_types' ]     = 'The :label field must be a file of type :list.';
        $output[ 'not_mime_types' ] = 'The :label field must not be a file of type :list.';

        return $output;
    }
}
