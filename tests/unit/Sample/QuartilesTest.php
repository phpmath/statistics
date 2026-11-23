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
#[CoversMethod(Sample::class, 'quartiles')]
final class QuartilesTest extends TestCase
{
    public function testReturnsAllThreeQuartiles(): void
    {
        // Arrange
        $sample = new Sample([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

        // Act
        $result = $sample->quartiles();

        // Assert
        self::assertEqualsWithDelta(3.25, $result->first, 1.0E-9);
        self::assertEqualsWithDelta(5.5, $result->second, 1.0E-9);
        self::assertEqualsWithDelta(7.75, $result->third, 1.0E-9);
    }

    public function testDoesNotThrowForExtremeValues(): void
    {
        // Arrange
        $sample = new Sample([-1.0E308, 1.0E308]);

        // Act
        $result = $sample->quartiles();

        // Assert
        self::assertSame(0.0, $result->second);
    }
}
