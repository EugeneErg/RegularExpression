<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Functions\Contracts\ChildFunctionInterface;
use EugeneErg\RegularExpression\Functions\Contracts\FunctionInterface;
use EugeneErg\RegularExpression\Functions\Traits\TraitSetParent;

readonly class HexFunction implements ChildFunctionInterface
{
    use TraitSetParent;

    public function __construct(public string $value)
    {
    }

    public function __toString(): string
    {
        return '\\x'.(strlen($this->value) > 2 ? '{'.$this->value.'}' : $this->value);
    }

    public static function fromArray(array $data): static
    {
        return new self($data['value']);
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

    public function generate(string $from, bool $not): string
    {
        //todo not
        return json_decode('"'.$this->value.'"');
    }
}
