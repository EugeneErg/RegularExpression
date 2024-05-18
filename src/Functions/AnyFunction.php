<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Functions\Contracts\ChildFunctionInterface;
use EugeneErg\RegularExpression\Functions\Traits\TraitGenerate;
use EugeneErg\RegularExpression\Functions\Traits\TraitSetParent;

readonly class AnyFunction implements ChildFunctionInterface
{
    use TraitGenerate;
    use TraitSetParent;

    final public function __toString(): string
    {
        return '.';
    }

    /** @param array{} $data */
    public static function fromArray(array $data): static
    {
        return new static();
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
