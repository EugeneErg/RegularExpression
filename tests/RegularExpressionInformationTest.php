<?php

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;

class RegularExpressionInformationTest extends TestCase
{
    public function test(): void
    {
        var_dump(preg_match('{[a-]}', '-'));
    }
}