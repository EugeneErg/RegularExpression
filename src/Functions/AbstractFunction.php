<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

abstract class AbstractFunction implements FunctionInterface
{
    public function __construct(public readonly ?FunctionInterface $parent, public readonly Functions $values)
    {
    }

    public static function fromValues(?FunctionInterface $parent, FunctionInterface|string ...$values): static
    {
        return new static($parent, new Functions(...$values));
    }

    public function getChildren(): Functions
    {
        return $this->values;
    }

    public function getParent(): ?FunctionInterface
    {
        return $this->parent;
    }
}