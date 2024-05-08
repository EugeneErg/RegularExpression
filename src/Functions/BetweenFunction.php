<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

class BetweenFunction implements FunctionInterface
{
    use TraitGenerate;

    public readonly FunctionInterface $root;

    public function __construct(
        public readonly string $from,
        public readonly string $to,
        public readonly ?FunctionWithChildrenInterface $parent,
    ) {
        $this->root = $parent?->getRoot() ?? $this;
    }

    public function __toString(): string
    {
        return $this->from . '-' . $this->to;
    }

    public static function fromArray(array $data, ?FunctionWithChildrenInterface $parent = null): static
    {
        return new self($data['from'], $data['to'], $parent);
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

    public function getParent(): ?FunctionWithChildrenInterface
    {
        return $this->parent;
    }

    public function getRoot(): FunctionInterface
    {
        return $this->root;
    }
}
