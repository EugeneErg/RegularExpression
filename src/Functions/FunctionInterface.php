<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use JsonSerializable;
use Stringable;

interface FunctionInterface extends Stringable, JsonSerializable
{
    public function getChildren(): Functions;
    public function jsonSerialize(): array;
    public function getParent(): ?FunctionInterface;
    public function getOptions(): array;
}