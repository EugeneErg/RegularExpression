<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression;

use JsonSerializable;
use Stringable;

class RegularExpression implements Stringable, JsonSerializable
{
    public const PCRE_CASELESS = 1;

    public const PCRE_MULTILINE = 2;

    public const PCRE_DOTALL = 4;

    public const PCRE_EXTENDED = 8;

    public const PCRE_ANCHORED = 16;

    public const PCRE_DOLLAR_ENDONLY = 32;

    public const PCRE_EXTRA = 64;

    public const PCRE_UNGREEDY = 512;

    public const PCRE_UTF8 = 2048;

    public const PCRE_NO_AUTO_CAPTURE = 4096;

    public const PCRE_INFO_JCHANGED = 128;

    public const MODIFIER_MAPPING = [
        'i' => self::PCRE_CASELESS,
        'm' => self::PCRE_MULTILINE,
        's' => self::PCRE_DOTALL,
        'x' => self::PCRE_EXTENDED,
        'u' => self::PCRE_UTF8,
        'U' => self::PCRE_UNGREEDY,
        'D' => self::PCRE_DOLLAR_ENDONLY,
        'S' => self::PCRE_EXTRA,
        'A' => self::PCRE_ANCHORED,
        'X' => self::PCRE_NO_AUTO_CAPTURE,
        'J' => self::PCRE_INFO_JCHANGED,
    ];

    private const MIRROR_DELIMITERS = ['(' => ')', '[' => ']', '{' => '}', '<' => '>'];

    /**
     * @throws RegularExpressionException
     */
    public function __construct(
        public readonly string $openDelimiter,
        public readonly string $pattern,
        public readonly string $closeDelimiter,
        public readonly int $modifiers = 0,
    ) {
        try {
            static::checkPattern((string) $this);
        } catch (\Throwable) {
            var_dump((string) $this);
            die;
        }
    }

    /**
     * @throws RegularExpressionException
     */
    public static function fromPattern(string $pattern): static
    {
        static::checkPattern($pattern);
        $openDelimiter = $pattern[0];
        $closeDelimiter = static::MIRROR_DELIMITERS[$openDelimiter] ?? $openDelimiter;
        $modifiers = preg_quote(implode('', array_keys(self::MODIFIER_MAPPING)));
        $quoteOpenDelimiter = preg_quote($openDelimiter);
        $quoteCloseDelimiter = preg_quote($closeDelimiter);
        $mirrorPattern = "{^{$quoteOpenDelimiter}(?<result_pattern>.*){$quoteCloseDelimiter}(?<result_modifiers>[{$modifiers}]*)$}";
        preg_match($mirrorPattern, $pattern, $matches);
        $resultPattern = static::unDelimiterPattern($matches['result_pattern'], $openDelimiter, $closeDelimiter);
        $resultModifiers = $matches['result_modifiers'];

        return new static($openDelimiter, $resultPattern, $closeDelimiter, static::modifiersFromString($resultModifiers));
    }

    /**
     * @throws RegularExpressionException
     */
    public static function fromString(string $value): static
    {
        return new static('{', preg_quote($value), '}');
    }

    public function __toString(): string
    {
        return "{$this->openDelimiter}{$this->delimiterPattern()}{$this->closeDelimiter}{$this->modifiersAsString()}";
    }

    public function jsonSerialize(): string
    {
        return $this->__toString();
    }

    /**
     * @return array<string>
     */
    public function match(string $subject, int $offset = 0): array
    {
        preg_match((string) $this, $subject, $match, PREG_UNMATCHED_AS_NULL, $offset);

        return $this->prepareMatch($match);
    }

    /**
     * @return array<OffsetCapture>
     */
    public function matchOffsetCapture(string $subject, int $offset = 0): array
    {
        preg_match(
            (string) $this,
            $subject,
            $match,
            PREG_OFFSET_CAPTURE | PREG_UNMATCHED_AS_NULL,
            $offset,
        );

        return $this->prepareMatchOffsetCapture($match);
    }

    /**
     * @return array<array<string>>
     */
    public function matchAll(string $subject, int $offset = 0, bool $setOrder = false): array
    {
        preg_match_all(
            (string) $this,
            $subject,
            $matches,
            PREG_UNMATCHED_AS_NULL | ($setOrder ? PREG_SET_ORDER : 0),
            $offset,
        );
        $result = [];

        foreach ($matches as $key => $match) {
            $result[$key] = $this->prepareMatch($match);
        }

        return $result;
    }

    /**
     * @return array<array<OffsetCapture>>
     */
    public function matchOffsetCaptureAll(string $subject, int $offset = 0, bool $setOrder = false): array
    {
        preg_match_all(
            (string) $this,
            $subject,
            $matches,
            PREG_UNMATCHED_AS_NULL | PREG_OFFSET_CAPTURE | ($setOrder ? PREG_SET_ORDER : 0),
            $offset,
        );
        $result = [];

        foreach ($matches as $key => $match) {
            $result[$key] = $this->prepareMatchOffsetCapture($match);
        }

        return $result;
    }

