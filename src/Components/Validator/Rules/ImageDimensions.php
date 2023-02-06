<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Validator\Rules;

use Psr\Http\Message\UploadedFileInterface;
use Soosyze\Components\Validator\Comparators\MinMax;

/**
 * {@inheritdoc}
 *
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
abstract class ImageDimensions extends FileMimetypes
{
    /**
     * {@inheritdoc}
     *
     * @param string                $key   Clé du test.
     * @param UploadedFileInterface $value Valeur à tester.
     * @param mixed                 $args  Argument de test.
     * @param bool                  $not   Inverse le test.
     */
    public function test(string $key, $value, $args, bool $not): void
    {
        if (!is_string($args)) {
            throw new \TypeError('The argument must be a string.');
        }

        parent::test('file_mimetypes', $value, 'image', true);

        if ($this->hasErrors()) {
            return;
        }

        $length = $this->getDimensions($value);
        $type   = $key === 'image_dimensions_height'
            ? 'height'
            : 'width';
        $this->sizeBetween($key, $length[ $type ], $this->getParamMinMax($args), $not);
    }

    /**
     * Retourne les dimensions d'une image.
     *
     * @param UploadedFileInterface $upload Image
     *
     * @return int[] Dimensions
     */
    protected function getDimensions(UploadedFileInterface $upload): array
    {
        /** @var string $uri */
        $uri = $upload->getStream()->getMetadata('uri');
        /** @var array $imageSize */
        $imageSize = getimagesize($uri);
        [ $width, $height ] = $imageSize;

        return ['width' => $width, 'height' => $height];
    }

    /**
     * Test la taille d'une image.
     *
     * @param string $key         Clé du test.
     * @param int    $lengthValue Hauteur de l'image en pixel.
     * @param MinMax $comparator  Hauteur minimum et maximum autorisées.
     * @param bool   $not         Inverse le test.
     */
    protected function sizeBetween(
        string $key,
        $lengthValue,
        MinMax $comparator,
        bool $not
    ): void {
        if (!($lengthValue <= $comparator->getValueMax() && $lengthValue >= $comparator->getValueMin()) && $not) {
            $this->addReturn($key, 'must', [
                ':min' => $comparator->getValueMin(),
                ':max' => $comparator->getValueMax()
            ]);
        } elseif ($lengthValue <= $comparator->getValueMax() && $lengthValue >= $comparator->getValueMin() && !$not) {
            $this->addReturn($key, 'not_must', [
                ':min' => $comparator->getValueMin(),
                ':max' => $comparator->getValueMax()
            ]);
        }
    }
}
