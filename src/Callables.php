<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression;

class Callables
{
    /** @var array<callable> */
    public readonly array $items;

    public function __construct(callable ...$callables)
    {
        $this->items = $callables;
    }
}
