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
    private function parse(ParserItem $item, string $name, int $position = 0, array $parentOptions = []): ?ParserResult
    {
        if ($item->getBegin() !== null) {
            $match = $this->match($item->getBegin());

            if ($match === []) {
                return null;
            }
        }

        $options = $this->parseOption($item, $position, $name, $parentOptions);
        $children = [];

        if ($item->getChildren() !== []) {
            do {
                foreach ($item->getChildren() as $childName => $child) {
                    $childResult = $this->parse($child, $childName, count($children), $options ?? []);

                    if ($childResult !== null) {
                        $children[] = $childResult;

                        break;
                    }
                }

                if ($item->getEnd() !== null) {
                    $match = $this->match($item->getEnd());

                    if ($match !== []) {
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
        $result = RegularExpression::fromPattern('{(?<=\\A.{' . $this->offset . '})' . $pattern->pattern . '}J')
            ->match($this->subject);

        if ($result !== []) {
            $this->offset += strlen($result[0]);
        }

        return $result;
    }

    /**
     * @throws RegularExpressionException
     */
    private function parseOption(ParserItem $item, int $position, string $name, array $parentOptions): ?array
    {
        foreach ($item->getOptions() as $option) {
            $match = $this->match($option->regularExpression);

            if ($match !== []) {
                return ($option->callback)($match, $position, $name, $parentOptions);
            }
        }

        return null;
    }
}
