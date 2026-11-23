<?php

/**
 * phpmath / statistics (https://github.com/phpmath/statistics)
 *
 * @link https://github.com/phpmath/statistics for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Statistics\Tests\NormalDistribution;

use PHPMath\Statistics\Exception\InvalidStandardDeviationException;
use PHPMath\Statistics\Exception\NonFiniteValueException;
use PHPMath\Statistics\NormalDistribution;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

use const INF;
use const NAN;

#[CoversClass(NormalDistribution::class)]
#[CoversMethod(NormalDistribution::class, '__construct')]
final class ConstructTest extends TestCase
{
    public function testStoresParameters(): void
    {
        // Act
        $distribution = new NormalDistribution(2.0, 3.0);

        // Assert
        self::assertSame([2.0, 3.0], [$distribution->mean, $distribution->standardDeviation]);
    }

    public function testThrowsOnZeroStandardDeviation(): void
    {
        // Arrange
        $this->expectException(InvalidStandardDeviationException::class);

        // Act
        $distribution = new NormalDistribution(0.0, 0.0);
    }

    public function testThrowsOnNegativeStandardDeviation(): void
    {
        // Arrange
        $this->expectException(InvalidStandardDeviationException::class);

        // Act
        $distribution = new NormalDistribution(0.0, -1.0);
    }

    public function testThrowsOnInfiniteStandardDeviation(): void
    {
        // Arrange
        $this->expectException(InvalidStandardDeviationException::class);

        // Act
        $distribution = new NormalDistribution(0.0, INF);
    }

    public function testThrowsOnNonFiniteMean(): void
    {
        // Arrange
        $this->expectException(NonFiniteValueException::class);

        // Act
        $distribution = new NormalDistribution(NAN, 1.0);
    }
}
