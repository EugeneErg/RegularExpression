<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression;

class ResultCount
{
    public function __construct(
        public readonly int $count,
        public readonly string $value,
    ) {
    }
}