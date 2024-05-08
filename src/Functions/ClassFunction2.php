<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Functions\Traits\TraitGenerate;

class ClassFunction implements FunctionInterface
{
    use TraitGenerate;

    public readonly FunctionInterface $root;

    public function __construct(
        public readonly bool                                     $not,
        public readonly string                                   $value,
        public readonly ?FunctionWithChildrenWithParentInterface $parent,
    ) {
        $this->root = $parent?->getRoot() ?? $this;
    }

    public function __toString(): string
    {
        return '[' . ($this->not ? '^' : '') . ':' . $this->value . ':]';
    }

    public static function fromArray(array $data, ?FunctionWithChildrenWithParentInterface $parent = null): static
    {
        return new self($data['not'], $data['value'], $parent);
    }

    public function getMinLength(): int
    {
        return 1;
    }

    public function getMaxLength(): ?int
    {
        return 1;
    }

    public function jsonSerialize(): string
    {
        return $this->__toString();
    }

    public function getParent(): ?FunctionWithChildrenWithParentInterface
    {
        return $this->parent;
    }

    public function getRoot(): FunctionInterface
    {
        return $this->root;
    }
}