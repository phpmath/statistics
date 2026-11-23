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
 * Thrown when a sample is created without any values.
 */
final class EmptySampleException extends InvalidArgumentException implements StatisticsException
{
}
