<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Functions\Traits\TraitGenerate;

class AnyFunction implements FunctionInterface
{
    use TraitGenerate;

    public readonly FunctionInterface $root;

    public function __construct(
        public readonly ?FunctionWithChildrenWithParentInterface $parent,
    ) {
        $this->root = $this->parent?->getRoot() ?? $this;
    }

    public function __toString(): string
    {
        return '.';
    }

    public function getRoot(): FunctionInterface
    {
        return $this->root;
    }

    public function getParent(): ?FunctionWithChildrenWithParentInterface
    {
        return $this->parent;
    }

    public static function fromArray(array $data, ?FunctionInterface $parent = null): static
    {
        return new self($parent);
    }

    public function getMinLength(): int
    {
        return 0;
    }

    public function getMaxLength(): ?int
    {
        return 0;
    }

    public function jsonSerialize(): string
    {
        return $this->__toString();
    }
}