<?php

/**
 * phpmath / statistics (https://github.com/phpmath/statistics)
 *
 * @link https://github.com/phpmath/statistics for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Statistics\Tests\Sample;

use PHPMath\Statistics\Exception\InvalidPercentileException;
use PHPMath\Statistics\Sample;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

use const NAN;

#[CoversClass(Sample::class)]
#[CoversMethod(Sample::class, 'percentile')]
final class PercentileTest extends TestCase
{
    public function testInterpolatesLinearly(): void
    {
        // Arrange
        $sample = new Sample([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

        // Act
        $result = $sample->percentile(25);

        // Assert
        self::assertEqualsWithDelta(3.25, $result, 1.0E-9);
    }

    public function testReturnsMedianAtFifty(): void
    {
        // Arrange
        $sample = new Sample([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

        // Act
        $result = $sample->percentile(50);

        // Assert
        self::assertEqualsWithDelta(5.5, $result, 1.0E-9);
    }

    public function testReturnsMinimumAtZero(): void
    {
        // Arrange
        $sample = new Sample([4, 2, 9]);

        // Act
        $result = $sample->percentile(0);

        // Assert
        self::assertSame(2.0, $result);
    }

    public function testReturnsMaximumAtHundred(): void
    {
        // Arrange
        $sample = new Sample([4, 2, 9]);

        // Act
        $result = $sample->percentile(100);

        // Assert
        self::assertSame(9.0, $result);
    }

    public function testReturnsOnlyValueForSingleValue(): void
    {
        // Arrange
        $sample = new Sample([4]);

        // Act
        $result = $sample->percentile(37.5);

        // Assert
        self::assertSame(4.0, $result);
    }

    public function testThrowsAboveHundred(): void
    {
        // Arrange
        $sample = new Sample([1.0, 2.0]);
        $this->expectException(InvalidPercentileException::class);

        // Act
        $sample->percentile(100.5);
    }

    public function testThrowsBelowZero(): void
    {
        // Arrange
        $sample = new Sample([1.0, 2.0]);
        $this->expectException(InvalidPercentileException::class);

        // Act
        $sample->percentile(-0.1);
    }

    public function testThrowsOnNan(): void
    {
        // Arrange
        $sample = new Sample([1.0, 2.0]);
        $this->expectException(InvalidPercentileException::class);

        // Act
        $sample->percentile(NAN);
    }

    public function testDoesNotOverflowBetweenExtremeValues(): void
    {
        // Arrange
        $sample = new Sample([-1.0E308, 1.0E308]);

        // Act
        $result = $sample->percentile(75);

        // Assert
        self::assertEqualsWithDelta(5.0E307, $result, 1.0E293);
    }
}
