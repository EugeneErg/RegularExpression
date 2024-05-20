<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Functions\Contracts\ChildFunctionInterface;
use EugeneErg\RegularExpression\Functions\Traits\TraitGenerate;
use EugeneErg\RegularExpression\Functions\Traits\TraitSetParent;

readonly class DecimalFunction implements ChildFunctionInterface
{
    use TraitGenerate;
    use TraitSetParent;

    public const NOT = 1;

    public const MAP = [self::NOT => 'D', 0 => 'd'];

    public function __construct(
        public bool $negative,
    ) {
    }

    public function __toString(): string
    {
        return '\\'.self::MAP[$this->negative ? self::NOT : 0];
    }

    public static function fromArray(array $data): static
    {
        return new self($data['not'] ?? false);
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
