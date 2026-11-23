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
 * Thrown when a distribution parameter is not valid, for example a standard deviation that is not positive.
 */
final class InvalidStandardDeviationException extends InvalidArgumentException implements StatisticsException
{
}
