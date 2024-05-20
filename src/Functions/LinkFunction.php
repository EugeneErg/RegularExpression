<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Functions\Contracts\ChildFunctionInterface;
use EugeneErg\RegularExpression\Functions\Traits\TraitSetParent;

readonly class LinkFunction implements ChildFunctionInterface
{
    use TraitSetParent;

    public function __construct(
        public GroupFunction $target,
        public string|int $link,
        public bool $isRecursive,
        public bool $isFirstInCondition,
    ) {
    }

    public function __toString(): string
    {
        if ($this->link === 0) {
            return '('.($this->isFirstInCondition ? '' : '?').'R)';
        }

        if ($this->isFirstInCondition) {
            return '('.$this->link.')';
        }

        if ($this->isRecursive) {
            return '(?'.(is_string($this->link) ? '&' : '').$this->link.')';
        }

        return '\g'.(is_string($this->link) ? '<'.$this->link.'>' : $this->link);
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

    public function generate(string $from, bool $negative): string
    {
        return $this->root->generate($from, $negative);
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
