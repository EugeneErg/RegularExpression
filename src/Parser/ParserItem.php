<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Parser;

use EugeneErg\RegularExpression\RegularExpression;
use EugeneErg\RegularExpression\RegularExpressionException;

final class ParserItem
{
    /** @var ParserOption[] */
    private readonly array $options;

    private array $children = [];

    public function __construct(
        private readonly ?RegularExpression $begin = null,
        private readonly ?RegularExpression $end = null,
        ParserOption ...$options,
    ) {
        $this->options = $options;
    }

    public static function fromArray(array $data): self
    {
        $result = new self(
            self::regularExpressionFromArray($data['begin'] ?? null),
            self::regularExpressionFromArray($data['end'] ?? null),
            ...self::optionsFromArray((array) ($data['options'] ?? [])),
        );

        if (isset($data['children'])) {
            foreach ($data['children'] as $name => $child) {
                $result->addChildren(...[$name => self::fromArray($child)]);
            }
        }

        return $result;
    }

    /**
     * @throws RegularExpressionException
     */
    public static function group(string $begin, string $end, ParserOption ...$options): self
    {
        return new self(
            new RegularExpression('{', $begin, '}', RegularExpression::PCRE_INFO_JCHANGED),
            new RegularExpression('{', $end, '}', RegularExpression::PCRE_INFO_JCHANGED),
            ...$options,
        );
    }

    public function addChildren(self ...$children): self
    {
        $this->children = array_replace($this->children, $children);

        return $this;
    }

    private static function regularExpressionFromArray(null|string|RegularExpression $value): ?RegularExpression
    {
        return is_string($value)
            ? new RegularExpression('{', $value, '}', RegularExpression::PCRE_INFO_JCHANGED)
            : $value;
    }

    /**
     * @param array<string, callable|mixed[]> $options
     * @return ParserOption[]
     */
    private static function optionsFromArray(array $options): array
    {
        $result = [];

        foreach ($options as $pattern => $option) {
            if (is_int($pattern) && is_string($option)) {
                $pattern = $option;
                $option = [];
            }

            $result[] = new ParserOption(
                self::regularExpressionFromArray($pattern),
                is_callable($option) ? $option : function (array $match) use ($option): array {
                    $result = [];

                    foreach ((array) $option as $field => $value) {
                        if (is_int($field)) {
                            $field = $value;
                            $value = $match[$value] ?? null;
                        }

                        $result[$field] = $value;
                    }

                    return $result;
                },
            );
        }

        return $result;
    }

    /**
     * @return array<string, ParserItem>
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function getBegin(): ?RegularExpression
    {
        return $this->begin;
    }

    public function getEnd(): ?RegularExpression
    {
        return $this->end;
    }

    /** @return ParserOption[] */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @throws RegularExpressionException
     */
    public static function equal(string $value, callable $callback = null): self
    {
        return self::options(new ParserOption(
            new RegularExpression('{', $value, '}', RegularExpression::PCRE_INFO_JCHANGED),
            $callback ?? fn () => [],
        ));
    }

    public static function options(ParserOption ...$options): self
    {
        return new self(null, null, ...$options);
    }

    public static function children(self ...$children): self
    {
        return (new self())->addChildren(...$children);
    }
}
