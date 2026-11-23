<?php

/**
 * phpmath / statistics (https://github.com/phpmath/statistics)
 *
 * @link https://github.com/phpmath/statistics for the canonical source repository
 */

declare(strict_types=1);

namespace PHPMath\Statistics;

use Countable;
use PHPMath\Statistics\Exception\EmptySampleException;
use PHPMath\Statistics\Exception\InsufficientDataException;
use PHPMath\Statistics\Exception\InvalidPercentileException;
use PHPMath\Statistics\Exception\NonFiniteValueException;
use PHPMath\Statistics\Exception\NonNumericValueException;
use PHPMath\Statistics\Exception\SampleSizeMismatchException;
use PHPMath\Statistics\Exception\ZeroVarianceException;

use function abs;
use function array_map;
use function count;
use function floor;
use function intdiv;
use function is_finite;
use function is_float;
use function is_int;
use function max;
use function min;
use function sort;
use function sprintf;
use function sqrt;

/**
 * An immutable, non-empty list of finite floats with descriptive statistics.
 *
 * Numerical methods (results are not checked beyond that: a sum, a variance or a product can overflow to INF):
 *
 * - Sums use Neumaier's improved Kahan summation, which keeps a running compensation for lost low-order bits.
 * - The mean and the variances use Welford's running algorithm, so the mean never overflows for finite values.
 * - Variance, skewness and covariance use single-pass Welford style updates (with the Terriberry/Pebay extension for
 *   the third moment) so they do not suffer from catastrophic cancellation when values are large and close together.
 */
