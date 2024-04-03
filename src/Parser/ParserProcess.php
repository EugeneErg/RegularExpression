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
    public function __construct(private readonly ParserGroupItem $parseItem, private readonly string $subject)
    {
        $this->offset = 0;
        $this->result = $this->parse($this->parseItem);
    }

    /**
     * @throws RegularExpressionException
     */
    private function parse(ParserItemInterface $item): ?ParserResult
    {
        if ($item instanceof ParserItemGroupInterface) {
            return $this->parseGroup($item);
        }

        return new ParserResult($this->parseOption($item));
    }

    /**
     * @return string[]
     *
     * @throws RegularExpressionException
     */
    private function match(RegularExpression $pattern): array
    {
        return RegularExpression::fromPattern('{.{' . $this->offset . '}' . $pattern->pattern . '}J')->match($this->subject);
    }

    /**
     * @throws RegularExpressionException
     */
    private function parseGroup(ParserItemGroupInterface $item): ?ParserResult
    {
        $this->parseGroup($item);
        $match = $this->match($item->getBegin());

        if ($match === []) {
            return null;
        }

        $this->offset += strlen($match[0]);
        $options[] = $this->parseOption($item);
        $children = [];

        do {
            foreach ($item->getChildren() as $name => $child) {
                $childResult = $this->parse($child);

                if ($childResult !== null) {
                    $children[$name] = $childResult;

                    break;
                }
            }

            $match = $this->match($item->getEnd());

            if ($match !== []) {
                $this->offset += strlen($match[0]);

                break;
            }
        } while (false);

        return new ParserResult($options, $children);
    }

    /**
     * @throws RegularExpressionException
     */
    private function parseOption(ParserItemInterface $item)
    {
        foreach ($item->getOptions() as $option) {
            $match = $this->match($option->regularExpression);

            if ($match !== []) {
                $this->offset += strlen($match[0]);

                return ($option->callback)($match);
            }
        }

        return [];
    }
}
