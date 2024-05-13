<?php

declare(strict_types=1);

namespace Tests;

use EugeneErg\RegularExpression\OffsetCapture;
use EugeneErg\RegularExpression\RegularExpression;
use EugeneErg\RegularExpression\RegularExpressionException;
use EugeneErg\RegularExpression\ResultCount;
use EugeneErg\RegularExpression\ResultsCount;
use EugeneErg\RegularExpression\Strings;
use PHPUnit\Framework\TestCase;

class RegularExpressionTest extends TestCase
{
    /**
     * @return void
     * @throws RegularExpressionException
     */
    public function testFromPatternMirrorSuccess(): void
    {
        $actual = RegularExpression::fromPattern('{test{2}/.}A');

        $this->assertEquals(
            new RegularExpression('{', 'test{2}/.', '}', RegularExpression::MODIFIER_MAPPING['A']),
            $actual,
        );
    }

    /**
     * @return void
     * @throws RegularExpressionException
     */
    public function testFromPatternSuccess(): void
    {
        $actual = RegularExpression::fromPattern('/test{2}\/./A');

        $this->assertEquals(
            new RegularExpression('/', 'test{2}/.', '/', RegularExpression::MODIFIER_MAPPING['A']),
            $actual,
        );
    }

    /**
     * @return void
     * @throws RegularExpressionException
     */
    public function testFromPatternFail(): void
    {
        $this->expectException(RegularExpressionException::class);

        RegularExpression::fromPattern('invalid pattern');
    }

    public function testFromStringSuccess(): void
    {
        $actual = RegularExpression::fromString('/test{2}\\/./A');

        $this->assertEquals(new RegularExpression('{', '/test\\{2\\}\\\\/\\./A', '}'), $actual);
    }

    public function testToStringSuccess(): void
    {
        $actual = new RegularExpression('{', 'test{2}/.', '}', RegularExpression::MODIFIER_MAPPING['A']);

        $this->assertEquals('{test{2}/.}A', (string) $actual);
    }

    public function testMatchSuccess(): void
    {
        $actual = RegularExpression::fromPattern('{.}')->match('test', 2);

        $this->assertEquals(['s'], $actual);
    }

    public function testMatchOffsetCaptureSuccess(): void
    {
        $actual = RegularExpression::fromPattern('{.}')->matchOffsetCapture('test', 2);

        $this->assertEquals([new OffsetCapture('s', 2)], $actual);
    }

    public function testMatchAllSuccess(): void
    {
        $actual = RegularExpression::fromPattern('{.}')->matchAll('test', 2);

        $this->assertEquals([['s', 't']], $actual);
    }

    public function testMatchAllSuccessSetOrder(): void
    {
        $actual = RegularExpression::fromPattern('{.}')->matchAll('test', 2, true);

        $this->assertEquals([['s'], ['t']], $actual);
    }

    public function testMatchOffsetCaptureAllSuccess(): void
    {
        $actual = RegularExpression::fromPattern('{.}')->matchOffsetCaptureAll('test', 2);

        $this->assertEquals([[new OffsetCapture('s', 2), new OffsetCapture('t', 3)]], $actual);
    }

    public function testMatchOffsetCaptureAllSuccessSetOrder(): void
    {
        $actual = RegularExpression::fromPattern('{.}')->matchOffsetCaptureAll('test', 2, true);

        $this->assertEquals([[new OffsetCapture('s', 2)], [new OffsetCapture('t', 3)]], $actual);
    }

    public function testReplaceSuccess(): void
    {
        $actual = RegularExpression::fromPattern('{.}')->replace('test', 'Q', 2);

        $this->assertEquals(new ResultCount(2, 'QQst'), $actual);
    }

    public function testReplaceSuccessFilter(): void
    {
        $actual = RegularExpression::fromPattern('{^.$}')->replace('test', 'Q', 2, true);

        $this->assertEquals(null, $actual);
    }

    public function testReplaceCallbackSuccess(): void
    {
        $actual = RegularExpression::fromPattern('{.}')
            ->replaceCallback(
                'test',
                function (array $match): string {
                    static $step = 0;

                    $this->assertEquals([['t', 'e'][$step]], $match);
                    $step++;

                    return 'Q';
                },
                2,
            );

        $this->assertEquals(new ResultCount(2, 'QQst'), $actual);
    }

