<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Parser;

use EugeneErg\RegularExpression\RegularExpression;
use EugeneErg\RegularExpression\RegularExpressionException;

class ParserProcess
{
    private int $offset;
    public readonly ?ParserResult $result;

    /**
     * @throws RegularExpressionException
     */
    public function __construct(private readonly ParserItem $parseItem, private readonly string $subject, string $name)
    {
        $this->offset = 0;
        $this->result = $this->parse($this->parseItem, $name);
    }

    /**
     * @throws RegularExpressionException
     */
    private function parse(ParserItem $item, string $name): ?ParserResult
    {
        if ($item->getBegin() !== null) {
            $match = $this->match($item->getBegin());

            if ($match === []) {
                return null;
            }

            $this->offset += strlen($match[0]);
        }

        $options = $this->parseOption($item);
        $children = [];

        if ($item->getChildren() !== []) {
            do {
                foreach ($item->getChildren() as $childName => $child) {
                    $childResult = $this->parse($child, $childName);

                    if ($childResult !== null) {
                        $children[] = $childResult;

                        break;
                    }
                }

                if ($item->getEnd() !== null) {
                    $match = $this->match($item->getEnd());

                    if ($match !== []) {
                        $this->offset += strlen($match[0]);

                        break;
                    }
                }
            } while ($this->offset < strlen($this->subject));
        }

        if ($options === null && $children === [] && $item->getBegin() === null) {
            return null;
        }

        return new ParserResult($name, $options ?? [], $children);
    }

    /**
     * @return string[]
     *
     * @throws RegularExpressionException
     */
    private function match(RegularExpression $pattern): array
    {
        return RegularExpression::fromPattern('{^.{' . $this->offset . '}' . $pattern->pattern . '}J')->match($this->subject);
    }

    /**
     * @throws RegularExpressionException
     */
    private function parseOption(ParserItem $item): ?array
    {
        foreach ($item->getOptions() as $option) {
            $match = $this->match($option->regularExpression);

            if ($match !== []) {
                $this->offset += strlen($match[0]);

                return ($option->callback)($match);
            }
        }

        return null;
    }
}
