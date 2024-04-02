<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Parser;

use EugeneErg\RegularExpression\RegularExpression;
use EugeneErg\RegularExpression\RegularExpressionException;

final class ParserOption
{
    /**
     * @var callable(array $match): array $callback
     */
    public readonly array|string|object $callback;

    /**
     * @param RegularExpression $regularExpression
     * @param callable(array $match): array $callback
     */
    public function __construct(public RegularExpression $regularExpression, callable $callback)
    {
        $this->callback = $callback;
    }

    /**
     * @throws RegularExpressionException
     */
    public static function new(string|RegularExpression $pattern, array|callable $callback): self
    {
        return new self(
            $pattern instanceof RegularExpression ? $pattern : RegularExpression::fromPattern($pattern),
            is_callable($callback) ? $callback : fn () => $callback,
        );
    }

    public static function match(string|RegularExpression $pattern, string ...$fields): self
    {
        return new self(
            $pattern instanceof RegularExpression ? $pattern : RegularExpression::fromPattern($pattern),
            function (array $match) use ($fields): array {
                $result = [];

                foreach ($fields as $field) {
                    $result[$field] = $match[$field] ?? null;
                }

                return $result;
            }
        );
    }
}
