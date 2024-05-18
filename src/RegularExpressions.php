<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression;

use ArgumentCountError;
use Closure;

readonly class RegularExpressions
{
    /** @var RegularExpression[] */
    public array $items;

    public function __construct(RegularExpression ...$regularExpressions)
    {
        $this->items = $regularExpressions;
    }

    public function replace(
        string $subject,
        string|Strings $replacement,
        ?int $limit = null,
        bool $filter = false,
    ): ResultCount {
        /** @var string $result */
        [$result, $count] = $this->replaceSome($subject, $replacement, $limit, $filter);

        return new ResultCount($count, $result);
    }

    public function replaceCallback(
        string $subject,
        callable|Callables $replacement,
        ?int $limit = null,
        bool $offsetCapture = false,
    ): ResultCount {
        /** @var string $result */
        [$result, $count] = $this->replaceSomeCallback($subject, $replacement, $limit, $offsetCapture);

        return new ResultCount($count, $result);
    }

    public function multiReplace(
        Strings $subject,
        string|Strings $replacement,
        ?int $limit = null,
        bool $filter = false,
    ): ResultsCount {
        /** @var string[] $result */
        [$result, $count] = $this->replaceSome($subject, $replacement, $limit, $filter);

        return new ResultsCount($count, new Strings(...$result));
    }

    /**
     * @param callable(array $match): string|Callables<callable(array $match): string> $replacement
     */
    public function multiReplaceCallback(
        Strings $subject,
        callable|Callables $replacement,
        ?int $limit = null,
        bool $offsetCapture = false,
    ): ResultsCount {
        /** @var string[] $result */
        [$result, $count] = $this->replaceSomeCallback($subject, $replacement, $limit, $offsetCapture);

        return new ResultsCount($count, new Strings(...$result));
    }

    /** @return array{0: string|string[], 1: int} */
    private function replaceSome(
        string|Strings $subject,
        string|Strings $replacement,
        ?int $limit,
        bool $filter,
    ): array {
        $result = $filter
            ? preg_filter(
                array_map(fn (RegularExpression $value): string => (string) $value, $this->items),
                $replacement instanceof Strings ? $replacement->items : $replacement,
                $subject instanceof Strings ? $subject->items : $subject,
                $limit ?? -1,
                $count,
            )
            : preg_replace(
                array_map(fn (RegularExpression $value): string => (string) $value, $this->items),
                $replacement instanceof Strings ? $replacement->items : $replacement,
                $subject instanceof Strings ? $subject->items : $subject,
                $limit ?? -1,
                $count,
            );

        /** @var string|string[] $result */
        return [$result, $count];
    }

    /**
     * @param callable(array<string|OffsetCapture> $match): string|Callables<callable(array<string|OffsetCapture> $match): string> $replacement
     * @return array{0: string|string[], 1: int}
     */
    private function replaceSomeCallback(
        string|Strings $subject,
        callable|Callables $replacement,
        ?int $limit,
        bool $offsetCapture,
    ): array {
        if ($replacement instanceof Callables) {
            if (count($replacement->items) !== count($this->items)) {
                throw new ArgumentCountError('The number of elements in replacement does not match the number of patterns.', 0);
            }

            $patterns = array_combine(
                array_map(fn (RegularExpression $value): string => (string) $value, $this->items),
                array_map(
                    /** @param callable(array<string|OffsetCapture> $match): string $value */
                    fn (callable $value): Closure => $offsetCapture
                        ? fn (array $match): string => $value($this->prepareMatchOffsetCapture($match))
                        : fn (array $match): string => $value($this->prepareMatch($match)),
                    $replacement->items,
                ),
            );

            $result = preg_replace_callback_array(
                $patterns,
                $subject instanceof Strings ? $subject->items : $subject,
                $limit ?? -1,
                $count,
                $offsetCapture ? PREG_OFFSET_CAPTURE : 0,
            );
        } else {
            $result = preg_replace_callback(
                array_map(fn (RegularExpression $value): string => (string) $value, $this->items),
                $offsetCapture
                    ? fn (array $match): string => $replacement($this->prepareMatchOffsetCapture($match))
                    : fn (array $match): string => $replacement($this->prepareMatch($match)),
                $subject instanceof Strings ? $subject->items : $subject,
                $limit ?? -1,
                $count,
                $offsetCapture ? PREG_OFFSET_CAPTURE : 0,
            );
        }

        /** @var string|string[] $result */
        return [$result, $count];
    }

    /**
     * @param array<string|null> $match
     * @return string[]
     */
    private function prepareMatch(array $match): array
    {
        return array_filter($match, fn (?string $value): bool => $value !== null);
    }

    /**
     * @param array<string|null>[] $match
     * @return OffsetCapture[]
     */
    private function prepareMatchOffsetCapture(array $match): array
    {
        $match = array_filter($match, fn (array $value): bool => $value[0] !== null);

        /** @var string[][] $match */
        array_walk($match, function (array &$value): void {
            $value = new OffsetCapture(...$value);
        });

        /** @var OffsetCapture[] $match */
        return $match;
    }
}
