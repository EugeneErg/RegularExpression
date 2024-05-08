<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

use EugeneErg\RegularExpression\Parser\ParserResult;

class ModifierFunction implements FunctionInterface
{
    public const PCRE_CASELESS = 1;
    public const PCRE_MULTILINE = 2;
    public const PCRE_DOTALL = 4;
    public const PCRE_EXTENDED = 8;
    public const PCRE_UNGREEDY = 512;
    public const PCRE_NO_AUTO_CAPTURE = 4096;
    public const PCRE_INFO_JCHANGED = 128;
    public const MODIFIER_MAPPING = [
        'i' => self::PCRE_CASELESS,
        'm' => self::PCRE_MULTILINE,
        's' => self::PCRE_DOTALL,
        'x' => self::PCRE_EXTENDED,
        'U' => self::PCRE_UNGREEDY,
        'X' => self::PCRE_NO_AUTO_CAPTURE,
        'J' => self::PCRE_INFO_JCHANGED,
    ];

    public readonly FunctionInterface $root;

    public function __construct(
        public readonly int $add = 0,
        public readonly int $remove = 0,
        public readonly ?FunctionWithChildrenInterface $parent = null,
    ) {
        $this->root = $this->parent?->getRoot() ?? $this;
    }

    public function getParent(): ?FunctionWithChildrenInterface
    {
        return $this->parent;
    }

    public function getRoot(): FunctionInterface
    {
        return $this->root;
    }

    public function __toString(): string
    {
        $remove = $this->modifiersAsString($this->remove);

        return '(?' . $this->modifiersAsString($this->add) . ($remove === '' ? '' : '-' . $remove) . ')';
    }

    public static function fromArray(ParserResult $item, ?FunctionInterface $parent = null): FunctionInterface
    {
        return new self(
            self::modifiersFromString($item->options['add']),
            self::modifiersFromString($item->options['remove']),
            $parent,
        );
    }

    public function getMinLength(): int
    {
        return 0;
    }

    public function getMaxLength(): ?int
    {
        return 0;
    }

    public function generate(string $from): string
    {
        return $this->root->generate($from);
    }

    public function jsonSerialize(): string
    {
        return $this->__toString();
    }

    private static function modifiersFromString(string $flags): int
    {
        $result = 0;
        $length = strlen($flags);

        for ($i = 0; $i < $length; $i++) {
            $result |= static::MODIFIER_MAPPING[$flags[$i]];
        }

        return $result;
    }

    private function modifiersAsString(int $modifiers): string
    {
        $result = '';

        foreach (self::MODIFIER_MAPPING as $flagChar => $flagValue) {
            if ($modifiers & $flagValue) {
                $result .= $flagChar;
            }
        }

        return $result;
    }
}