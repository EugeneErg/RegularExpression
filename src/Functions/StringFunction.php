<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Functions\Contracts\ChildFunctionInterface;
use EugeneErg\RegularExpression\Functions\Traits\TraitSetParent;

readonly class StringFunction implements ChildFunctionInterface
{
    use TraitSetParent;

    final public function __construct(public string $value)
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

    public function generate(string $from, bool $negative): string
    {
        return $this->value;
    }

    public function jsonSerialize(): string
    {
        return $this->__toString();
    }

    /** @param array{value: string} $data */
    public static function fromArray(array $data): static
    {
        return new static($data['value']);
    }
}
