<?php

namespace Soosyze\Tests\Traits;

trait DateTime
{
    protected static function dateCreate(string $date): \DateTime
    {
        $dateTime = date_create($date);
        if ($dateTime === false) {
            throw new \InvalidArgumentException('The date must be in valid format');
        }

        return $dateTime;
    }
}
