<?php

declare(strict_types=1);

namespace Tests;

use EugeneErg\RegularExpression\Callables;
use EugeneErg\RegularExpression\OffsetCapture;
use EugeneErg\RegularExpression\RegularExpression;
use EugeneErg\RegularExpression\RegularExpressions;
use EugeneErg\RegularExpression\ResultCount;
use EugeneErg\RegularExpression\ResultsCount;
use EugeneErg\RegularExpression\Strings;
use PHPUnit\Framework\TestCase;

class RegularExpressionsTest extends TestCase
{
    public function testReplaceSuccess(): void
    {
        $actual = (new RegularExpressions(
            RegularExpression::fromPattern('{.}'),
            RegularExpression::fromPattern('{(.).}'),
        ))->replace('test', '$1Q', 2);

        $this->assertEquals(new ResultCount(4, 'QQsQ'), $actual);
    }

    public function testReplaceSuccessReplacements(): void
    {
        $actual = (new RegularExpressions(
            RegularExpression::fromPattern('{.}'),
            RegularExpression::fromPattern('{(.).}'),
        ))->replace('test', new Strings('$1Q'), 2);

        $this->assertEquals(new ResultCount(4, ''), $actual);
    }

    public function testReplaceSuccessFilterReplacements(): void
    {
        $actual = (new RegularExpressions(
            RegularExpression::fromPattern('{.}'),
            RegularExpression::fromPattern('{(.).}'),
        ))->replace('test', new Strings('$1Q'), 2, true);

        $this->assertEquals(new ResultCount(4, ''), $actual);
    }

    public function testReplaceCallbackSuccess(): void
    {
        $actual = (new RegularExpressions(
            RegularExpression::fromPattern('{.}'),
            RegularExpression::fromPattern('{(.).}'),
        ))->replaceCallback(
            'test',
            function (array $match): string {
                static $step = 0;

                $this->assertEquals([['t'], ['e'], ['QQ', 'Q'], ['st', 's']][$step], $match);
                $step++;

                return 'Q';
            },
            2,
        );

        $this->assertEquals(new ResultCount(4, 'QQ'), $actual);
    }

    public function testReplaceCallbackSuccessReplacements(): void
    {
        $actual = (new RegularExpressions(
            RegularExpression::fromPattern('{.}'),
            RegularExpression::fromPattern('{(.).}'),
        ))->replaceCallback(
            'test',
            new Callables(
                function (array $match): string {
                    static $step = 0;

                    $this->assertEquals([['t'], ['e']][$step], $match);
                    $step++;

                    return 'Q';
                },
                function (array $match): string {
                    static $step = 0;

                    $this->assertEquals([['QQ', 'Q'], ['st', 's']][$step], $match);
                    $step++;

                    return 'Z';
                },
            ),
            2,
        );

        $this->assertEquals(new ResultCount(4, 'ZZ'), $actual);
    }

    public function testReplaceCallbackSuccessOffsetCapture(): void
    {
        $actual = (new RegularExpressions(
            RegularExpression::fromPattern('{.}'),
            RegularExpression::fromPattern('{(.).}'),
        ))->replaceCallback(
            'test',
            function (array $match): string {
                static $step = 0;

                $this->assertEquals([
                    [new OffsetCapture('t', 0)],
                    [new OffsetCapture('e', 1)],
                    [new OffsetCapture('QQ', 0), new OffsetCapture('Q', 0)],
                    [new OffsetCapture('st', 2), new OffsetCapture('s', 2)],
                ][$step], $match);
                $step++;

                return 'Q';
            },
            2,
            true,
        );

        $this->assertEquals(new ResultCount(4, 'QQ'), $actual);
    }

    public function testReplaceCallbackSuccessOffsetCaptureReplacements(): void
    {
        $actual = (new RegularExpressions(
            RegularExpression::fromPattern('{.}'),
            RegularExpression::fromPattern('{(.).}'),
        ))->replaceCallback(
            'test',
            new Callables(
                function (array $match): string {
                    static $step = 0;

                    $this->assertEquals([
                        [new OffsetCapture('t', 0)],
                        [new OffsetCapture('e', 1)],
                    ][$step], $match);
                    $step++;

                    return 'Q';
                },
                function (array $match): string {
                    static $step = 0;

                    $this->assertEquals([
                        [new OffsetCapture('QQ', 0), new OffsetCapture('Q', 0)],
                        [new OffsetCapture('st', 2), new OffsetCapture('s', 2)],
                    ][$step], $match);
                    $step++;

                    return 'Z';
                },
            ),
            2,
            true,
        );

        $this->assertEquals(new ResultCount(4, 'ZZ'), $actual);
    }

