<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

interface FunctionWithParentInterface extends FunctionInterface
{
    public function getRoot(): self;
    public function getParent(): ?self;
    public static function fromParseResult(array $options, ?self $parent = null): self;
}