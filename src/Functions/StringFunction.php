<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Parser\ParserResult;

class StringFunction implements FunctionInterface
{
    public readonly FunctionInterface $root;

    public function __construct(
        public readonly string $value,
        public readonly ?FunctionInterface $parent = null,
    ) {
        $this->root = $this->parent?->getRoot() ?? $this;
    }

    public function __toString(): string
    {
        return preg_quote($this->value);
    }

    public function getMinLength(): int
    {
        return strlen($this->value);
    }

    public function getMaxLength(): int
    {
        return strlen($this->value);
    }

    public function generate(string $from): string
    {
        return $this->value;
    }

    public function jsonSerialize(): string
    {
        return $this->__toString();
    }

    public static function fromParseResult(array $options, ?FunctionInterface $parent = null): FunctionInterface
    {
        return new self($options['value'], $parent);
    }

    public function getRoot(): FunctionInterface
    {
        return $this->root;
    }

    public function getParent(): ?FunctionInterface
    {
        return $this->parent;
    }
}