<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Parser;

use EugeneErg\RegularExpression\RegularExpressionException;

class StringParser
{
    public function __construct(private readonly ParserItem $item)
    {
    }

    /**
     * @throws RegularExpressionException
     */
    public function parse(string $subject): ?ParserResult
    {
        return (new ParserProcess($this->item, $subject, 'structure'))->result;
    }
}
