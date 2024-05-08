<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Functions\Contracts\FunctionInterface;

class Functions
{
    /** @var array<FunctionInterface|string> */
    public readonly array $items;

    public function __construct(FunctionInterface|string ...$functions)
    {
        $this->items = $functions;
    }
}