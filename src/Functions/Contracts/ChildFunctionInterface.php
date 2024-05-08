<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions\Contracts;

interface ChildFunctionInterface extends FunctionInterface
{
    public function getParent(): ParentFunctionInterface;
    public function setParent(ParentFunctionInterface $parent): void;
}
