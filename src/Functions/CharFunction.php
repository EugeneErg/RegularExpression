<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Functions\Contracts\ChildFunctionInterface;
use EugeneErg\RegularExpression\Functions\Contracts\ParentFunctionInterface;
use EugeneErg\RegularExpression\Functions\Traits\TraitSetChildren;
use EugeneErg\RegularExpression\Functions\Traits\TraitSetParent;

readonly class CharFunction implements ParentFunctionInterface, ChildFunctionInterface
{
    use TraitSetParent;
    use TraitSetChildren;

    public function __construct(public bool $negative)
    {
    }

    public function __toString(): string
    {
        return '['
            .($this->negative ? '^' : '')
            .implode('|', array_map(fn (ChildFunctionInterface $child) => (string) $child, $this->children))
            .']';
    }

    public function getMinLength(): int
    {
        return 1;
    }

    public function getMaxLength(): ?int
    {
        return 1;
    }

    public function jsonSerialize(): string
    {
        return $this->__toString();
    }

    public function generate(string $from, bool $negative): string
    {
        $key = array_rand($this->children);

        return $this->children[$key]->generate($from, $negative === $this->negative);
    }

    public static function fromArray(array $data): static
    {
        return new self($data['not'] ?? false);
    }
}
