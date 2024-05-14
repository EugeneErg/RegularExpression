<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Parser;

final readonly class ParserResult
{
    /**
     * @param array<string, mixed> $options
     * @param array<int, ParserResult> $children
     */
    public function __construct(
        public string $name,
        public array $options = [],
        public array $children = [],
    ) {
    }
}
