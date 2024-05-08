<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Functions\Contracts\ChildFunctionInterface;
use EugeneErg\RegularExpression\Functions\Traits\TraitSetParent;

class StringFunction implements ChildFunctionInterface
{
    use TraitSetParent;

    public function __construct(public readonly string $value)
    {
    }

    public function __toString(): string
    {
        return preg_quote($this->value);
    }

    public function getMinLength(): int
    {
        return strlen($this->value);
    }

    public function getMaxLength(): int
    {
        return strlen($this->value);
    }

    public function generate(string $from, bool $not): string
    {
        return $this->value;
    }

    public function jsonSerialize(): string
    {
        return $this->__toString();
    }

    public static function fromArray(array $data): static
    {
        return new self($data['value'],);
    }
}