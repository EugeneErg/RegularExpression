<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

class Functions
{
    /** @var array<FunctionInterface|string> */
    public readonly array $items;

    public function __construct(FunctionInterface|string ...$functions)
    {
        $this->items = $functions;
    }
}