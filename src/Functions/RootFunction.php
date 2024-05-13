<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Functions\Contracts\RootFunctionInterface;

class RootFunction extends AbstractStructure implements RootFunctionInterface
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

    public function getRoot(): RootFunctionInterface
    {
        return $this;
    }

    public static function fromArray(array $data): static
    {
        // TODO: Implement fromArray() method.
    }
}