    public function testMultiReplaceSuccess(): void
    {
        $actual = (new RegularExpressions(
            RegularExpression::fromPattern('{.}'),
            RegularExpression::fromPattern('{(.).}'),
        ))->multiReplace(new Strings('test'), 'Q', 4);

        $this->assertEquals(new ResultsCount(6, new Strings('QQ')), $actual);
    }

    public function testMultiReplaceSuccessReplacements(): void
    {
        $actual = (new RegularExpressions(
            RegularExpression::fromPattern('{.}'),
            RegularExpression::fromPattern('{(.).}'),
        ))->multiReplace(new Strings('test'), new Strings('Q'), 4);

        $this->assertEquals(new ResultsCount(6, new Strings('')), $actual);
    }

    public function testMultiReplaceSuccessFilter(): void
    {
        $actual = (new RegularExpressions(
            RegularExpression::fromPattern('{.}'),
            RegularExpression::fromPattern('{(.).}'),
        ))->multiReplace(new Strings('test'), 'Q', 2, true);

        $this->assertEquals(new ResultsCount(4, new Strings('QQ')), $actual);
    }

    public function testMultiReplaceSuccessFilterReplacements(): void
    {
        $actual = (new RegularExpressions(
            RegularExpression::fromPattern('{.}'),
            RegularExpression::fromPattern('{(.).}'),
        ))->multiReplace(new Strings('test'), new Strings('Q'), 2, true);

        $this->assertEquals(new ResultsCount(4, new Strings('')), $actual);
    }

    public function testMultiReplaceCallbackSuccess(): void
    {
        $actual = (new RegularExpressions(
            RegularExpression::fromPattern('{.}'),
            RegularExpression::fromPattern('{(.).}'),
        ))->multiReplaceCallback(
            new Strings('test'),
            function (array $match): string {
                static $step = 0;

                $this->assertEquals([['t'], ['e'], ['QQ', 'Q'], ['st', 's']][$step], $match);
                $step++;

                return 'Q';
            },
            2,
        );

        $this->assertEquals(new ResultsCount(4, new Strings('QQ')), $actual);
    }

    public function testMultiReplaceCallbackSuccessReplacements(): void
    {
        $actual = (new RegularExpressions(
            RegularExpression::fromPattern('{.}'),
            RegularExpression::fromPattern('{(.).}'),
        ))->multiReplaceCallback(
            new Strings('test'),
            new Callables(
                function (array $match): string {
                    static $step = 0;

                    $this->assertEquals([['t', 'e'][$step]], $match);
                    $step++;

                    return 'Q';
                },
                function (array $match): string {
                    static $step = 0;

                    $this->assertEquals([['QQ', 'Q'], ['st', 's']][$step], $match);
                    $step++;

                    return 'Z';
                },
            ),
            2,
        );

        $this->assertEquals(new ResultsCount(4, new Strings('ZZ')), $actual);
    }

    public function testMultiReplaceCallbackSuccessOffsetCapture(): void
    {
        $actual = (new RegularExpressions(
            RegularExpression::fromPattern('{.}'),
            RegularExpression::fromPattern('{(.).}'),
        ))->multiReplaceCallback(
            new Strings('test'),
            function (array $match): string {
                static $step = 0;

                $this->assertEquals([
                    [new OffsetCapture('t', 0)],
                    [new OffsetCapture('e', 1)],
                    [new OffsetCapture('QQ', 0), new OffsetCapture('Q', 0)],
                    [new OffsetCapture('st', 2), new OffsetCapture('s', 2)],
                ][$step], $match);
                $step++;

                return 'Q';
            },
            2,
            true,
        );

        $this->assertEquals(new ResultsCount(4, new Strings('QQ')), $actual);
    }

    public function testMultiReplaceCallbackSuccessOffsetCaptureReplacements(): void
    {
        $actual = (new RegularExpressions(
            RegularExpression::fromPattern('{.}'),
            RegularExpression::fromPattern('{(.).}'),
        ))->multiReplaceCallback(
            new Strings('test'),
            new Callables(
                //fn (): int => 12,
                function (array $match): string {
                    static $step = 0;

                    $this->assertEquals([[new OffsetCapture('t', 0), new OffsetCapture('e', 1)][$step]], $match);
                    $step++;

                    return 'Q';
                },
                function (array $match): string {
                    static $step = 0;

                    $this->assertEquals([
                        [new OffsetCapture('QQ', 0), new OffsetCapture('Q', 0)],
                        [new OffsetCapture('st', 2), new OffsetCapture('s', 2)],
                    ][$step], $match);
                    $step++;

                    return 'Z';
                },
            ),
            2,
            true,
        );

        $this->assertEquals(new ResultsCount(4, new Strings('ZZ')), $actual);
    }
}
