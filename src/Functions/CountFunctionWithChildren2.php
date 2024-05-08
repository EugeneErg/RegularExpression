<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

class CountFunctionWithChildren implements FunctionInterface, FunctionWithChildrenWithParentInterface
{
    public readonly FunctionInterface $root;

    public function __construct(
        public readonly int $from,
        public readonly ?int $to,
        public readonly bool $lazy,
        public readonly FunctionInterface $child,
        public readonly ?FunctionInterface $parent = null,
    ) {
        $this->root = $this->parent?->getRoot() ?? $this;
    }

    public function __toString(): string
    {
        return match ($this->to) {
            $this->from => match ($this->from) {
                0 => '',
                1 => $this->child,
                default => $this->child . '{' . $this->from . '}',
            },
            1 => $this->child . '?' . ($this->lazy ? '?' : ''),
            null => match ($this->from) {
                    0 => $this->child . '*',
                    1 => $this->child . '+',
                    default => $this->child . '{' . $this->from . ',}',
                } . ($this->lazy ? '?' : ''),
            default => $this->child . '{' . $this->from . ',' . $this->to . '}',
        };
    }

    public function getRoot(): FunctionInterface
    {
        return $this->root;
    }

    public function getParent(): ?FunctionInterface
    {
        return $this->parent;
    }

    public static function fromArray(
        array              $data,
        ?FunctionInterface $parent = null,
        FunctionInterface ...$children
    ): FunctionWithChildrenWithParentInterface {
        return new self(
            $data['from'],
            $data['to'],
            $data['lazy'],
            $children[array_key_first($children)],
            $parent,
        );
    }

    public function getMinLength(): int
    {
        if ($this->from === 0) {
            return 0;
        }

        $result = $this->child->getMinLength();

        if ($result === 0) {
            return 0;
        }

        return $result * $this->from;
    }

    public function getMaxLength(): ?int
    {
        if ($this->to === null) {
            return null;
        }

        $result = $this->child->getMaxLength();

        if ($result === null) {
            return null;
        }

        return $result * $this->to;
    }

    public function generate(string $from): string
    {
        $value = rand($this->from, $this->to ?? ($this->from + 100));
        $result = '';

        for ($i = 0; $i < $value; $i++) {
            $result .= $this->child->generate($from);
        }

        return $result;
    }

    public function jsonSerialize(): string
    {
        return $this->__toString();
    }

    public function getChildren(): array
    {
        return [$this->child];
    }
}
