<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Parser;

use EugeneErg\RegularExpression\RegularExpression;
use EugeneErg\RegularExpression\RegularExpressionException;

final class ParserOptionItem
{
    /** @var ParserOption[] */
    private readonly array $options;

    public function __construct(ParserOption ...$options)
    {
        $this->options = $options;
    }

    /**
     * @throws RegularExpressionException
     */
    public static function equal(string $value): self
    {
        return self::options(new ParserOption(new RegularExpression('{', $value, '}', RegularExpression::PCRE_INFO_JCHANGED), fn () => []));
    }

    public static function options(ParserOption ...$options): self
    {
        return new self(...$options);
    }

    public function getOptions(): array
    {
        return $this->options;
    }
}