    public function replace(
        string $subject,
        string $replacement,
        ?int $limit = null,
        bool $filter = false,
    ): ?ResultCount {
        $result = $filter
            ? preg_filter((string) $this, $replacement, $subject, $limit ?? -1, $count)
            : preg_replace((string) $this, $replacement, $subject, $limit ?? -1, $count);

        return $result === null ? null : new ResultCount($count, $result);
    }

    public function replaceCallback(
        string $subject,
        callable $replacement,
        ?int $limit = null,
        bool $offsetCapture = false,
    ): ResultCount {
        $result = preg_replace_callback(
            (string) $this,
            $offsetCapture
                ? fn (array $match): string => $replacement($this->prepareMatchOffsetCapture($match))
                : fn (array $match): string => $replacement($this->prepareMatch($match)),
            $subject,
            $limit ?? -1,
            $count,
            $offsetCapture ? PREG_OFFSET_CAPTURE : 0,
        );

        return new ResultCount($count, $result);
    }

    public function multiReplace(
        Strings $subject,
        string $replacement,
        ?int $limit = null,
        bool $filter = false,
    ): ResultsCount {
        $result = $filter
            ? preg_filter((string) $this, $replacement, $subject->items, $limit ?? -1, $count)
            : preg_replace((string) $this, $replacement, $subject->items, $limit ?? -1, $count);

        return new ResultsCount($count, new Strings(...$result));
    }

    public function multiReplaceCallback(
        Strings $subject,
        callable $replacement,
        ?int $limit = null,
        bool $offsetCapture = false,
    ): ResultsCount {
        $result = preg_replace_callback(
            (string) $this,
            $offsetCapture
                ? fn (array $match): string => $replacement($this->prepareMatchOffsetCapture($match))
                : fn (array $match): string => $replacement($this->prepareMatch($match)),
            $subject->items,
            $limit ?? -1,
            $count,
            $offsetCapture ? PREG_OFFSET_CAPTURE : 0,
        );

        return new ResultsCount($count, new Strings(...$result));
    }

    public function grep(Strings $strings, bool $invert = false): array
    {
        return preg_grep((string) $this, $strings->items, $invert ? PREG_GREP_INVERT : 0);
    }

    public function split(
        string $subject,
        ?int $limit = null,
        bool $withoutEmpty = false,
        bool $delimiterCapture = false,
    ): array {
        return preg_split(
            (string) $this,
            $subject,
            $limit ?? -1,
            ($withoutEmpty ? PREG_SPLIT_NO_EMPTY : 0) | ($delimiterCapture ? PREG_SPLIT_DELIM_CAPTURE : 0),
        );
    }

    public function splitOffsetCapture(
        string $subject,
        ?int $limit = null,
        bool $withoutEmpty = false,
        bool $delimiterCapture = false,
    ): array {
        $result = preg_split(
            (string) $this,
            $subject,
            $limit ?? -1,
            PREG_SPLIT_OFFSET_CAPTURE
                | ($withoutEmpty ? PREG_SPLIT_NO_EMPTY : 0)
                | ($delimiterCapture ? PREG_SPLIT_DELIM_CAPTURE : 0),
        );

        return $this->prepareMatchOffsetCapture($result);
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

    private function modifiersAsString(): string
    {
        $result = '';

        foreach (self::MODIFIER_MAPPING as $flagChar => $flagValue) {
            if ($this->modifiers & $flagValue) {
                $result .= $flagChar;
            }
        }

        return $result;
    }

    private static function unDelimiterPattern(string $pattern, string $openDelimiter, string $closeDelimiter): string
    {
        return $openDelimiter === $closeDelimiter
            ? str_replace("\\{$openDelimiter}", $openDelimiter, $pattern)
            : $pattern;
    }

    private function delimiterPattern(): string
    {
        return $this->openDelimiter === $this->closeDelimiter
            ? str_replace($this->openDelimiter, "\\{$this->openDelimiter}", $this->pattern)
            : $this->pattern;
    }

    private function prepareMatch(array $match): array
    {
        return array_filter($match, fn (?string $value): bool => $value !== null);
    }

    private function prepareMatchOffsetCapture(array $match): array
    {
        $match = array_filter($match, fn (array $value): bool => $value[0] !== null);

        array_walk($match, function (array &$value): void {
            $value = new OffsetCapture(...$value);
        });

        return $match;
    }

    /**
     * @throws RegularExpressionException
     */
    private static function checkPattern(string $pattern): void
    {
        $oldErrorHandler = set_error_handler(
            /**
             * @throws RegularExpressionException
             */
            function (int $level, string $message, string $file, int $line) {
                if ($level === E_WARNING && str_contains($message, 'preg_match')) {
                    throw new RegularExpressionException($message, null, $file, $line);
                }

                return false;
            },
        );

        try {
            preg_match($pattern, '');
        } finally {
            set_error_handler($oldErrorHandler);
        }
    }
}
