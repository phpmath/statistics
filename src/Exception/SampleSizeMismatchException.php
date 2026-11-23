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
 * Thrown when two samples must have the same number of values but do not.
 */
final class SampleSizeMismatchException extends InvalidArgumentException implements StatisticsException
{
}
