<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression;

class OffsetCapture
{
    public function __construct(
        public readonly string $value,
        public readonly int $position,
    ) {
    }
}