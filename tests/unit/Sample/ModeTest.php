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
#[CoversMethod(Sample::class, 'mode')]
final class ModeTest extends TestCase
{
    public function testReturnsSingleMode(): void
    {
        // Arrange
        $sample = new Sample([2, 4, 4, 4, 5, 5, 7, 9]);

        // Act
        $result = $sample->mode();

        // Assert
        self::assertSame([4.0], $result);
    }

    public function testReturnsAllModesInAscendingOrder(): void
    {
        // Arrange
        $sample = new Sample([5, 1, 5, 1, 3]);

        // Act
        $result = $sample->mode();

        // Assert
        self::assertSame([1.0, 5.0], $result);
    }

    public function testReturnsAllValuesWhenAllAreUnique(): void
    {
        // Arrange
        $sample = new Sample([3, 1, 2]);

        // Act
        $result = $sample->mode();

        // Assert
        self::assertSame([1.0, 2.0, 3.0], $result);
    }

    public function testDistinguishesFractionalValues(): void
    {
        // Arrange
        $sample = new Sample([1.1, 1.2, 1.2]);

        // Act
        $result = $sample->mode();

        // Assert
        self::assertSame([1.2], $result);
    }
}
