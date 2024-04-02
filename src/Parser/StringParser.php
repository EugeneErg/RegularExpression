<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Parser;

use EugeneErg\RegularExpression\RegularExpression;
use EugeneErg\RegularExpression\RegularExpressionException;

class StringParser
{
    public function __construct(private readonly ParserItem $item)
    {
    }

    public function parse(string $subject): ?ParserResult
    {
        return $this->parseItem($this->item, $subject);
    }

    /**
     * @param ParserItem $item
     * @param string $subject
     * @param int $offset
     * @return ParserResult|null
     * @throws RegularExpressionException
     */
    private function parseItem(ParserItem $item, string $subject, int $offset = 0): ?ParserResult
    {
        if ($item->begin !== null) {
            $match = $this->match($item->begin, $subject, $offset);

            if ($match === []) {
                return null;
            }

            $offset += strlen($match[0]);
        }

        foreach ($item->options as $option) {
            $match = $this->match($option->regularExpression, $subject, $offset);

            if ($match !== []) {


                break;
            }
        }
    }

    /**
     * @return string[]
     *
     * @throws RegularExpressionException
     */
    private function match(RegularExpression $pattern, string $subject, int $offset = 0): array
    {
        return RegularExpression::fromPattern('{.{' . $offset . '}' . $pattern->pattern . '}J')->match($subject);
    }
}