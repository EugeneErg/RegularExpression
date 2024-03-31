<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions;

class Contain extends AbstractFunction
{
    public function __toString(): string
    {
        return implode('', array_map(
            fn (string|FunctionInterface $function): string => is_string($function) ? preg_quote($function) : (string) $function,
            $this->values->items,
        ));
    }

    public function jsonSerialize(): array
    {
        return $this->values->items;
    }
}
