<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

class Enum extends AbstractFunction
{
    public function __toString(): string
    {
        return implode('|', array_map(
            fn (string|FunctionInterface $function): string => is_string($function) ? preg_quote($function) : (string) $function,
            $this->values->items,
        ));
    }

    public function jsonSerialize(): array
    {
        $result = [];

        foreach ($this->values->items as $function) {
            $result[] = $function;
            $result[] = '|';
        }

        array_pop($result);

        return $result;
    }
}
