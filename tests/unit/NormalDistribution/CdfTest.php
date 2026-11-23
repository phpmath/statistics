<?php

/**
 * phpmath / statistics (https://github.com/phpmath/statistics)
 *
 * @link https://github.com/phpmath/statistics for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Statistics\Tests\NormalDistribution;

use PHPMath\Statistics\Exception\NonFiniteValueException;
use PHPMath\Statistics\NormalDistribution;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

use const INF;
use const NAN;

#[CoversClass(NormalDistribution::class)]
#[CoversMethod(NormalDistribution::class, 'cdf')]
final class CdfTest extends TestCase
{
    public function testReturnsHalfAtMean(): void
    {
        // Arrange
        $distribution = new NormalDistribution(5.0, 2.0);

        // Act
        $result = $distribution->cdf(5.0);

        // Assert
        self::assertEqualsWithDelta(0.5, $result, 1.0E-12);
    }

    public function testReturnsKnownValueAtOne(): void
    {
        // Arrange
        $distribution = new NormalDistribution(0.0, 1.0);

        // Act
        $result = $distribution->cdf(1.0);

        // Assert
        self::assertEqualsWithDelta(0.8413447460685429, $result, 1.0E-12);
    }

    public function testReturnsKnownValueAt196(): void
    {
        // Arrange
        $distribution = new NormalDistribution(0.0, 1.0);

        // Act
        $result = $distribution->cdf(1.96);

        // Assert
        self::assertEqualsWithDelta(0.9750021048517795, $result, 1.0E-12);
    }

    public function testReturnsKnownValueAtMinusOne(): void
    {
        // Arrange
        $distribution = new NormalDistribution(0.0, 1.0);

        // Act
        $result = $distribution->cdf(-1.0);

        // Assert
        self::assertEqualsWithDelta(0.15865525393145707, $result, 1.0E-12);
    }

    public function testReturnsKnownTailValue(): void
    {
        // Arrange
        $distribution = new NormalDistribution(0.0, 1.0);

        // Act
        $result = $distribution->cdf(-5.0);

        // Assert
        self::assertEqualsWithDelta(2.866515718791939E-7, $result, 1.0E-15);
    }

    public function testStandardizesArgument(): void
    {
        // Arrange
        $distribution = new NormalDistribution(100.0, 15.0);

        // Act
        $result = $distribution->cdf(115.0);

        // Assert
        self::assertEqualsWithDelta(0.8413447460685429, $result, 1.0E-12);
    }

    public function testThrowsOnNan(): void
    {
        // Arrange
        $distribution = new NormalDistribution(0.0, 1.0);
        $this->expectException(NonFiniteValueException::class);

        // Act
        $distribution->cdf(NAN);
    }

    public function testReturnsLowerLimitForNegativeInfinity(): void
    {
        // Arrange
        $distribution = new NormalDistribution(0.0, 1.0);

        // Act
        $result = $distribution->cdf(-INF);

        // Assert
        self::assertSame(0.0, $result);
    }

    public function testReturnsUpperLimitForPositiveInfinity(): void
    {
        // Arrange
        $distribution = new NormalDistribution(0.0, 1.0);

        // Act
        $result = $distribution->cdf(INF);

        // Assert
        self::assertSame(1.0, $result);
    }
}
