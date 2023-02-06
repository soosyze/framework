<?php

namespace Soosyze\Components\Validator\Comparators;

class MinMax
{
    /** @var numeric|null */
    private $comparatorMax;

    /** @var numeric|null */
    private $comparatorMin;

    /** @var numeric */
    private $valueMax;

    /** @var numeric */
    private $valueMin;

    /**
     * @param numeric      $valueMax
     * @param numeric      $valueMin
     * @param numeric|null $comparatorMin
     * @param numeric|null $comparatorMax
     */
    private function __construct(
        $valueMin,
        $valueMax,
        $comparatorMin = null,
        $comparatorMax = null
    ) {
        $this->valueMin = $valueMin;
        $this->valueMax = $valueMax;
        $this->comparatorMin = $comparatorMin;
        $this->comparatorMax = $comparatorMax;
    }

    /**
     * @param mixed   $valueMax
     * @param mixed   $valueMin
     * @param numeric $comparatorMin
     * @param numeric $comparatorMax
     */
    public static function create(
        $valueMin,
        $valueMax,
        $comparatorMin = null,
        $comparatorMax = null
    ): self {
        if (!is_numeric($valueMin)) {
            throw new \InvalidArgumentException('The minimum value of between must be numeric.');
        }
        if (!is_numeric($valueMax)) {
            throw new \InvalidArgumentException('The maximum value of entry must be numeric.');
        }
        if ($valueMin > $valueMax) {
            throw new \InvalidArgumentException('The minimum value must not be greater than the maximum value.');
        }

        return new self(
            $valueMin,
            $valueMax,
            $comparatorMin,
            $comparatorMax
        );
    }

    /**
     * @return numeric
     */
    public function getValueMin()
    {
        return $this->valueMin;
    }

    /**
     * @return numeric
     */
    public function getValueMax()
    {
        return $this->valueMax;
    }

    /**
     * @return numeric|null
     */
    public function getComparatorMin()
    {
        return $this->comparatorMin;
    }

    /**
     * @return numeric|null
     */
    public function getComparatorMax()
    {
        return $this->comparatorMax;
    }
}
