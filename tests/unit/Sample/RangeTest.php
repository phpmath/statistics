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
#[CoversMethod(Sample::class, 'range')]
final class RangeTest extends TestCase
{
    public function testReturnsMaxMinusMin(): void
    {
        // Arrange
        $sample = new Sample([2, 4, 4, 4, 5, 5, 7, 9]);

        // Act
        $result = $sample->range();

        // Assert
        self::assertSame(7.0, $result);
    }

    public function testIsZeroForSingleValue(): void
    {
        // Arrange
        $sample = new Sample([4]);

        // Act
        $result = $sample->range();

        // Assert
        self::assertSame(0.0, $result);
    }
}
