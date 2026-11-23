<?php

/**
 * phpmath / statistics (https://github.com/phpmath/statistics)
 *
 * @link https://github.com/phpmath/statistics for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Statistics\Tests\Quartiles;

use PHPMath\Statistics\Exception\InvalidQuartilesException;
use PHPMath\Statistics\Exception\NonFiniteValueException;
use PHPMath\Statistics\Quartiles;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

use const INF;
use const NAN;

#[CoversClass(Quartiles::class)]
#[CoversMethod(Quartiles::class, '__construct')]
final class ConstructTest extends TestCase
{
    public function testStoresQuartiles(): void
    {
        // Act
        $quartiles = new Quartiles(1.0, 2.0, 3.0);

        // Assert
        self::assertSame([1.0, 2.0, 3.0], [$quartiles->first, $quartiles->second, $quartiles->third]);
    }

    public function testAcceptsEqualQuartiles(): void
    {
        // Arrange
        $expected = 2.0;

        // Act
        $quartiles = new Quartiles(2.0, 2.0, 2.0);

        // Assert
        self::assertSame($expected, $quartiles->second);
    }

    public function testThrowsWhenFirstIsGreaterThanSecond(): void
    {
        // Arrange
        $first = 3.0;
        $this->expectException(InvalidQuartilesException::class);

        // Act
        new Quartiles($first, 2.0, 4.0);
    }

    public function testThrowsWhenSecondIsGreaterThanThird(): void
    {
        // Arrange
        $second = 5.0;
        $this->expectException(InvalidQuartilesException::class);

        // Act
        new Quartiles(1.0, $second, 4.0);
    }

    public function testThrowsOnNan(): void
    {
        // Arrange
        $first = NAN;
        $this->expectException(NonFiniteValueException::class);

        // Act
        new Quartiles($first, 2.0, 3.0);
    }

    public function testThrowsOnInfinity(): void
    {
        // Arrange
        $third = INF;
        $this->expectException(NonFiniteValueException::class);

        // Act
        new Quartiles(1.0, 2.0, $third);
    }
}
