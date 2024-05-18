<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Functions\Contracts\FunctionInterface;

readonly class Functions
{
    /** @var array<FunctionInterface|string> */
    public array $items;

    public function __construct(FunctionInterface|string ...$functions)
    {
        $this->items = $functions;
    }
}
