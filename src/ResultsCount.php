<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression;

class ResultsCount
{
    public function __construct(
        public readonly int $count,
        public readonly Strings $values,
    ) {
    }
}
