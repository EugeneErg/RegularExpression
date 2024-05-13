<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions\Contracts;

interface ParentFunctionInterface extends FunctionInterface
{
    /** @return ChildFunctionInterface[] */
    public function getChildren(): array;

    public function addChild(ChildFunctionInterface $child): void;
}
