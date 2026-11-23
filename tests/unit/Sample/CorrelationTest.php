<?php

/**
 * phpmath / statistics (https://github.com/phpmath/statistics)
 *
 * @link https://github.com/phpmath/statistics for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Statistics\Tests\Sample;

use PHPMath\Statistics\Exception\SampleSizeMismatchException;
use PHPMath\Statistics\Exception\ZeroVarianceException;
use PHPMath\Statistics\Sample;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

#[CoversClass(Sample::class)]
#[CoversMethod(Sample::class, 'correlation')]
final class CorrelationTest extends TestCase
{
    public function testReturnsOneForPerfectPositiveRelation(): void
    {
        // Arrange
        $x = new Sample([1, 2, 3, 4, 5]);
        $y = new Sample([2, 4, 6, 8, 10]);

        // Act
        $result = $x->correlation($y);

        // Assert
        self::assertEqualsWithDelta(1.0, $result, 1.0E-12);
    }

    public function testReturnsMinusOneForPerfectNegativeRelation(): void
    {
        // Arrange
        $x = new Sample([1, 2, 3, 4, 5]);
        $y = new Sample([10, 8, 6, 4, 2]);

        // Act
        $result = $x->correlation($y);

        // Assert
        self::assertEqualsWithDelta(-1.0, $result, 1.0E-12);
    }

    public function testReturnsAnscombeQuartetCorrelation(): void
    {
        // Arrange
        $x = new Sample([10, 8, 13, 9, 11, 14, 6, 4, 12, 7, 5]);
        $y = new Sample([8.04, 6.95, 7.58, 8.81, 8.33, 9.96, 7.24, 4.26, 10.84, 4.82, 5.68]);

        // Act
        $result = $x->correlation($y);

        // Assert
        self::assertEqualsWithDelta(0.81642, $result, 1.0E-5);
    }

    public function testThrowsOnSizeMismatch(): void
    {
        // Arrange
        $x = new Sample([1, 2, 3, 4, 5]);
        $y = new Sample([1, 2]);
        $this->expectException(SampleSizeMismatchException::class);

        // Act
        $x->correlation($y);
    }

    public function testThrowsWhenOwnVarianceIsZero(): void
    {
        // Arrange
        $x = new Sample([3, 3, 3]);
        $y = new Sample([1, 2, 3]);
        $this->expectException(ZeroVarianceException::class);

        // Act
        $x->correlation($y);
    }

    public function testThrowsWhenOtherVarianceIsZero(): void
    {
        // Arrange
        $x = new Sample([1, 2, 3]);
        $y = new Sample([3, 3, 3]);
        $this->expectException(ZeroVarianceException::class);

        // Act
        $x->correlation($y);
    }
}
