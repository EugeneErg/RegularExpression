<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use JsonSerializable;
use Stringable;

interface FunctionInterface extends Stringable, JsonSerializable
{
    public function getMinLength(): int;
    public function getMaxLength(): ?int;
    public function generate(string $from, bool $not): string;
    public function jsonSerialize(): string;
    public function getRoot(): StructureFunction;
}
