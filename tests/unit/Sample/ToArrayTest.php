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
#[CoversMethod(Sample::class, 'toArray')]
final class ToArrayTest extends TestCase
{
    public function testReturnsValuesInOriginalOrder(): void
    {
        // Arrange
        $sample = new Sample([3.0, 1.0, 2.0]);

        // Act
        $result = $sample->toArray();

        // Assert
        self::assertSame([3.0, 1.0, 2.0], $result);
    }
}
