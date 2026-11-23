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
 * Thrown when a probability is not strictly between 0 and 1.
 */
final class InvalidProbabilityException extends InvalidArgumentException implements StatisticsException
{
}
