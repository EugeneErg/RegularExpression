<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Parser;

use EugeneErg\RegularExpression\RegularExpression;

final class ParserOption
{
    /**
     * @var callable(string[], int, string, array<string, mixed>): array<string, mixed>
     */
    public readonly mixed $callback;

    /**
     * @param RegularExpression $regularExpression
     * @param callable(string[] $match, int $position, string $name, array<string, mixed> $parentOptions): array<string, mixed> $callback
     */
    public function __construct(public RegularExpression $regularExpression, callable $callback)
    {
        $this->callback = $callback;
    }
}
