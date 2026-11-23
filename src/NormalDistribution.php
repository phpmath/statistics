<?php

/**
 * phpmath / statistics (https://github.com/phpmath/statistics)
 *
 * @link https://github.com/phpmath/statistics for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Statistics;

use PHPMath\Statistics\Exception\InvalidProbabilityException;
use PHPMath\Statistics\Exception\InvalidStandardDeviationException;
use PHPMath\Statistics\Exception\NonFiniteValueException;

use function exp;
use function is_finite;
use function is_nan;
use function sqrt;

use const M_PI;

/**
 * An immutable normal (Gaussian) distribution.
 *
 * The cumulative distribution uses the complementary error function, evaluated with a power series for small
 * arguments and a continued fraction for large arguments, which keeps the tails accurate (about 1e-14 relative).
 * The quantile is found by bisection on the cumulative distribution.
 */
final readonly class NormalDistribution
{
    private const float SERIES_LIMIT = 2.0;
    private const int FRACTION_TERMS = 200;
    private const int BISECTION_STEPS = 200;
    private const float QUANTILE_BOUND = 40.0;

    /**
     * @throws NonFiniteValueException when the mean is NaN or infinite.
     * @throws InvalidStandardDeviationException when the standard deviation is not finite and greater than zero.
     */
    public function __construct(
        public float $mean,
        public float $standardDeviation,
    ) {
        if (!is_finite($mean)) {
            throw new NonFiniteValueException('The mean must be finite.');
        }

        if (!is_finite($standardDeviation) || $standardDeviation <= 0.0) {
            throw new InvalidStandardDeviationException('The standard deviation must be finite and greater than zero.');
        }
    }

    /**
     * The probability density at x. Infinite values are allowed and give 0.0.
     *
     * @throws NonFiniteValueException when x is NaN.
     */
    public function pdf(float $x): float
    {
        if (is_nan($x)) {
            throw new NonFiniteValueException('The value x must not be NaN.');
        }

        $z = ($x - $this->mean) / $this->standardDeviation;

        return exp(-0.5 * $z * $z) / ($this->standardDeviation * sqrt(2 * M_PI));
    }

    /**
     * The cumulative probability P(X <= x). Infinite values are allowed: -INF gives 0.0 and INF gives 1.0.
     *
     * @throws NonFiniteValueException when x is NaN.
     */
    public function cdf(float $x): float
    {
        if (is_nan($x)) {
            throw new NonFiniteValueException('The value x must not be NaN.');
        }

        return 0.5 * self::erfc(-($x - $this->mean) / ($this->standardDeviation * sqrt(2.0)));
    }

    /**
     * The value x for which P(X <= x) equals the probability.
     *
     * @throws InvalidProbabilityException when the probability is not strictly between 0 and 1.
     */
    public function quantile(float $probability): float
    {
        if (!($probability > 0.0 && $probability < 1.0)) {
            throw new InvalidProbabilityException('The probability must be strictly between 0 and 1.');
        }

        $standard = new self(0.0, 1.0);
        $low = -self::QUANTILE_BOUND;
        $high = self::QUANTILE_BOUND;

        for ($step = 0; $step < self::BISECTION_STEPS; $step++) {
            $middle = ($low + $high) / 2;

            if ($middle === $low || $middle === $high) {
                break;
            }

            if ($standard->cdf($middle) < $probability) {
                $low = $middle;
            } else {
                $high = $middle;
            }
        }

        return $this->mean + $this->standardDeviation * ($low + $high) / 2;
    }

    private static function erfc(float $t): float
    {
        if ($t < 0.0) {
            return 2.0 - self::erfc(-$t);
        }

        if ($t < self::SERIES_LIMIT) {
            // erf(t) = 2 / sqrt(pi) * exp(-t^2) * sum(2^n * t^(2n+1) / (2n+1)!!), all terms positive.
            $term = $t;
            $sum = $t;
            for ($n = 1; $n < 200; $n++) {
                $term *= 2 * $t * $t / (2 * $n + 1);
                $sum += $term;

                if ($term < $sum * 1e-17) {
                    break;
                }
            }

            return 1.0 - 2.0 / sqrt(M_PI) * exp(-$t * $t) * $sum;
        }

        // Continued fraction: erfc(t) = exp(-t^2) / sqrt(pi) / (t + (1/2) / (t + 1 / (t + (3/2) / (t + ...)))).
        $fraction = $t;
        for ($k = self::FRACTION_TERMS; $k >= 1; $k--) {
            $fraction = $t + ($k / 2) / $fraction;
        }

        return exp(-$t * $t) / (sqrt(M_PI) * $fraction);
    }
}
