<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Functions\Contracts\ChildFunctionInterface;
use EugeneErg\RegularExpression\Functions\Traits\TraitGenerate;
use EugeneErg\RegularExpression\Functions\Traits\TraitSetParent;

readonly class WhitespaceFunction implements ChildFunctionInterface
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

    final public function __construct(
        public bool $horizontal,
        public bool $vertical,
        public bool $negative,
    ) {
    }

    public function __toString(): string
    {
        return '\\'.self::MAP[
            ($this->horizontal ? self::HORIZONTAL : 0)
            | ($this->vertical ? self::VERTICAL : 0)
            | ($this->negative ? self::NOT : 0)
            ];
    }

    /**
     * @param array{horizontal: bool, vertical: bool, not: bool} $data
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new static($data['horizontal'], $data['vertical'], $data['not']);
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
