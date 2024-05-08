<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

class ClassFunction implements FunctionInterface
{
    public function __construct()
    {
    }

    public function __toString(): string
    {
        // TODO: Implement __toString() method.
    }

    public static function fromArray(array $data, ?FunctionWithChildrenInterface $parent = null): static
    {
        // TODO: Implement fromArray() method.
    }

    public function getMinLength(): int
    {
        // TODO: Implement getMinLength() method.
    }

    public function getMaxLength(): ?int
    {
        // TODO: Implement getMaxLength() method.
    }

    public function generate(string $from): string
    {
        // TODO: Implement generate() method.
    }

    public function jsonSerialize(): string
    {
        // TODO: Implement jsonSerialize() method.
    }

    public function getParent(): ?FunctionWithChildrenInterface
    {
        // TODO: Implement getParent() method.
    }

    public function getRoot(): FunctionInterface
    {
        // TODO: Implement getRoot() method.
    }
}