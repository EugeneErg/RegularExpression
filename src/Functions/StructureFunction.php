<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Functions\GroupFunction\Type;

class StructureFunction implements FunctionWithChildrenInterface
{
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

    public function getParent(): ?FunctionWithChildrenInterface
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

    public static function fromArray(array $data, ?FunctionWithChildrenInterface $parent = null, FunctionInterface ...$children,): static
    {
        // TODO: Implement fromArray() method.
    }
}