    public function testReplaceCallbackSuccessOffsetCapture(): void
    {
        $actual = RegularExpression::fromPattern('{.}')
            ->replaceCallback(
                'test',
                function (array $match): string {
                    static $step = 0;

                    $this->assertEquals([[new OffsetCapture('t', 0), new OffsetCapture('e', 1)][$step]], $match);
                    $step++;

                    return 'Q';
                },
                2,
                true,
            );

        $this->assertEquals(new ResultCount(2, 'QQst'), $actual);
    }

    public function testMultiReplaceSuccess(): void
    {
        $actual = RegularExpression::fromPattern('{.}')
            ->multiReplace(new Strings('test'), 'Q', 2);

        $this->assertEquals(new ResultsCount(2, new Strings('QQst')), $actual);
    }

    public function testMultiReplaceSuccessFilter(): void
    {
        $actual = RegularExpression::fromPattern('{^.$}')
            ->multiReplace(new Strings('test'), 'Q', 2, true);

        $this->assertEquals(new ResultsCount(0, new Strings()), $actual);
    }

    public function testMultiReplaceCallbackSuccess(): void
    {
        $actual = RegularExpression::fromPattern('{.}')
            ->multiReplaceCallback(
                new Strings('test'),
                function (array $match): string {
                    static $step = 0;

                    $this->assertEquals([['t', 'e'][$step]], $match);
                    $step++;

                    return 'Q';
                },
                2,
            );

        $this->assertEquals(new ResultsCount(2, new Strings('QQst')), $actual);
    }

    public function testMultiReplaceCallbackSuccessOffsetCapture(): void
    {
        $actual = RegularExpression::fromPattern('{.}')
            ->multiReplaceCallback(
                new Strings('test'),
                function (array $match): string {
                    static $step = 0;

                    $this->assertEquals([[new OffsetCapture('t', 0), new OffsetCapture('e', 1)][$step]], $match);
                    $step++;

                    return 'Q';
                },
                2,
                true,
            );

        $this->assertEquals(new ResultsCount(2, new Strings('QQst')), $actual);
    }

    public function testSplitSuccess(): void
    {
        $actual = RegularExpression::fromPattern('{(.)}')->split('test', 2);

        $this->assertEquals(['', 'est'], $actual);
    }

    public function testSplitSuccessWithoutEmpty(): void
    {
        $actual = RegularExpression::fromPattern('{(.)}')->split('test', 2, true);

        $this->assertEquals([], $actual);
    }

    public function testSplitSuccessDelimiterCapture(): void
    {
        $actual = RegularExpression::fromPattern('{(.)}')->split('test', 2, false, true);

        $this->assertEquals(['', 't', 'est'], $actual);
    }

    public function testSplitSuccessWithoutEmptyDelimiterCapture(): void
    {
        $actual = RegularExpression::fromPattern('{(.)}')->split('test', 2, true, true);

        $this->assertEquals(['t', 'e', 's', 't'], $actual);
    }

    public function testSplitOffsetCaptureSuccess(): void
    {
        $actual = RegularExpression::fromPattern('{(.)}')->splitOffsetCapture('test', 2);

        $this->assertEquals([new OffsetCapture('', 0), new OffsetCapture('est', 1)], $actual);
    }

    public function testSplitOffsetCaptureSuccessWithoutEmpty(): void
    {
        $actual = RegularExpression::fromPattern('{(.)}')->splitOffsetCapture('test', 2, true);

        $this->assertEquals([], $actual);
    }

    public function testSplitOffsetCaptureSuccessDelimiterCapture(): void
    {
        $actual = RegularExpression::fromPattern('{(.)}')->splitOffsetCapture('test', 2, false, true);

        $this->assertEquals([
            new OffsetCapture('', 0),
            new OffsetCapture('t', 0),
            new OffsetCapture('est', 1),
        ], $actual);
    }

    public function testSplitOffsetCaptureSuccessWithoutEmptyDelimiterCapture(): void
    {
        $actual = RegularExpression::fromPattern('{(.)}')->splitOffsetCapture('test', 2, true, true);

        $this->assertEquals([
            new OffsetCapture('t', 0),
            new OffsetCapture('e', 1),
            new OffsetCapture('s', 2),
            new OffsetCapture('t', 3),
        ], $actual);
    }

    public function testGrepSuccess(): void
    {
        $actual = RegularExpression::fromPattern('{.}')->grep(new Strings('test'));

        $this->assertEquals(['test'], $actual);
    }

    public function testGrepSuccessInvert(): void
    {
        $actual = RegularExpression::fromPattern('{.}')->grep(new Strings('test'), true);

        $this->assertEquals([], $actual);
    }
}
