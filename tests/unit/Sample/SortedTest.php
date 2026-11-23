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
#[CoversMethod(Sample::class, 'sorted')]
final class SortedTest extends TestCase
{
    public function testReturnsAscendingCopy(): void
    {
        // Arrange
        $sample = new Sample([3.0, 1.0, 2.0]);

        // Act
        $result = $sample->sorted();

        // Assert
        self::assertSame([1.0, 2.0, 3.0], $result->toArray());
    }

    public function testDoesNotChangeOriginal(): void
    {
        // Arrange
        $sample = new Sample([3.0, 1.0]);

        // Act
        $sample->sorted();

        // Assert
        self::assertSame([3.0, 1.0], $sample->toArray());
    }
}
