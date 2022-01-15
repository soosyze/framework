<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Router;

/**
 * @author Mathieu NOËL <mathieu@soosyze.com>
 */
final class Route implements \JsonSerializable
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $path;

    /**
     * @var string
     */
    private $uses;

    /**
     * @var array|null
     */
    private $withs = null;

    public function __construct(
        string $key,
        string $method,
        string $path,
        string $uses,
        ?array $withs = null
    ) {
        $this->key    = $key;
        $this->method = $method;
        $this->path   = $this->filterPath($path);
        $this->uses   = $uses;
        $this->withs  = $withs;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getUses(): string
    {
        return $this->uses;
    }

    public function getCallable(): array
    {
        return explode('@', $this->uses);
    }

    public function getWiths(): ?array
    {
        return $this->withs;
    }

    public function jsonSerialize()
    {
        return [
            'key'    => $this->key,
            'method' => $this->method,
            'path'   => $this->path,
            'uses'   => $this->uses,
            'withs'  => $this->withs
        ];
    }

    public function whereDigits(string $key): self
    {
        $this->withs[ $key ] = '\d+';

        return $this;
    }

    public function whereWords(string $key): self
    {
        $this->withs[ $key ] = '\w+';

        return $this;
    }

    public function whereSlug(string $key): self
    {
        $this->withs[ $key ] = '[a-z\d\-]+';

        return $this;
    }

    private function filterPath(string $path): string
    {
        if ($path === '/') {
            return '/';
        }

        return rtrim($path, '/');
    }
}
