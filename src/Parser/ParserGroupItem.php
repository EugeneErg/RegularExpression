<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Parser;

use EugeneErg\RegularExpression\RegularExpression;
use EugeneErg\RegularExpression\RegularExpressionException;

final class ParserGroupItem implements ParserItemInterface, ParserItemGroupInterface
{
    /** @var ParserOption[] */
    private readonly array $options;

    private array $children;

    public function __construct(
        private readonly RegularExpression $begin,
        private readonly RegularExpression $end,
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
            new RegularExpression('{', $begin, '}', ),
            new RegularExpression('{', $end, '}', ),
            ...$options,
        );
    }


    public function addChildren(ParserItemInterface ...$children): self
    {
        $this->children = array_replace($this->children, $children);

        return $this;
    }

    /** @inheritDoc */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function getBegin(): RegularExpression
    {
        return $this->begin;
    }

    public function getEnd(): RegularExpression
    {
        return $this->end;
    }

    /** @inheritDoc */
    public function getOptions(): array
    {
        return $this->options;
    }
}
