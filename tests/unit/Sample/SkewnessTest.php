<?php

/**
 * phpmath / statistics (https://github.com/phpmath/statistics)
 *
 * @link https://github.com/phpmath/statistics for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Statistics\Tests\Sample;

use PHPMath\Statistics\Exception\ZeroVarianceException;
use PHPMath\Statistics\Sample;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

use function sqrt;

#[CoversClass(Sample::class)]
#[CoversMethod(Sample::class, 'skewness')]
final class SkewnessTest extends TestCase
{
    public function testReturnsPositiveSkewForRightTail(): void
    {
        // Arrange
        $sample = new Sample([1, 2, 3, 10]);

        // Act
        $result = $sample->skewness();

        // Assert
        self::assertEqualsWithDelta(45 / (12.5 * sqrt(12.5)), $result, 1.0E-9);
    }

    public function testReturnsZeroForSymmetricSample(): void
    {
        // Arrange
        $sample = new Sample([1, 2, 3, 4, 5]);

        // Act
        $result = $sample->skewness();

        // Assert
        self::assertEqualsWithDelta(0.0, $result, 1.0E-9);
    }

    public function testReturnsNegativeSkewForLeftTail(): void
    {
        // Arrange
        $sample = new Sample([-10, -3, -2, -1]);

        // Act
        $result = $sample->skewness();

        // Assert
        self::assertEqualsWithDelta(-45 / (12.5 * sqrt(12.5)), $result, 1.0E-9);
    }

    public function testThrowsForZeroVariance(): void
    {
        // Arrange
        $sample = new Sample([2.0, 2.0, 2.0]);
        $this->expectException(ZeroVarianceException::class);

        // Act
        $sample->skewness();
    }
}
