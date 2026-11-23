<?php

/**
 * phpmath / statistics (https://github.com/phpmath/statistics)
 *
 * @link https://github.com/phpmath/statistics for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Statistics;

use PHPMath\Statistics\Exception\InvalidQuartilesException;
use PHPMath\Statistics\Exception\NonFiniteValueException;

use function is_finite;

/**
 * The three quartiles of a sample, see {@see Sample::percentile()} for the interpolation method.
 */
final readonly class Quartiles
{
    /**
     * @throws NonFiniteValueException when a quartile is NaN or infinite.
     * @throws InvalidQuartilesException when the quartiles are not in ascending order (first <= second <= third).
     */
    public function __construct(
        public float $first,
        public float $second,
        public float $third,
    ) {
        if (!is_finite($first) || !is_finite($second) || !is_finite($third)) {
            throw new NonFiniteValueException('The quartiles must be finite.');
        }

        if ($first > $second || $second > $third) {
            throw new InvalidQuartilesException('The quartiles must satisfy first <= second <= third.');
        }
    }
}
