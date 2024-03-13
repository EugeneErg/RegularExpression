<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression;

class Strings
{
    /** @var string[] */
    public readonly array $items;

    public function __construct(string... $values)
    {
        $this->items = $values;
    }
}
