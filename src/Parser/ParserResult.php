<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Parser;

final class ParserResult
{
    /**
     * @param ParserOption[] $options
     * @param array<string, ParserItemInterface> $children
     */
    public function __construct(public readonly array $options = [], public readonly array $children = [])
    {
    }
}