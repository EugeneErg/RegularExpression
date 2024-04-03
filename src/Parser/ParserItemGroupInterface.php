<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Parser;

use EugeneErg\RegularExpression\RegularExpression;

interface ParserItemGroupInterface extends ParserItemInterface
{
    public function getBegin(): RegularExpression;
    public function getEnd(): RegularExpression;
    /** @return array<string, ParserItemInterface> */
    public function getChildren(): array;
}
