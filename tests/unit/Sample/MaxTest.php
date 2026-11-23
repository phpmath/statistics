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
#[CoversMethod(Sample::class, 'max')]
final class MaxTest extends TestCase
{
    public function testReturnsLargestValue(): void
    {
        // Arrange
        $sample = new Sample([3, -2, 7]);

        // Act
        $result = $sample->max();

        // Assert
        self::assertSame(7.0, $result);
    }
}
