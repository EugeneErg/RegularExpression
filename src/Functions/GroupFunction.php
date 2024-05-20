<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Functions\Contracts\ChildFunctionInterface;
use EugeneErg\RegularExpression\Functions\Traits\TraitSetParent;

readonly class GroupFunction extends AbstractStructureFunction implements ChildFunctionInterface
{
    use TraitSetParent;

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

    public function generate(string $from, bool $negative): string
    {
        // TODO: Implement generate() method.
    }
}
