<?php

/**
 * phpmath / statistics (https://github.com/phpmath/statistics)
 *
 * @link https://github.com/phpmath/statistics for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Statistics\Tests\Sample;

use PHPMath\Statistics\Exception\NonFiniteValueException;
use PHPMath\Statistics\Exception\NonNumericValueException;
use PHPMath\Statistics\Sample;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

use const INF;

#[CoversClass(Sample::class)]
#[CoversMethod(Sample::class, 'map')]
final class MapTest extends TestCase
{
    public function testAppliesCallbackToEveryValue(): void
    {
        // Arrange
        $sample = new Sample([1.0, 2.0, 3.0]);

        // Act
        $result = $sample->map(static fn (float $value): float => $value * $value);

        // Assert
        self::assertSame([1.0, 4.0, 9.0], $result->toArray());
    }

    public function testDoesNotChangeOriginal(): void
    {
        // Arrange
        $sample = new Sample([1.0, 2.0]);

        // Act
        $sample->map(static fn (float $value): float => $value + 1);

        // Assert
        self::assertSame([1.0, 2.0], $sample->toArray());
    }

    public function testThrowsWhenCallbackReturnsNonFiniteValue(): void
    {
        // Arrange
        $sample = new Sample([1.0, 2.0]);
        $this->expectException(NonFiniteValueException::class);

        // Act
        $sample->map(static fn (float $value): float => INF);
    }

    public function testThrowsWhenCallbackReturnsString(): void
    {
        // Arrange
        $sample = new Sample([1.0, 2.0]);
        $this->expectException(NonNumericValueException::class);

        // Act
        $sample->map(static fn (float $value): string => 'abc'); // @phpstan-ignore argument.type
    }
}
