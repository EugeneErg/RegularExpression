<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

class HexFunction implements FunctionInterface
{
    public readonly FunctionInterface $root;

    public function __construct(
        public readonly string $value,
        public readonly ?FunctionWithChildrenInterface $parent,
    ) {
        $this->root = $parent?->getRoot() ?? $this;
    }

    public function __toString(): string
    {

        return '\\x' . (strlen($this->value) > 2 ? '{' . $this->value . '}' : $this->value);
    }

    public static function fromArray(array $data, ?FunctionWithChildrenInterface $parent = null): static
    {
        return new self($data['value'], $parent);
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

    public function generate(string $from): string
    {
        return json_decode('"' . $this->value . '"');
    }

    public function getParent(): ?FunctionWithChildrenInterface
    {
        return $this->parent;
    }

    public function getRoot(): FunctionInterface
    {
        return $this->root;
    }
}
