<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Parser\ParserResult;

class CharFunction implements FunctionInterface, ParentFunctionInterface
{
    public readonly FunctionInterface $root;
    /** @var FunctionInterface[] */
    public readonly array $children;

    public function __construct(
        public readonly bool $not,
        public readonly ?FunctionInterface $parent = null,
        FunctionInterface ...$children,
    ) {
        $this->root = $this->parent?->getRoot() ?? $this;
        $this->children = $children;
    }

    public function getParent(): ?FunctionInterface
    {
        return $this->parent;
    }

    public function __toString(): string
    {
        return '['
            . ($this->not ? '^' : '')
            . implode('|', array_map(fn (FunctionInterface $child) => (string) $child, $this->children))
            . ']';
    }

    public function getRoot(): FunctionInterface
    {
        return $this->root;
    }

    public static function fromParseResult(
        array $options,
        ?FunctionInterface $parent = null,
        FunctionInterface ...$children,
    ): ParentFunctionInterface {
        return new self($options['not'] ?? false, $parent, ...$children);
    }

    public function getMinLength(): int
    {
        return 1;
    }

    public function getMaxLength(): ?int
    {
        return 1;
    }

    public function generate(string $from): string
    {
        $key = array_rand($this->children);

        return $this->children[$key]->generate($from);
    }

    public function jsonSerialize(): string
    {
        return $this->__toString();
    }

    public function getChildren(): array
    {
        return $this->children;
    }
}