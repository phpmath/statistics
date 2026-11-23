<?php

/**
 * phpmath / statistics (https://github.com/phpmath/statistics)
 *
 * @link https://github.com/phpmath/statistics for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Statistics\Tests\Sample;

use PHPMath\Statistics\Exception\InsufficientDataException;
use PHPMath\Statistics\Exception\SampleSizeMismatchException;
use PHPMath\Statistics\Sample;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

#[CoversClass(Sample::class)]
#[CoversMethod(Sample::class, 'sampleCovariance')]
final class SampleCovarianceTest extends TestCase
{
    public function testReturnsTextbookCovariance(): void
    {
        // Arrange
        $x = new Sample([1, 2, 3, 4, 5]);
        $y = new Sample([2, 4, 6, 8, 10]);

        // Act
        $result = $x->sampleCovariance($y);

        // Assert
        self::assertEqualsWithDelta(5.0, $result, 1.0E-9);
    }

    public function testThrowsOnSizeMismatch(): void
    {
        // Arrange
        $x = new Sample([1, 2, 3, 4, 5]);
        $y = new Sample([1, 2]);
        $this->expectException(SampleSizeMismatchException::class);

        // Act
        $x->sampleCovariance($y);
    }

    public function testThrowsForSingleValue(): void
    {
        // Arrange
        $x = new Sample([1]);
        $y = new Sample([2]);
        $this->expectException(InsufficientDataException::class);

        // Act
        $x->sampleCovariance($y);
    }
}
