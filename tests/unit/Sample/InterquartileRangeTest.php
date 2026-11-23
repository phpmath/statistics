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
#[CoversMethod(Sample::class, 'interquartileRange')]
final class InterquartileRangeTest extends TestCase
{
    public function testReturnsThirdMinusFirstQuartile(): void
    {
        // Arrange
        $sample = new Sample([1, 2, 3, 4, 5, 6, 7, 8, 9, 10]);

        // Act
        $result = $sample->interquartileRange();

        // Assert
        self::assertEqualsWithDelta(4.5, $result, 1.0E-9);
    }
}
