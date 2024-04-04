<?php

declare(strict_types=1);

namespace Tests;

use EugeneErg\RegularExpression\RegularExpression;
use EugeneErg\RegularExpression\RegularExpressionInformation;
use PHPUnit\Framework\TestCase;

class RegularExpressionInformationTest extends TestCase
{
    public function test(): void
    {
        $regularExpressionInformation = new RegularExpressionInformation();

        $actual = $regularExpressionInformation->getStructure(new RegularExpression('{', 'a|(?<test>ba)', '}'));

        var_dump($actual);
    }
}