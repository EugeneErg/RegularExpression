<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

interface FunctionWithChildrenInterface extends FunctionInterface
{
    /** @return FunctionWithParentInterface[] */
    public function getChildren(): array;
}
