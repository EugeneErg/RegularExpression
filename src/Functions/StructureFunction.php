<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Functions\Contracts\FunctionInterface;

class StructureFunction extends GroupFunction
{
    public function __construct(int $addModifiers = 0)
    {
        parent::__construct(0, null, $addModifiers);
    }

    public function __toString(): string
    {

    }

    public function getMinLength(): int
    {
        // TODO: Implement getMinLength() method.
    }

    public function getMaxLength(): ?int
    {
        // TODO: Implement getMaxLength() method.
    }

    public function generate(string $from, bool $not): string
    {
        // TODO: Implement generate() method.
    }

    public function jsonSerialize(): string
    {
        // TODO: Implement jsonSerialize() method.
    }

    public function getParent(): ?ParentFunctionInterface
    {
        // TODO: Implement getParent() method.
    }

    public function getRoot(): FunctionInterface
    {
        // TODO: Implement getRoot() method.
    }

    public function getChildren(): array
    {
        // TODO: Implement getChildren() method.
    }

    public static function fromArray(array $data, ?ParentFunctionInterface $parent = null, FunctionInterface ...$children,): static
    {
        // TODO: Implement fromArray() method.
    }
}