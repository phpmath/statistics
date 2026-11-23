<?php

/**
 * phpmath / statistics (https://github.com/phpmath/statistics)
 *
 * @link https://github.com/phpmath/statistics for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Statistics\Tests\Sample;

use PHPMath\Statistics\Exception\InsufficientDataException;
use PHPMath\Statistics\Sample;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

#[CoversClass(Sample::class)]
#[CoversMethod(Sample::class, 'sampleVariance')]
final class SampleVarianceTest extends TestCase
{
    public function testReturnsTextbookVariance(): void
    {
        // Arrange
        $sample = new Sample([2, 4, 4, 4, 5, 5, 7, 9]);

        // Act
        $result = $sample->sampleVariance();

        // Assert
        self::assertEqualsWithDelta(32 / 7, $result, 1.0E-9);
    }

    public function testIsStableForLargeOffsets(): void
    {
        // Arrange
        $sample = new Sample([1000000004.0, 1000000007.0, 1000000013.0, 1000000016.0]);

        // Act
        $result = $sample->sampleVariance();

        // Assert
        self::assertEqualsWithDelta(30.0, $result, 1.0E-9);
    }

    public function testThrowsForSingleValue(): void
    {
        // Arrange
        $sample = new Sample([4.0]);
        $this->expectException(InsufficientDataException::class);

        // Act
        $sample->sampleVariance();
    }
}
