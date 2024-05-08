<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Functions\Contracts\ChildFunctionInterface;
use EugeneErg\RegularExpression\Functions\Contracts\ParentFunctionInterface;
use EugeneErg\RegularExpression\Functions\Traits\TraitChildren;
use EugeneErg\RegularExpression\Functions\Traits\TraitParent;

class CharFunction implements ParentFunctionInterface, ChildFunctionInterface
{
    use TraitParent;
    use TraitChildren;

    public function __construct(public readonly bool $not)
    {
    }

    public function __toString(): string
    {
        return '['
            . ($this->not ? '^' : '')
            . implode('|', array_map(fn (ChildFunctionInterface $child) => (string) $child, $this->children))
            . ']';
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

    public function getChildren(): array
    {
        return $this->children;
    }

    public function generate(string $from): string
    {
        $key = array_rand($this->children);

        return $this->children[$key]->generate($from);
    }

    public static function fromArray(
        array                                    $data,
        ?FunctionWithChildrenWithParentInterface $parent = null,
        FunctionInterface ...$children,
    ): static {
        return new self($data['not'] ?? false, $parent, ...$children);
    }
}
