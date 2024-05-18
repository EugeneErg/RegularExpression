<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

abstract readonly class AbstractGroupFunction extends AbstractStructureFunction
{
    public static function fromArray(array $data): static
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

    public function generate(string $from, bool $not): string
    {
        // TODO: Implement generate() method.
    }
}
