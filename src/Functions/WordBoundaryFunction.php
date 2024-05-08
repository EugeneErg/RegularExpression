<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Functions\Contracts\ChildFunctionInterface;
use EugeneErg\RegularExpression\Functions\Traits\TraitGenerate;
use EugeneErg\RegularExpression\Functions\Traits\TraitSetParent;

class WordBoundaryFunction implements ChildFunctionInterface
{
    use TraitGenerate;
    use TraitSetParent;

    public function __construct(public readonly bool $not)
    {
    }

    public function __toString(): string
    {
        return $this->not ? '\\B' : '\\b';
    }

    public static function fromArray(array $data): static
    {
        return new self($data['not']);
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
