<?php

namespace Soosyze\Tests\Traits;

trait ResourceTrait
{
    /**
     * @return resource
     */
    public function getRessourceTemp(string $mode = 'r+')
    {
        return $this->getRessourceFile('php://temp', $mode);
    }

    /**
     * @return resource
     */
    public function getRessourceFile(string $file, string $mode = 'r+')
    {
        $handle = fopen($file, $mode);

        if ($handle === false) {
            throw new \Exception();
        }

        return $handle;
    }

    /**
     * @return resource
     */
    public function streamFactory(string $content, string $mode = 'r+')
    {
        $stream = $this->getRessourceTemp($mode);
        fwrite($stream, $content);
        rewind($stream);

        return $stream;
    }

    /**
     * @return resource
     */
    public function streamFileFactory(
        string $file,
        string $content,
        string $mode = 'r+'
    ) {
        $stream = $this->getRessourceFile($file, $mode);
        fwrite($stream, $content);
        rewind($stream);

        return $stream;
    }

    /**
     * @return resource
     */
    public function streamImageFactory(string $image)
    {
        $handle = imagecreatefromstring($image);

        if ($handle === false) {
            throw new \Exception();
        }

        return $handle;
    }
}
