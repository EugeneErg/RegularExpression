<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression;

readonly class ResultCount
{
    public function __construct(
        public int $count,
        public string $value,
    ) {
    }
}
