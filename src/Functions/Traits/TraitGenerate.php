<?php

declare(strict_types=1);

namespace EugeneErg\RegularExpression\Functions\Traits;

use EugeneErg\RegularExpression\Functions\Contracts\FunctionInterface;
use EugeneErg\RegularExpression\RegularExpression;
use LogicException;
use Throwable;

/**
 * @mixin FunctionInterface
 */
trait TraitGenerate
{
    public function generate(string $from, bool $not): string
    {
        //todo $not
        try {
            $matches = (new RegularExpression('{', $this.'+', '}'))->matchAll($from);
        } catch (Throwable $exception) {
            throw new LogicException('Invalid pattern.', previous: $exception);
        }
        $keyA = array_rand($matches);
        $keyB = array_rand($matches[$keyA]);

        return substr($matches[$keyA][$keyB], rand(0, strlen($matches[$keyA][$keyB]) - 1), 1);
    }
}
