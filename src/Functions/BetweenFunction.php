<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Functions\Contracts\ChildFunctionInterface;
use EugeneErg\RegularExpression\Functions\Traits\TraitGenerate;
use EugeneErg\RegularExpression\Functions\Traits\TraitSetParent;

class BetweenFunction implements ChildFunctionInterface
{
    use TraitGenerate;
    use TraitSetParent;

    public function __construct(
        public readonly string $from,
        public readonly string $to,
    ) {
    }

    public function __toString(): string
    {
        return $this->from.'-'.$this->to;
    }

    public static function fromArray(array $data): static
    {
        return new self($data['from'], $data['to']);
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
