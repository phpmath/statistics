<?php

/**
 * phpmath / statistics (https://github.com/phpmath/statistics)
 *
 * @link https://github.com/phpmath/statistics for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Statistics;

use PHPMath\Statistics\Exception\SampleSizeMismatchException;
use PHPMath\Statistics\Exception\ZeroVarianceException;

/**
 * An immutable simple linear regression (ordinary least squares): y = intercept + slope * x.
 *
 * The coefficient of determination is the squared Pearson correlation. When y is constant the fitted line is
 * horizontal and fits perfectly, so rSquared is defined as 1.
 */
final readonly class LinearRegression
{
    private function __construct(
        public float $slope,
        public float $intercept,
        public float $rSquared,
    ) {
    }

    /**
     * @throws SampleSizeMismatchException when the samples differ in size.
     * @throws ZeroVarianceException when x has zero variance, so no slope can be determined.
     */
    public static function fit(Sample $x, Sample $y): self
    {
        if ($x->count() !== $y->count()) {
            throw new SampleSizeMismatchException('Both samples must have the same number of values.');
        }

        $varianceX = $x->populationVariance();

        if ($varianceX === 0.0) {
            throw new ZeroVarianceException('A regression is undefined when x has zero variance.');
        }

        $slope = $x->populationCovariance($y) / $varianceX;
        $intercept = $y->mean() - $slope * $x->mean();
        $rSquared = $y->populationVariance() === 0.0 ? 1.0 : $x->correlation($y) ** 2;

        return new self($slope, $intercept, $rSquared);
    }

    public function predict(float $x): float
    {
        return $this->intercept + $this->slope * $x;
    }
}
