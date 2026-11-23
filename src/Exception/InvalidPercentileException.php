<?php

/**
 * phpmath / statistics (https://github.com/phpmath/statistics)
 *
 * @link https://github.com/phpmath/statistics for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Statistics\Exception;

use InvalidArgumentException;

/**
 * Thrown when a percentile is not a number between 0 and 100.
 */
final class InvalidPercentileException extends InvalidArgumentException implements StatisticsException
{
}
