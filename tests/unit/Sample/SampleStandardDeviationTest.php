<?php

/**
 * phpmath / statistics (https://github.com/phpmath/statistics)
 *
 * @link https://github.com/phpmath/statistics for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Statistics\Tests\Sample;

use PHPMath\Statistics\Exception\InsufficientDataException;
use PHPMath\Statistics\Sample;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

use function sqrt;

#[CoversClass(Sample::class)]
#[CoversMethod(Sample::class, 'sampleStandardDeviation')]
final class SampleStandardDeviationTest extends TestCase
{
    public function testReturnsTextbookStandardDeviation(): void
    {
        // Arrange
        $sample = new Sample([2, 4, 4, 4, 5, 5, 7, 9]);

        // Act
        $result = $sample->sampleStandardDeviation();

        // Assert
        self::assertEqualsWithDelta(sqrt(32 / 7), $result, 1.0E-9);
    }

    public function testThrowsForSingleValue(): void
    {
        // Arrange
        $sample = new Sample([4.0]);
        $this->expectException(InsufficientDataException::class);

        // Act
        $sample->sampleStandardDeviation();
    }

    public function testMessageNamesStandardDeviation(): void
    {
        // Arrange
        $sample = new Sample([4.0]);

        try {
            // Act
            $sample->sampleStandardDeviation();
            self::fail('Expected an exception.');
        } catch (InsufficientDataException $exception) {
            // Assert
            self::assertSame('The sample standard deviation requires at least two values.', $exception->getMessage());
        }
    }
}
