<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

class HexFunction implements FunctionInterface
{
    public readonly FunctionInterface $root;

    public function __construct(
        public readonly string $value,
        public readonly ?FunctionInterface $parent = null,
    ) {
        $this->root = $this->parent?->getRoot() ?? $this;
    }

    public function __toString(): string
    {

        return '\\x' . (strlen($this->value) > 2 ? '{' . $this->value . '}' : $this->value);
    }

    public static function fromArray(array $data, ?FunctionInterface $parent = null): FunctionInterface
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

    public function getRoot(): FunctionInterface
    {
        return $this->root;
    }

    public function getParent(): ?FunctionInterface
    {
        return $this->parent;
    }
}
