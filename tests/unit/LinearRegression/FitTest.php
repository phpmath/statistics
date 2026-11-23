<?php

/**
 * phpmath / statistics (https://github.com/phpmath/statistics)
 *
 * @link https://github.com/phpmath/statistics for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Statistics\Tests\LinearRegression;

use PHPMath\Statistics\Exception\SampleSizeMismatchException;
use PHPMath\Statistics\Exception\ZeroVarianceException;
use PHPMath\Statistics\LinearRegression;
use PHPMath\Statistics\Sample;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

#[CoversClass(LinearRegression::class)]
#[CoversMethod(LinearRegression::class, 'fit')]
final class FitTest extends TestCase
{
    public function testFitsExactLine(): void
    {
        // Arrange
        $x = new Sample([1, 2, 3, 4, 5]);
        $y = new Sample([3, 5, 7, 9, 11]);

        // Act
        $result = LinearRegression::fit($x, $y);

        // Assert
        self::assertEqualsWithDelta(2.0, $result->slope, 1.0E-12);
        self::assertEqualsWithDelta(1.0, $result->intercept, 1.0E-12);
        self::assertEqualsWithDelta(1.0, $result->rSquared, 1.0E-12);
    }

    public function testFitsAnscombeQuartet(): void
    {
        // Arrange
        $x = new Sample([10, 8, 13, 9, 11, 14, 6, 4, 12, 7, 5]);
        $y = new Sample([8.04, 6.95, 7.58, 8.81, 8.33, 9.96, 7.24, 4.26, 10.84, 4.82, 5.68]);

        // Act
        $result = LinearRegression::fit($x, $y);

        // Assert
        self::assertEqualsWithDelta(0.5001, $result->slope, 1.0E-4);
        self::assertEqualsWithDelta(3.0001, $result->intercept, 1.0E-4);
        self::assertEqualsWithDelta(0.6665, $result->rSquared, 1.0E-4);
    }

    public function testFitsNegativeSlope(): void
    {
        // Arrange
        $x = new Sample([1, 2, 3, 4, 5]);
        $y = new Sample([10, 8, 6, 4, 2]);

        // Act
        $result = LinearRegression::fit($x, $y);

        // Assert
        self::assertEqualsWithDelta(-2.0, $result->slope, 1.0E-12);
        self::assertEqualsWithDelta(12.0, $result->intercept, 1.0E-12);
    }

    public function testDefinesRSquaredAsOneForConstantY(): void
    {
        // Arrange
        $x = new Sample([1, 2, 3, 4, 5]);
        $y = new Sample([4, 4, 4, 4, 4]);

        // Act
        $result = LinearRegression::fit($x, $y);

        // Assert
        self::assertSame(1.0, $result->rSquared);
    }

    public function testThrowsOnSizeMismatch(): void
    {
        // Arrange
        $x = new Sample([1, 2, 3, 4, 5]);
        $y = new Sample([1, 2]);
        $this->expectException(SampleSizeMismatchException::class);

        // Act
        LinearRegression::fit($x, $y);
    }

    public function testThrowsWhenXHasZeroVariance(): void
    {
        // Arrange
        $x = new Sample([2, 2, 2]);
        $y = new Sample([1, 2, 3]);
        $this->expectException(ZeroVarianceException::class);

        // Act
        LinearRegression::fit($x, $y);
    }
}
