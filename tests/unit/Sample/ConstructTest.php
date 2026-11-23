<?php

/**
 * phpmath / statistics (https://github.com/phpmath/statistics)
 *
 * @link https://github.com/phpmath/statistics for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Statistics\Tests\Sample;

use PHPMath\Statistics\Exception\EmptySampleException;
use PHPMath\Statistics\Exception\NonFiniteValueException;
use PHPMath\Statistics\Exception\NonNumericValueException;
use PHPMath\Statistics\Sample;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

use const INF;
use const NAN;

#[CoversClass(Sample::class)]
#[CoversMethod(Sample::class, '__construct')]
final class ConstructTest extends TestCase
{
    public function testStoresValuesAsFloats(): void
    {
        // Arrange
        $values = [1, 2.5, 3];

        // Act
        $sample = new Sample($values);

        // Assert
        self::assertSame([1.0, 2.5, 3.0], $sample->toArray());
    }

    public function testThrowsOnEmptyList(): void
    {
        // Arrange
        $this->expectException(EmptySampleException::class);

        // Act
        $sample = new Sample([]);
    }

    public function testThrowsOnNan(): void
    {
        // Arrange
        $this->expectException(NonFiniteValueException::class);

        // Act
        $sample = new Sample([1.0, NAN]);
    }

    public function testThrowsOnInfinity(): void
    {
        // Arrange
        $this->expectException(NonFiniteValueException::class);

        // Act
        $sample = new Sample([1.0, INF]);
    }

    public function testThrowsOnNumericString(): void
    {
        // Arrange
        $values = [1.0, '2.5'];
        $this->expectException(NonNumericValueException::class);

        // Act
        new Sample($values); // @phpstan-ignore argument.type
    }

    public function testThrowsOnNonNumericString(): void
    {
        // Arrange
        $values = [1.0, 'abc'];
        $this->expectException(NonNumericValueException::class);

        // Act
        new Sample($values); // @phpstan-ignore argument.type
    }

    public function testThrowsOnNull(): void
    {
        // Arrange
        $values = [null];
        $this->expectException(NonNumericValueException::class);

        // Act
        new Sample($values); // @phpstan-ignore argument.type
    }

    public function testThrowsOnBoolean(): void
    {
        // Arrange
        $values = [true];
        $this->expectException(NonNumericValueException::class);

        // Act
        new Sample($values); // @phpstan-ignore argument.type
    }

    public function testNonNumericMessageNamesPosition(): void
    {
        // Arrange
        $values = [1.0, 2.0, 'abc'];

        try {
            // Act
            new Sample($values); // @phpstan-ignore argument.type
            self::fail('Expected an exception.');
        } catch (NonNumericValueException $exception) {
            // Assert
            self::assertSame('The value at position 3 is not an int or a float.', $exception->getMessage());
        }
    }

    public function testNonFiniteMessageNamesPosition(): void
    {
        // Arrange
        $values = [1.0, NAN];

        try {
            // Act
            new Sample($values);
            self::fail('Expected an exception.');
        } catch (NonFiniteValueException $exception) {
            // Assert
            self::assertSame('The value at position 2 is not finite.', $exception->getMessage());
        }
    }
}
