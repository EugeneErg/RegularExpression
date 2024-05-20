<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Functions\Contracts\ChildFunctionInterface;
use EugeneErg\RegularExpression\Functions\Contracts\ParentFunctionInterface;
use EugeneErg\RegularExpression\Functions\Traits\TraitSetChildren;
use EugeneErg\RegularExpression\Functions\Traits\TraitSetParent;

readonly class ConditionFunction implements ChildFunctionInterface, ParentFunctionInterface
{
    use TraitSetChildren;
    use TraitSetParent;

    final public function __construct()
    {
    }

    /** @param array{} $data */
    public static function fromArray(array $data): static
    {
        return new static();
    }

    public function getMinLength(): int
    {
        $result = null;
        $children = $this->getChildren();
        array_shift($children);

        foreach ($children as $child) {
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
        $children = $this->getChildren();
        array_shift($children);

        foreach ($children as $child) {
            $childMinValue = $child->getMaxLength();

            if ($childMinValue === null) {
                return null;
            }

            $result = max($result, $childMinValue);
        }

        return $result;
    }

    public function generate(string $from, bool $negative): string
    {
        //todo choose on the prev generate value
        $children = $this->getChildren();
        $key = rand(1, min(2, count($children)));

        return $children[$key]->generate($from, $negative);
    }

    public function __toString(): string
    {
        $children = $this->getChildren();
        $condition = array_shift($children);

        return '(?' . $condition . implode('|', array_map(fn (ChildFunctionInterface $child) => (string) $child, $children)) . ')';
    }

    public function jsonSerialize(): string
    {
        return $this->__toString();
    }
}
