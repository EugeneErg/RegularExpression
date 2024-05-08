<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

class TemplateRecursiveLinkFunction implements FunctionWithParentInterface
{
    public readonly FunctionWithParentInterface $root;
    public readonly ?FunctionWithChildrenInterface $parent;

    public function __construct()
    {
        $this->root = $this->parent?->getRoot() ?? $this;
    }

    public function getParent(): ?FunctionWithChildrenInterface
    {
        return $this->parent;
    }

    public function getRoot(): FunctionWithParentInterface
    {
        return $this->root;
    }

    public function __toString(): string
    {
        return '(?R)';
    }

    public static function fromArray(array $data, ?FunctionWithChildrenInterface $parent = null): static
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