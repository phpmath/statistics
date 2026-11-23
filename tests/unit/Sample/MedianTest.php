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
#[CoversMethod(Sample::class, 'median')]
final class MedianTest extends TestCase
{
    public function testReturnsMiddleValueForOddCount(): void
    {
        // Arrange
        $sample = new Sample([7, 1, 3]);

        // Act
        $result = $sample->median();

        // Assert
        self::assertSame(3.0, $result);
    }

    public function testReturnsMeanOfMiddleValuesForEvenCount(): void
    {
        // Arrange
        $sample = new Sample([2, 4, 4, 4, 5, 5, 7, 9]);

        // Act
        $result = $sample->median();

        // Assert
        self::assertSame(4.5, $result);
    }

    public function testReturnsOnlyValueForSingleValue(): void
    {
        // Arrange
        $sample = new Sample([9]);

        // Act
        $result = $sample->median();

        // Assert
        self::assertSame(9.0, $result);
    }
}
