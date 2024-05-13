<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Functions\Contracts\ChildFunctionInterface;
use EugeneErg\RegularExpression\Functions\Traits\TraitGenerate;
use EugeneErg\RegularExpression\Functions\Traits\TraitSetParent;

class WhitespaceFunction implements ChildFunctionInterface
{
    use TraitGenerate;
    use TraitSetParent;

    public const NOT = 1;

    public const HORIZONTAL = 2;

    public const VERTICAL = 4;

    public const MAP = [
        self::HORIZONTAL => 'h',
        self::VERTICAL => 'v',
        self::HORIZONTAL | self::VERTICAL => 's',
        self::NOT | self::HORIZONTAL => 'H',
        self::NOT | self::VERTICAL => 'V',
        self::NOT | self::HORIZONTAL | self::VERTICAL => 'S',
    ];

    public function __construct(
        public readonly bool $horizontal,
        public readonly bool $vertical,
        public readonly bool $not,
    ) {
    }

    public function __toString(): string
    {
        return '\\'.self::MAP[
            ($this->horizontal ? self::HORIZONTAL : 0)
            | ($this->vertical ? self::VERTICAL : 0)
            | ($this->not ? self::NOT : 0)
            ];
    }

    public static function fromArray(array $data): static
    {
        return new self($data['horizontal'], $data['vertical'], $data['not']);
    }

    public function getMinLength(): int
    {
        return 1;
    }

    public function getMaxLength(): int
    {
        return 1;
    }

    public function jsonSerialize(): string
    {
        return $this->__toString();
    }
}
