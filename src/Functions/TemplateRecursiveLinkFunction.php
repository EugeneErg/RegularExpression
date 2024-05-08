<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Parser\ParserResult;

class TemplateRecursiveLinkFunction implements FunctionInterface
{
    public readonly FunctionInterface $root;

    public function __construct(
        public readonly ?FunctionInterface $parent = null,
    ) {
        $this->root = $this->parent?->getRoot() ?? $this;
    }

    public function getParent(): ?FunctionInterface
    {
        return $this->parent;
    }

    public function getRoot(): FunctionInterface
    {
        return $this->root;
    }

    public function __toString(): string
    {
        return '(?R)';
    }

    public static function fromArray(array $data, ?FunctionInterface $parent = null): FunctionInterface
    {
        return new self($parent);
    }

    public function getMinLength(): int
    {
        //todo
    }

    public function getMaxLength(): ?int
    {
        //todo
    }

    public function generate(string $from): string
    {
        return $this->root->generate($from);
    }

    public function jsonSerialize(): string
    {
        return $this->__toString();
    }
}