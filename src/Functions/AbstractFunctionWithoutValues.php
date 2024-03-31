<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

abstract class AbstractFunctionWithoutValues implements FunctionInterface
{
    public function __construct(public readonly ?FunctionInterface $parent)
    {
    }

    public function getChildren(): Functions
    {
        return new Functions();
    }

    public function getParent(): ?FunctionInterface
    {
        return $this->parent;
    }
}