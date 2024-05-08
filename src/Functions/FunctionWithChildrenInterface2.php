<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

interface FunctionWithChildrenInterface extends FunctionInterface
{
    /** @return FunctionInterface[] */
    public function getChildren(): array;
}
