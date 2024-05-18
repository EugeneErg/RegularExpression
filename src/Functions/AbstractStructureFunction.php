<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Functions\Contracts\ParentFunctionInterface;
use EugeneErg\RegularExpression\Functions\Traits\TraitSetChildren;

abstract readonly class AbstractStructureFunction implements ParentFunctionInterface
{
    use TraitSetChildren;

    public function __construct(
        public int $number,
        public ?string $name = null,
        public int $addModifiers = 0,
        public int $subModifiers = 0,
        public bool $not = false,
        public ?bool $direction = null,
        public bool $once = false,
    ) {
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

    }

    /** @param array{} $data todo */
    abstract public static function fromArray(array $data): static;
}
