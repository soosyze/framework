<?php

/**
 * @see https://mwop.net/blog/2014-08-11-testing-output-generating-code.html
 */

namespace Soosyze\Components\Http
{

    abstract class Output
    {
        /**
         * @var string[]
         */
        public static $headers = [];

        public static function reset(): void
        {
            self::$headers = [];
        }
    }

    function headers_sent(): bool
    {
        return false;
    }

    function header(string $value): void
    {
        Output::$headers[] = $value;
    }
}

namespace Soosyze\Components\Util
{

    abstract class Input
    {
        /**
         * @var array
         */
        private static $iniGet;

        public static function reset(): void
        {
            self::$iniGet = [
                'upload_max_filesize' => '1 K',
                'post_max_size'       => '1 K',
                'memory_limit'        => '1 K'
            ];
        }

        public static function addIni(string $key, ?string $value): void
        {
            self::$iniGet[ $key ] = $value;
        }

        /**
         * @return string|false
         */
        public static function getIni(string $key)
        {
            return self::$iniGet[ $key ] ?? false;
        }
    }

    /**
     * @return string|false
     */
    function ini_get(string $option)
    {
        switch ($option) {
            case 'upload_max_filesize':
                return Input::getIni('upload_max_filesize');
            case 'post_max_size':
                return Input::getIni('post_max_size');
            case 'memory_limit':
                return Input::getIni('memory_limit');
            default:
                return false;
        }
    }
}
