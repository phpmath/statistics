<?php

/**
 * phpmath / statistics (https://github.com/phpmath/statistics)
 *
 * @link https://github.com/phpmath/statistics for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Statistics\Tests\Sample;

use PHPMath\Statistics\Sample;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

use function array_fill;

use const INF;

#[CoversClass(Sample::class)]
#[CoversMethod(Sample::class, 'sum')]
final class SumTest extends TestCase
{
    public function testSumsValues(): void
    {
        // Arrange
        $sample = new Sample([2, 4, 4, 4, 5, 5, 7, 9]);

        // Act
        $result = $sample->sum();

        // Assert
        self::assertSame(40.0, $result);
    }

    public function testIsNotLostToCancellation(): void
    {
        // Arrange
        $sample = new Sample([1.0E16, 1.0, -1.0E16]);

        // Act
        $result = $sample->sum();

        // Assert
        self::assertSame(1.0, $result);
    }

    public function testSumsTenTenthsToExactlyOne(): void
    {
        // Arrange
        $sample = new Sample(array_fill(0, 10, 0.1));

        // Act
        $result = $sample->sum();

        // Assert
        self::assertSame(1.0, $result);
    }

    public function testSumsNegativeValues(): void
    {
        // Arrange
        $sample = new Sample([-1.5, -2.5, 1.0]);

        // Act
        $result = $sample->sum();

        // Assert
        self::assertSame(-3.0, $result);
    }

    public function testOverflowsToInfinity(): void
    {
        // Arrange
        $sample = new Sample([1.0E308, 1.0E308]);

        // Act
        $result = $sample->sum();

        // Assert
        self::assertSame(INF, $result);
    }
}
