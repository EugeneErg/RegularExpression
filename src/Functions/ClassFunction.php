<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Functions\Contracts\FunctionInterface;
use EugeneErg\RegularExpression\Functions\Traits\TraitGenerate;
use EugeneErg\RegularExpression\Functions\Traits\TraitSetParent;

readonly class ClassFunction implements FunctionInterface
{
    use TraitGenerate;
    use TraitSetParent;

    public function __construct(
        public bool $negative,
        public string $value,
    ) {
    }

    public function __toString(): string
    {
        return '['.($this->negative ? '^' : '').':'.$this->value.':]';
    }

    public static function fromArray(array $data): static
    {
        return new self($data['not'], $data['value']);
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
}
