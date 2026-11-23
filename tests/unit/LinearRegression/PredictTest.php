<?php

/**
 * phpmath / statistics (https://github.com/phpmath/statistics)
 *
 * @link https://github.com/phpmath/statistics for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Statistics\Tests\LinearRegression;

use PHPMath\Statistics\LinearRegression;
use PHPMath\Statistics\Sample;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\TestCase;

#[CoversClass(LinearRegression::class)]
#[CoversMethod(LinearRegression::class, 'predict')]
final class PredictTest extends TestCase
{
    public function testPredictsOnFittedLine(): void
    {
        // Arrange
        $regression = LinearRegression::fit(new Sample([1, 2, 3, 4, 5]), new Sample([3, 5, 7, 9, 11]));

        // Act
        $result = $regression->predict(10.0);

        // Assert
        self::assertEqualsWithDelta(21.0, $result, 1.0E-12);
    }
}
