<?php

namespace Soosyze\Components\Http;

final class Utils
{
    /**
     * @throws \RuntimeException
     * @return resource
     */
    public static function tryFopen(string $filename, string $mode)
    {
        try {
            /** @var resource $handle */
            $handle = fopen($filename, $mode);
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf(
                'Unable to open "%s" using mode "%s": %s',
                $filename,
                $mode,
                $e->getMessage()
            ), 0, $e);
        }

        return $handle;
    }
}
