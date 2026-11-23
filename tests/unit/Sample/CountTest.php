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

use function count;

#[CoversClass(Sample::class)]
#[CoversMethod(Sample::class, 'count')]
final class CountTest extends TestCase
{
    public function testReturnsNumberOfValues(): void
    {
        // Arrange
        $sample = new Sample([3.0, 1.0, 2.0]);

        // Act
        $result = $sample->count();

        // Assert
        self::assertSame(3, $result);
    }

    public function testIsUsableWithCountFunction(): void
    {
        // Arrange
        $sample = new Sample([3.0, 1.0]);

        // Act
        $result = count($sample);

        // Assert
        self::assertSame(2, $result);
    }
}
