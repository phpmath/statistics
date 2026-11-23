<?php

/**
 * phpmath / statistics (https://github.com/phpmath/statistics)
 *
 * @link https://github.com/phpmath/statistics for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Statistics\Tests\NormalDistribution;

use PHPMath\Statistics\Exception\InvalidProbabilityException;
use PHPMath\Statistics\NormalDistribution;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

#[CoversClass(NormalDistribution::class)]
#[CoversMethod(NormalDistribution::class, 'quantile')]
final class QuantileTest extends TestCase
{
    public function testReturnsMeanAtHalf(): void
    {
        // Arrange
        $distribution = new NormalDistribution(3.0, 2.0);

        // Act
        $result = $distribution->quantile(0.5);

        // Assert
        self::assertEqualsWithDelta(3.0, $result, 1.0E-12);
    }

    public function testReturnsKnownCriticalValue(): void
    {
        // Arrange
        $distribution = new NormalDistribution(0.0, 1.0);

        // Act
        $result = $distribution->quantile(0.975);

        // Assert
        self::assertEqualsWithDelta(1.959963984540054, $result, 1.0E-12);
    }

    public function testReturnsKnownLowerCriticalValue(): void
    {
        // Arrange
        $distribution = new NormalDistribution(0.0, 1.0);

        // Act
        $result = $distribution->quantile(0.025);

        // Assert
        self::assertEqualsWithDelta(-1.959963984540054, $result, 1.0E-12);
    }

    public function testScalesWithParameters(): void
    {
        // Arrange
        $distribution = new NormalDistribution(100.0, 15.0);

        // Act
        $result = $distribution->quantile(0.8413447460685429);

        // Assert
        self::assertEqualsWithDelta(115.0, $result, 1.0E-9);
    }

    public function testThrowsAtZero(): void
    {
        // Arrange
        $distribution = new NormalDistribution(0.0, 1.0);
        $this->expectException(InvalidProbabilityException::class);

        // Act
        $distribution->quantile(0.0);
    }

    public function testThrowsAtOne(): void
    {
        // Arrange
        $distribution = new NormalDistribution(0.0, 1.0);
        $this->expectException(InvalidProbabilityException::class);

        // Act
        $distribution->quantile(1.0);
    }
}
