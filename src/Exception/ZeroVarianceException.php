<?php

/**
 * phpmath / statistics (https://github.com/phpmath/statistics)
 *
 * @link https://github.com/phpmath/statistics for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Statistics\Exception;

use DomainException;

/**
 * Thrown when a statistic is undefined because a sample has zero variance.
 */
final class ZeroVarianceException extends DomainException implements StatisticsException
{
}
