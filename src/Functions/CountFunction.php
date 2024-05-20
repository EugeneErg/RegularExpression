<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Functions\Contracts\ChildFunctionInterface;
use EugeneErg\RegularExpression\Functions\Contracts\ParentFunctionInterface;
use EugeneErg\RegularExpression\Functions\Traits\TraitSetChildren;
use EugeneErg\RegularExpression\Functions\Traits\TraitSetParent;

readonly class CountFunction implements ParentFunctionInterface, ChildFunctionInterface
{
    use TraitSetParent;
    use TraitSetChildren;

    public function __construct(
        public int $from,
        public ?int $to,
        public bool $lazy,
    ) {
    }

    public function __toString(): string
    {
        /*return match ($this->from) {
            0 => match ($this->to) {
                0 => '',
                1 => $this->child . '?' . ($this->lazy ? '?' : ''),
                null => '*' . ($this->lazy ? '?' : ''),
                default => $this->child . '{' . $this->from . ',}' . ($this->lazy ? '?' : ''),
            },
            1 => match ($this->to) {
                1 => (string) $this->child,
                null => $this->child . '+' . ($this->lazy ? '?' : ''),
                default => $this->child . '{' . $this->from . ',' . $this->to . '}',
            },
            $this->to => $this->child . '{' . $this->from . '}',
            default => $this->child . '{' . $this->from . ',' . $this->to . '}' . ($this->to === null && $this->lazy ? '?' : ''),
        };*/

        return match ($this->to) {
            $this->from => match ($this->from) {
                0 => '',
                1 => $this->children[0],
                default => $this->children[0].'{'.$this->from.'}',
            },
            1 => $this->children[0].'?'.($this->lazy ? '?' : ''),
            null => match ($this->from) {
                0 => $this->children[0].'*',
                1 => $this->children[0].'+',
                default => $this->children[0].'{'.$this->from.',}',
            }.($this->lazy ? '?' : ''),
            default => $this->children[0].'{'.$this->from.','.$this->to.'}',
        };
    }

    public static function fromArray(array $data): static
    {
        return new self($data['from'], $data['to'], $data['lazy']);
    }

    public function getMinLength(): int
    {
        if ($this->from === 0) {
            return 0;
        }

        $result = $this->children[0]->getMinLength();

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

        $result = $this->children[0]->getMaxLength();

        if ($result === null) {
            return null;
        }

        return $result * $this->to;
    }

    public function generate(string $from, bool $negative): string
    {
        $value = rand($this->from, $this->to ?? ($this->from + 100));
        $result = '';

        for ($i = 0; $i < $value; $i++) {
            $result .= $this->children[0]->generate($from, $negative);
        }

        return $result;
    }

    public function jsonSerialize(): string
    {
        return $this->__toString();
    }
}
