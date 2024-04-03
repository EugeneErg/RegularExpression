<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Parser;

class StringParser
{
    public function __construct(private readonly ParserGroupItem $item)
    {
    }

    public function parse(string $subject): ?ParserResult
    {
        return (new ParserProcess($this->item, $subject))->result;
    }
}