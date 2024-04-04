<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Parser;

final class ParserResult
{
    /**
     * @param ParserOption[] $options
     * @param array<string, ParserItem> $children
     */
    public function __construct(
        public readonly string $name,
        public readonly array $options = [],
        public readonly array $children = [],
    ) {
    }
}