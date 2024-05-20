<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Functions\Contracts\ChildFunctionInterface;
use EugeneErg\RegularExpression\Functions\Traits\TraitGenerate;
use EugeneErg\RegularExpression\Functions\Traits\TraitSetParent;

readonly class WordBoundaryFunction implements ChildFunctionInterface
{
    use TraitGenerate;
    use TraitSetParent;

    final public function __construct(public bool $negative)
    {
    }

    public function __toString(): string
    {
        return $this->negative ? '\\B' : '\\b';
    }

    /** @param array{not?: bool} $data */
    public static function fromArray(array $data): static
    {
        return new static($data['not'] ?? false);
    }

    public function getMinLength(): int
    {
        return 0;
    }

    public function getMaxLength(): ?int
    {
        return 0;
    }

    public function jsonSerialize(): string
    {
        return $this->__toString();
    }
}
