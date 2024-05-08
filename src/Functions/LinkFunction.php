<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Functions\Contracts\ChildFunctionInterface;
use EugeneErg\RegularExpression\Functions\Traits\TraitParent;

class LinkFunction implements ChildFunctionInterface
{
    use TraitParent;

    public function __construct(
        public readonly GroupFunction $target,
        public readonly string|int $link,
        public readonly bool $isRecursive,
        public readonly bool $isFirstInCondition,
    ) {
    }

    public function __toString(): string
    {
        if ($this->link === 0) {
            return '(' . ($this->isFirstInCondition ? '' : '?') . 'R)';
        }

        if ($this->isFirstInCondition) {
            return '(' . $this->link . ')';
        }

        if ($this->isRecursive) {
            return '(?' . (is_string($this->link) ? '&' : '') . $this->link . ')';
        }

        return '\g' . (is_string($this->link) ? '<' . $this->link . '>' : $this->link);
    }

    public static function fromArray(array $data): static
    {
        $byName = is_string($data['value']);
        $target = $byName
            ? static::getGroupFunctionByName($data['value'])
            : static::getGroupFunctionByNumber($data['value']);

        return new self($target, $data['value'], $data['recursive'], $data['first_in_condition']);
    }

    public function getMinLength(): int
    {
        //todo
    }

    public function getMaxLength(): ?int
    {
        //todo
    }

    public function generate(string $from, bool $not): string
    {
        return $this->root->generate($from, $not);
    }

    public function jsonSerialize(): string
    {
        return $this->__toString();
    }

    private static function getGroupFunctionByName(string $value): GroupFunction
    {
        //todo
    }

    private static function getGroupFunctionByNumber(int $value): GroupFunction
    {
        //todo
    }
}
