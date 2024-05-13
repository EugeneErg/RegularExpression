<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Functions\Contracts\ParentFunctionInterface;
use EugeneErg\RegularExpression\Functions\Traits\TraitSetChildren;

abstract class AbstractStructure implements ParentFunctionInterface
{
    use TraitSetChildren;

    public function __construct(
        public readonly int $number,
        public readonly ?string $name = null,
        public readonly int $addModifiers = 0,
        public readonly int $subModifiers = 0,
        public readonly bool $not = false,
        public readonly ?bool $direction = null,
        public readonly GroupFunction\Type $type = GroupFunction\Type::Group,
        public readonly bool $once = false,
    ) {
    }

    public function __toString(): string
    {
        if ($this->type !== GroupFunction\Type::Enum) {
        }
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

    public static function fromArray(array $data): static
    {
        // TODO: Implement fromArray() method.
    }
}
