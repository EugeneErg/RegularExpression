<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

interface ParentFunctionInterface extends FunctionInterface
{
    /** @return FunctionInterface[] */
    public function getChildren(): array;
    public static function fromParseResult(
        array $options,
        ?FunctionInterface $parent = null,
        FunctionInterface ...$children,
    ): self;
}