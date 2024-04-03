<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Parser;

interface ParserItemInterface
{
    /** @return ParserOption[] */
    public function getOptions(): array;
}