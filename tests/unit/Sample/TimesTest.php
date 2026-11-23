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
#[CoversMethod(Sample::class, 'times')]
final class TimesTest extends TestCase
{
    public function testMultipliesEveryValue(): void
    {
        // Arrange
        $sample = new Sample([1.0, -2.0]);

        // Act
        $result = $sample->times(3.0);

        // Assert
        self::assertSame([3.0, -6.0], $result->toArray());
    }
}
