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

#[CoversClass(Sample::class)]
#[CoversMethod(Sample::class, 'mean')]
final class MeanTest extends TestCase
{
    public function testReturnsAverage(): void
    {
        // Arrange
        $sample = new Sample([2, 4, 4, 4, 5, 5, 7, 9]);

        // Act
        $result = $sample->mean();

        // Assert
        self::assertSame(5.0, $result);
    }

    public function testAveragesFractions(): void
    {
        // Arrange
        $sample = new Sample([0.5, 1.5, 2.5, 3.5]);

        // Act
        $result = $sample->mean();

        // Assert
        self::assertEqualsWithDelta(2.0, $result, 1.0E-9);
    }

    public function testDoesNotOverflowForLargeValues(): void
    {
        // Arrange
        $sample = new Sample([1.0E308, 1.0E308]);

        // Act
        $result = $sample->mean();

        // Assert
        self::assertSame(1.0E308, $result);
    }

    public function testDoesNotOverflowForOppositeLargeValues(): void
    {
        // Arrange
        $sample = new Sample([1.0E308, -1.0E308]);

        // Act
        $result = $sample->mean();

        // Assert
        self::assertSame(0.0, $result);
    }
}
