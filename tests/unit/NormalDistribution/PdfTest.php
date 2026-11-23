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
#[CoversMethod(NormalDistribution::class, 'pdf')]
final class PdfTest extends TestCase
{
    public function testReturnsPeakAtMean(): void
    {
        // Arrange
        $distribution = new NormalDistribution(0.0, 1.0);

        // Act
        $result = $distribution->pdf(0.0);

        // Assert
        self::assertEqualsWithDelta(0.3989422804014327, $result, 1.0E-12);
    }

    public function testReturnsStandardDensityAtOne(): void
    {
        // Arrange
        $distribution = new NormalDistribution(0.0, 1.0);

        // Act
        $result = $distribution->pdf(1.0);

        // Assert
        self::assertEqualsWithDelta(0.24197072451914337, $result, 1.0E-12);
    }

    public function testScalesWithStandardDeviation(): void
    {
        // Arrange
        $distribution = new NormalDistribution(10.0, 2.0);

        // Act
        $result = $distribution->pdf(10.0);

        // Assert
        self::assertEqualsWithDelta(0.19947114020071635, $result, 1.0E-12);
    }

    public function testThrowsOnNan(): void
    {
        // Arrange
        $distribution = new NormalDistribution(0.0, 1.0);
        $this->expectException(NonFiniteValueException::class);

        // Act
        $distribution->pdf(NAN);
    }

    public function testReturnsLowerLimitForNegativeInfinity(): void
    {
        // Arrange
        $distribution = new NormalDistribution(0.0, 1.0);

        // Act
        $result = $distribution->pdf(-INF);

        // Assert
        self::assertSame(0.0, $result);
    }

    public function testReturnsUpperLimitForPositiveInfinity(): void
    {
        // Arrange
        $distribution = new NormalDistribution(0.0, 1.0);

        // Act
        $result = $distribution->pdf(INF);

        // Assert
        self::assertSame(0.0, $result);
    }
}
