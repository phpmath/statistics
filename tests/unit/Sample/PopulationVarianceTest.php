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

use const INF;

#[CoversClass(Sample::class)]
#[CoversMethod(Sample::class, 'populationVariance')]
final class PopulationVarianceTest extends TestCase
{
    public function testReturnsTextbookVariance(): void
    {
        // Arrange
        $sample = new Sample([2, 4, 4, 4, 5, 5, 7, 9]);

        // Act
        $result = $sample->populationVariance();

        // Assert
        self::assertSame(4.0, $result);
    }

    public function testIsZeroForSingleValue(): void
    {
        // Arrange
        $sample = new Sample([4]);

        // Act
        $result = $sample->populationVariance();

        // Assert
        self::assertSame(0.0, $result);
    }

    public function testIsStableForLargeOffsets(): void
    {
        // Arrange
        $sample = new Sample([1000000004.0, 1000000007.0, 1000000013.0, 1000000016.0]);

        // Act
        $result = $sample->populationVariance();

        // Assert
        self::assertEqualsWithDelta(22.5, $result, 1.0E-9);
    }

    public function testDoesNotOverflowWhenResultIsRepresentable(): void
    {
        // Arrange
        $sample = new Sample([1.0E150, -1.0E150]);

        // Act
        $result = $sample->populationVariance();

        // Assert
        self::assertEqualsWithDelta(1.0E300, $result, 1.0E288);
    }

    public function testOverflowsToInfinityWhenResultIsNotRepresentable(): void
    {
        // Arrange
        $sample = new Sample([1.0E200, -1.0E200]);

        // Act
        $result = $sample->populationVariance();

        // Assert
        self::assertSame(INF, $result);
    }

    public function testIsZeroForLargeEqualValues(): void
    {
        // Arrange
        $sample = new Sample([1.0E308, 1.0E308]);

        // Act
        $result = $sample->populationVariance();

        // Assert
        self::assertSame(0.0, $result);
    }
}
