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


    public function addChildren(ParserItem ...$children): self
    {
        $this->children = array_replace($this->children, $children);

        return $this;
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
    public static function equal(string $value): self
    {
        return self::options(new ParserOption(new RegularExpression('{', $value, '}', RegularExpression::PCRE_INFO_JCHANGED), fn () => []));
    }

    public static function options(ParserOption ...$options): self
    {
        return new self(null, null, ...$options);
    }

    public static function children(ParserItem ...$children): self
    {
        return (new self())->addChildren(...$children);
    }
}
