<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression;

/**
 * @template TValue
 */
readonly class Callables
{
    /** @var TValue[] */
    public array $items;

    /**
     * @param TValue[] $callables
     * @phpstan-ignore-next-line
     */
    public function __construct(callable ...$callables)
    {
        /** @var TValue[] $callables */
        $this->items = $callables;
    }
}
