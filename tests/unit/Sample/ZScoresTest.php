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

#[CoversClass(Sample::class)]
#[CoversMethod(Sample::class, 'zScores')]
final class ZScoresTest extends TestCase
{
    public function testReturnsStandardScores(): void
    {
        // Arrange
        $sample = new Sample([2, 4, 4, 4, 5, 5, 7, 9]);

        // Act
        $result = $sample->zScores();

        // Assert
        self::assertSame([-1.5, -0.5, -0.5, -0.5, 0.0, 0.0, 1.0, 2.0], $result->toArray());
    }

    public function testThrowsForZeroVariance(): void
    {
        // Arrange
        $sample = new Sample([2.0, 2.0]);
        $this->expectException(ZeroVarianceException::class);

        // Act
        $sample->zScores();
    }
}