final readonly class Sample implements Countable
{
    /** @var list<float> */
    private array $values;

    /**
     * @param array<int|float> $values the values, at least one, all of type int or float and finite
     *
     * @throws EmptySampleException when no values are given.
     * @throws NonNumericValueException when a value is not an int or a float (numeric strings are rejected too).
     * @throws NonFiniteValueException when a value is NaN or infinite.
     */
    public function __construct(array $values)
    {
        if ($values === []) {
            throw new EmptySampleException('A sample requires at least one value.');
        }

        $floats = [];
        $position = 0;
        foreach ($values as $value) {
            /** @var mixed $value the type is not trusted at runtime */
            $position++;

            if (!is_int($value) && !is_float($value)) {
                throw new NonNumericValueException(
                    sprintf('The value at position %d is not an int or a float.', $position),
                );
            }

            $float = (float) $value;
            if (!is_finite($float)) {
                throw new NonFiniteValueException(
                    sprintf('The value at position %d is not finite.', $position),
                );
            }

            $floats[] = $float;
        }

        $this->values = $floats;
    }

    /** @return list<float> */
    public function toArray(): array
    {
        return $this->values;
    }

    public function count(): int
    {
        return count($this->values);
    }

    /**
     * The sum, computed with compensated (Neumaier) summation. The result is not checked: the sum of large values
     * can overflow to infinity, even though {@see self::mean()} of the same values is finite.
     */
    public function sum(): float
    {
        $sum = 0.0;
        $compensation = 0.0;

        foreach ($this->values as $value) {
            $total = $sum + $value;

            if (abs($sum) >= abs($value)) {
                $compensation += $sum - $total + $value;
            } else {
                $compensation += $value - $total + $sum;
            }

            $sum = $total;
        }

        if (!is_finite($sum)) {
            return $sum;
        }

        return $sum + $compensation;
    }

    public function min(): float
    {
        $min = $this->values[0];
        foreach ($this->values as $value) {
            if ($value >= $min) {
                continue;
            }

            $min = $value;
        }

        return $min;
    }

    public function max(): float
    {
        $max = $this->values[0];
        foreach ($this->values as $value) {
            if ($value <= $max) {
                continue;
            }

            $max = $value;
        }

        return $max;
    }

    public function range(): float
    {
        return $this->max() - $this->min();
    }

    /**
     * The arithmetic mean, computed with a running (Welford) update, so it does not overflow for finite values.
     */
    public function mean(): float
    {
        [$mean] = $this->welford();

        return $mean;
    }

    /**
     * The median: the middle value, or the mean of the two middle values for an even count.
     */
    public function median(): float
    {
        $sorted = $this->sortedValues();
        $count = count($sorted);
        $middle = intdiv($count, 2);

        if ($count % 2 === 1) {
            return $sorted[$middle];
        }

        return ($sorted[$middle - 1] + $sorted[$middle]) / 2;
    }

    /**
     * All values that occur most often, in ascending order. When every value is unique, every value is a mode.
     *
     * @return list<float>
     */
    public function mode(): array
    {
        $sorted = $this->sortedValues();
        $modes = [];
        $best = 0;
        $run = 0;
        $previous = null;

        foreach ($sorted as $value) {
            $run = $previous === $value ? $run + 1 : 1;
            $previous = $value;

            if ($run > $best) {
                $best = $run;
                $modes = [$value];
            } elseif ($run === $best) {
                $modes[] = $value;
            }
        }

        return $modes;
    }

    /**
     * The population variance: the sum of squared deviations divided by n.
     */
    public function populationVariance(): float
    {
        [, $m2] = $this->welford();

        return $m2 / $this->count();
    }

    /**
     * The sample variance (Bessel's correction): the sum of squared deviations divided by n - 1.
     *
     * @throws InsufficientDataException when the sample has fewer than two values.
     */
    public function sampleVariance(): float
    {
        if ($this->count() < 2) {
            throw new InsufficientDataException('The sample variance requires at least two values.');
        }

        [, $m2] = $this->welford();

        return $m2 / ($this->count() - 1);
    }

    public function populationStandardDeviation(): float
    {
        return sqrt($this->populationVariance());
    }

    /** @throws InsufficientDataException when the sample has fewer than two values. */
    public function sampleStandardDeviation(): float
    {
        if ($this->count() < 2) {
            throw new InsufficientDataException('The sample standard deviation requires at least two values.');
        }

        return sqrt($this->sampleVariance());
    }

    /**
     * The percentile using linear interpolation between closest ranks (Hyndman and Fan type 7, the default of R and
     * NumPy and the same as Excel's PERCENTILE.INC): the rank is p / 100 * (n - 1) on the sorted values, zero based.
     *
     * @param float $p a percentile between 0 and 100 inclusive
     *
     * @throws InvalidPercentileException when the percentile is outside of 0 to 100.
     */
    public function percentile(float $p): float
    {
        if (!($p >= 0.0 && $p <= 100.0)) {
            throw new InvalidPercentileException('A percentile must be between 0 and 100.');
        }

        $sorted = $this->sortedValues();
        $rank = $p / 100 * (count($sorted) - 1);
        $lower = (int) floor($rank);
        $fraction = $rank - $lower;

        if ($fraction === 0.0) {
            return $sorted[$lower];
        }

        $difference = $sorted[$lower + 1] - $sorted[$lower];
        if (!is_finite($difference)) {
            return $sorted[$lower] * (1 - $fraction) + $sorted[$lower + 1] * $fraction;
        }

        return $sorted[$lower] + $fraction * $difference;
    }

    /**
     * The first, second (median) and third quartile, using the interpolation of {@see self::percentile()}.
     */
    public function quartiles(): Quartiles
    {
        return new Quartiles($this->percentile(25), $this->percentile(50), $this->percentile(75));
    }

    public function interquartileRange(): float
    {
        $quartiles = $this->quartiles();

        return $quartiles->third - $quartiles->first;
    }

    /**
     * The population skewness (Fisher-Pearson coefficient g1): the third central moment divided by the
     * population standard deviation cubed.
     *
     * @throws ZeroVarianceException when all values are equal.
     */
    public function skewness(): float
    {
        $count = 0;
        $mean = 0.0;
        $m2 = 0.0;
        $m3 = 0.0;

        foreach ($this->values as $value) {
            $count++;
            $delta = $value - $mean;
            $deltaN = $delta / $count;
            $term = $delta * $deltaN * ($count - 1);
            $mean += $deltaN;
            $m3 += $term * $deltaN * ($count - 2) - 3 * $deltaN * $m2;
            $m2 += $term;
        }

        if ($m2 === 0.0) {
            throw new ZeroVarianceException('The skewness is undefined when all values are equal.');
        }

        return sqrt($count) * $m3 / ($m2 * sqrt($m2));
    }

    /** @throws SampleSizeMismatchException when the samples differ in size. */
    public function populationCovariance(self $other): float
    {
        [$comoment] = $this->comoments($other);

        return $comoment / $this->count();
    }

    /**
     * @throws SampleSizeMismatchException when the samples differ in size.
     * @throws InsufficientDataException when the samples have fewer than two values.
     */
    public function sampleCovariance(self $other): float
    {
        [$comoment] = $this->comoments($other);

        if ($this->count() < 2) {
            throw new InsufficientDataException('The sample covariance requires at least two values.');
        }

        return $comoment / ($this->count() - 1);
    }

    /**
     * The Pearson correlation coefficient, between -1 and 1.
     *
     * @throws SampleSizeMismatchException when the samples differ in size.
     * @throws ZeroVarianceException when either sample has zero variance.
     */
    public function correlation(self $other): float
    {
        [$comoment, $m2x, $m2y] = $this->comoments($other);

        if ($m2x === 0.0 || $m2y === 0.0) {
            throw new ZeroVarianceException('The correlation is undefined when a sample has zero variance.');
        }

        $correlation = $comoment / (sqrt($m2x) * sqrt($m2y));

        return max(-1.0, min(1.0, $correlation));
    }

    /**
     * The standard scores (x - mean) / population standard deviation.
     *
     * @throws ZeroVarianceException when all values are equal.
     */
    public function zScores(): self
    {
        $standardDeviation = $this->populationStandardDeviation();

        if ($standardDeviation === 0.0) {
            throw new ZeroVarianceException('The z-scores are undefined when all values are equal.');
        }

        $mean = $this->mean();

        return $this->map(static fn (float $value): float => ($value - $mean) / $standardDeviation);
    }

    /**
     * @param callable(float): float $callback
     *
     * @throws NonFiniteValueException when the callback returns NaN or infinity.
     */
    public function map(callable $callback): self
    {
        return new self(array_map($callback, $this->values));
    }

    public function plus(float $amount): self
    {
        return $this->map(static fn (float $value): float => $value + $amount);
    }

    public function times(float $factor): self
    {
        return $this->map(static fn (float $value): float => $value * $factor);
    }

    public function sorted(): self
    {
        return new self($this->sortedValues());
    }

    /** @return list<float> */
    private function sortedValues(): array
    {
        $sorted = $this->values;
        sort($sorted);

        return $sorted;
    }

    /** @return array{float, float} the mean and the sum of squared deviations (Welford) */
    private function welford(): array
    {
        $count = 0;
        $mean = 0.0;
        $m2 = 0.0;

        foreach ($this->values as $value) {
            $count++;
            $delta = $value - $mean;
            $mean += is_finite($delta) ? $delta / $count : $value / $count - $mean / $count;
            $m2 += $delta * ($value - $mean);
        }

        return [$mean, $m2];
    }

    /**
     * @return array{float, float, float} the co-moment and both sums of squared deviations
     *
     * @throws SampleSizeMismatchException when the samples differ in size.
     */
    private function comoments(self $other): array
    {
        if ($this->count() !== $other->count()) {
            throw new SampleSizeMismatchException('Both samples must have the same number of values.');
        }

        $count = 0;
        $meanX = 0.0;
        $meanY = 0.0;
        $m2x = 0.0;
        $m2y = 0.0;
        $comoment = 0.0;

        foreach ($this->values as $index => $x) {
            $y = $other->values[$index];
            $count++;
            $deltaX = $x - $meanX;
            $deltaY = $y - $meanY;
            $meanX += $deltaX / $count;
            $meanY += $deltaY / $count;
            $m2x += $deltaX * ($x - $meanX);
            $m2y += $deltaY * ($y - $meanY);
            $comoment += $deltaX * ($y - $meanY);
        }

        return [$comoment, $m2x, $m2y];
    }
}
