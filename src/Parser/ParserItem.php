<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Parser;

use EugeneErg\RegularExpression\RegularExpression;
use EugeneErg\RegularExpression\RegularExpressionException;

final class ParserItem
{
    /** @var ParserOption[] */
    public readonly array $options;

    private array $children;

    public function __construct(
        public readonly ?RegularExpression $begin = null,
        public readonly ?RegularExpression $end = null,
        ParserOption ...$options,
    ) {
        $this->options = $options;
    }

    /**
     * @throws RegularExpressionException
     */
    public static function group(string $begin, string $end, ParserOption ...$options): self
    {
        return new self(RegularExpression::fromPattern($begin), RegularExpression::fromPattern($end), ...$options);
    }

    /**
     * @throws RegularExpressionException
     */
    public static function equal(string $value): self
    {
        return self::options(new ParserOption(RegularExpression::fromPattern($value), fn () => []));
    }

    public static function options(ParserOption ...$options): self
    {
        return new self(null, null, ...$options);
    }

    public static function children(ParserItem ...$children): self
    {
        return (new self)->addChildren(...$children);
    }


    public function addChildren(ParserItem ...$children): self
    {
        $this->children = array_replace($this->children, $children);

        return $this;
    }

    /**
     * @return ParserItem[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }
}
