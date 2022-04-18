<?php

declare(strict_types=1);

/**
 * Soosyze Framework https://soosyze.com
 *
 * @license https://github.com/soosyze/framework/blob/master/LICENSE (MIT License)
 */

namespace Soosyze\Components\Router;

use Soosyze\Components\Router\Exception\RouteArgumentException;

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

    /**
     * @var array<string, null|numeric|string>|null
     */
    private $withsDefault = null;

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

    public function getWithsDefault(): ?array
    {
        return $this->withsDefault;
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

    /**
     * @param null|numeric|string $value
     */
    public function setDefault(string $key, $value): self
    {
        $this->withsDefault[ $key ] = $value;

        return $this;
    }

    /**
     * Créer une expression régulière à partir du chemin et des arguments.
     */
    public function getRegexForPath(): string
    {
        $search  = [ '\\', '/' ];
        $replace = [ '//', '\/' ];

        if ($this->withs === null) {
            return str_replace($search, $replace, $this->path);
        }

        foreach ($this->withs as $key => $with) {
            $search[]  = '{' . $key . '}';
            $replace[] = '(?<' . $key . '>' . str_replace([ '(', '/' ], [ '(?:', '\/' ], $with) . ')';
        }

        return str_replace($search, $replace, $this->path);
    }

    public function generatePath(?array $withs = null, bool $strict = true): string
    {
        if ($this->withs === null) {
            return $this->path;
        }

        $path = $this->path;
        foreach ($this->withs as $key => $value) {
            if ($strict && !isset($withs[ $key ])) {
                throw new \InvalidArgumentException(
                    htmlspecialchars("the argument $key is missing")
                );
            }
            if (!$strict && !isset($withs[ $key ])) {
                continue;
            }
            $pattern = str_replace([ '(', '/' ], [ '(?:', '\/' ], $value);
            /** @phpstan-var array $withs */
            if ($strict && !preg_match('/^' . $pattern . '$/', (string) $withs[ $key ])) {
                throw new RouteArgumentException($withs[ $key ], $pattern, $path);
            }
            $path = str_replace('{' . $key . '}', $withs[ $key ], $path);
        }

        return $path;
    }

    private function filterPath(string $path): string
    {
        if ($path === '/') {
            return '/';
        }

        return rtrim($path, '/');
    }
}
