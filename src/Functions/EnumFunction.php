<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Functions\Contracts\ChildFunctionInterface;
use EugeneErg\RegularExpression\Functions\Contracts\ParentFunctionInterface;
use EugeneErg\RegularExpression\Functions\Traits\TraitSetChildren;
use EugeneErg\RegularExpression\Functions\Traits\TraitSetParent;

readonly class EnumFunction implements ParentFunctionInterface, ChildFunctionInterface
{
    use TraitSetParent;
    use TraitSetChildren;

    final public function __construct()
    {
    }

    public function __toString(): string
    {
        return implode('|', array_map(fn (ChildFunctionInterface $child) => (string) $child, $this->getChildren()));
    }

    /** @param array{} $data */
    public static function fromArray(array $data): static
    {
        return new static();
    }

    /** @inheritdoc */
    public function getMinLength(): int
    {
        $result = null;

        foreach ($this->getChildren() as $child) {
            $childMinValue = $child->getMinLength();

            if ($childMinValue === 0) {
                return 0;
            }

            $result = min($result ?? $childMinValue, $childMinValue);
        }

        return $result ?? 0;
    }

    public function getMaxLength(): ?int
    {
        $result = 0;

        foreach ($this->getChildren() as $child) {
            $childMinValue = $child->getMaxLength();

            if ($childMinValue === null) {
                return null;
            }

            $result = max($result, $childMinValue);
        }

        return $result;
    }

    public function generate(string $from, bool $not): string
    {
        $children = $this->getChildren();

        if ($children === []) {
            return '';
        }

        $number = array_rand($children);

        return $children[$number]->generate($from, $not);
    }

    public function jsonSerialize(): string
    {
        return $this->__toString();
    }
}
