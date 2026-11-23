<?php

/**
 * phpmath / statistics (https://github.com/phpmath/statistics)
 *
 * @link https://github.com/phpmath/statistics for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Statistics\Tests\Sample;

use PHPMath\Statistics\Exception\SampleSizeMismatchException;
use PHPMath\Statistics\Sample;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

#[CoversClass(Sample::class)]
#[CoversMethod(Sample::class, 'populationCovariance')]
final class PopulationCovarianceTest extends TestCase
{
    public function testReturnsTextbookCovariance(): void
    {
        // Arrange
        $x = new Sample([1, 2, 3, 4, 5]);
        $y = new Sample([2, 4, 6, 8, 10]);

        // Act
        $result = $x->populationCovariance($y);

        // Assert
        self::assertEqualsWithDelta(4.0, $result, 1.0E-9);
    }

    public function testIsNegativeForInverseRelation(): void
    {
        // Arrange
        $x = new Sample([1, 2, 3, 4, 5]);
        $y = new Sample([10, 8, 6, 4, 2]);

        // Act
        $result = $x->populationCovariance($y);

        // Assert
        self::assertEqualsWithDelta(-4.0, $result, 1.0E-9);
    }

    public function testThrowsOnSizeMismatch(): void
    {
        // Arrange
        $x = new Sample([1, 2, 3, 4, 5]);
        $y = new Sample([1, 2]);
        $this->expectException(SampleSizeMismatchException::class);

        // Act
        $x->populationCovariance($y);
    }
}
