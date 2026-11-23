---
title: API reference
intro: Every public class and method of the statistics library.
group: Reference
order: 80
layout: reference
---

Every class is in the `PHPMath\Statistics` namespace. All classes are final and readonly, and no method changes the instance it is called on. Exceptions are in `PHPMath\Statistics\Exception`.

## Sample

`PHPMath\Statistics\Sample` is a final readonly, countable, non-empty list of finite floats with descriptive statistics. See [Core concepts](concepts.md) and [Descriptive statistics](descriptive-statistics.md).

### __construct()

```php
public function __construct(array $values)
```

Creates a sample. Only `int` and `float` values are accepted, so strings (also numeric ones), `null` and booleans are rejected. Integers are converted to floats and the order is kept.

- **Parameters:** `$values` a list of at least one finite `int` or `float`
- **Throws:** [EmptySampleException](errors.md) if no values are given
- **Throws:** [NonNumericValueException](errors.md) if a value is not an `int` or a `float`, with the position in the message
- **Throws:** [NonFiniteValueException](errors.md) if a value is `NAN` or infinite

### toArray()

```php
public function toArray(): array
```

Returns the values in their original order.

- **Returns:** `list<float>`

### count()

```php
public function count(): int
```

Returns the number of values. This implements `Countable`.

- **Returns:** `int`

### sum()

```php
public function sum(): float
```

Returns the sum, computed with compensated (Neumaier) summation. The result is not checked: it is infinity when the sum exceeds the largest float.

- **Returns:** `float`

### min()

```php
public function min(): float
```

Returns the smallest value.

- **Returns:** `float`

### max()

```php
public function max(): float
```

Returns the largest value.

- **Returns:** `float`

### range()

```php
public function range(): float
```

Returns `max() - min()`.

- **Returns:** `float`

### mean()

```php
public function mean(): float
```

Returns the arithmetic mean, computed with Welford's running algorithm, so it does not overflow for finite values.

- **Returns:** `float`

### median()

```php
public function median(): float
```

Returns the middle value of the sorted data, or the mean of the two middle values when the count is even.

- **Returns:** `float`

### mode()

```php
public function mode(): array
```

Returns every value that occurs most often, in ascending order. When all values are unique, every value is returned.

- **Returns:** `list<float>`

### populationVariance()

```php
public function populationVariance(): float
```

Returns the population variance: the sum of squared deviations divided by n, computed with Welford's running algorithm. A sample with one value gives `0.0`. The result is infinity when the true variance exceeds the largest float.

- **Returns:** `float`

### sampleVariance()

```php
public function sampleVariance(): float
```

Returns the sample variance: the sum of squared deviations divided by n - 1 (Bessel's correction).

- **Returns:** `float`
- **Throws:** [InsufficientDataException](errors.md) if the sample has fewer than two values

### populationStandardDeviation()

```php
public function populationStandardDeviation(): float
```

Returns the square root of the population variance.

- **Returns:** `float`

### sampleStandardDeviation()

```php
public function sampleStandardDeviation(): float
```

Returns the square root of the sample variance.

- **Returns:** `float`
- **Throws:** [InsufficientDataException](errors.md) if the sample has fewer than two values

### percentile()

```php
public function percentile(float $p): float
```

Returns the percentile using linear interpolation between closest ranks (Hyndman and Fan type 7, the default of R and NumPy, and the same as Excel's `PERCENTILE.INC`).

- **Parameters:** `$p` a percentile from 0 to 100, inclusive
- **Returns:** `float`
- **Throws:** [InvalidPercentileException](errors.md) if the percentile is outside 0 to 100, or `NAN`

### quartiles()

```php
public function quartiles(): Quartiles
```

Returns the first, second and third quartile, using the interpolation of `percentile()`.

- **Returns:** `Quartiles`

### interquartileRange()

```php
public function interquartileRange(): float
```

Returns the third quartile minus the first quartile.

- **Returns:** `float`

### skewness()

```php
public function skewness(): float
```

Returns the population skewness (Fisher-Pearson coefficient g1): the third central moment divided by the cube of the population standard deviation.

- **Returns:** `float`
- **Throws:** [ZeroVarianceException](errors.md) if all values are equal

### populationCovariance()

```php
public function populationCovariance(self $other): float
```

Returns the population covariance with another sample, dividing by n. The values are paired by position. Samples with one value give `0.0`.

- **Parameters:** `$other` a sample of the same size
- **Returns:** `float`
- **Throws:** [SampleSizeMismatchException](errors.md) if the samples differ in size

### sampleCovariance()

```php
public function sampleCovariance(self $other): float
```

Returns the sample covariance with another sample, dividing by n - 1.

- **Parameters:** `$other` a sample of the same size
- **Returns:** `float`
- **Throws:** [SampleSizeMismatchException](errors.md) if the samples differ in size
- **Throws:** [InsufficientDataException](errors.md) if the samples have fewer than two values

### correlation()

```php
public function correlation(self $other): float
```

Returns the Pearson correlation coefficient with another sample. The result is clamped to the range -1 to 1.

- **Parameters:** `$other` a sample of the same size
- **Returns:** `float`
- **Throws:** [SampleSizeMismatchException](errors.md) if the samples differ in size
- **Throws:** [ZeroVarianceException](errors.md) if either sample has all values equal

### zScores()

```php
public function zScores(): self
```

Returns a new sample with the standard scores `(x - mean) / population standard deviation`.

- **Returns:** `Sample`
- **Throws:** [ZeroVarianceException](errors.md) if all values are equal

### map()

```php
public function map(callable $callback): self
```

Returns a new sample with the callback applied to every value.

- **Parameters:** `$callback` a `callable(float): float`
- **Returns:** `Sample`
- **Throws:** [NonFiniteValueException](errors.md) if the callback returns `NAN` or infinity
- **Throws:** [NonNumericValueException](errors.md) if the callback returns something other than an `int` or a `float`

### plus()

```php
public function plus(float $amount): self
```

Returns a new sample with the amount added to every value.

- **Parameters:** `$amount` the number to add
- **Returns:** `Sample`
- **Throws:** [NonFiniteValueException](errors.md) if a result is infinite

### times()

```php
public function times(float $factor): self
```

Returns a new sample with every value multiplied by the factor.

- **Parameters:** `$factor` the multiplier
- **Returns:** `Sample`
- **Throws:** [NonFiniteValueException](errors.md) if a result is infinite or `NAN`

### sorted()

```php
public function sorted(): self
```

Returns a new sample with the values in ascending order.

- **Returns:** `Sample`

## Quartiles

`PHPMath\Statistics\Quartiles` is a final readonly value object that holds the three quartiles of a sample. Create it with `Sample::quartiles()`. See [Percentiles and quartiles](percentiles-and-quartiles.md).

### __construct()

```php
public function __construct(
    public float $first,
    public float $second,
    public float $third,
)
```

Creates a quartiles object. The values are exposed as public readonly properties.

- **Parameters:** `$first` the 25th percentile, `$second` the median, `$third` the 75th percentile
- **Throws:** [NonFiniteValueException](errors.md) if a value is `NAN` or infinite
- **Throws:** [InvalidQuartilesException](errors.md) if the values do not satisfy `first <= second <= third`

## NormalDistribution

`PHPMath\Statistics\NormalDistribution` is a final readonly normal (Gaussian) distribution. The properties `mean` and `standardDeviation` are public and readonly. See [Normal distribution](normal-distribution.md).

### __construct()

```php
public function __construct(
    public float $mean,
    public float $standardDeviation,
)
```

Creates a distribution.

- **Parameters:** `$mean` the centre of the curve, `$standardDeviation` the width of the curve, greater than zero
- **Throws:** [NonFiniteValueException](errors.md) if the mean is `NAN` or infinite
- **Throws:** [InvalidStandardDeviationException](errors.md) if the standard deviation is not finite and greater than zero

### pdf()

```php
public function pdf(float $x): float
```

Returns the probability density at `x`. Infinite values are allowed and give `0.0`.

- **Parameters:** `$x` the value to evaluate
- **Returns:** `float`
- **Throws:** [NonFiniteValueException](errors.md) if `x` is `NAN`

### cdf()

```php
public function cdf(float $x): float
```

Returns the cumulative probability P(X <= x). The tails are accurate to about 1e-14 relative. Infinite values are allowed: `-INF` gives `0.0` and `INF` gives `1.0`.

- **Parameters:** `$x` the value to evaluate
- **Returns:** `float`
- **Throws:** [NonFiniteValueException](errors.md) if `x` is `NAN`

### quantile()

```php
public function quantile(float $probability): float
```

Returns the value `x` for which P(X <= x) equals the probability. It is found by bisection on `cdf()`.

- **Parameters:** `$probability` a probability strictly between 0 and 1
- **Returns:** `float`
- **Throws:** [InvalidProbabilityException](errors.md) if the probability is not strictly between 0 and 1

## LinearRegression

`PHPMath\Statistics\LinearRegression` is a final readonly simple linear regression (ordinary least squares) `y = intercept + slope * x`. The properties `slope`, `intercept` and `rSquared` are public and readonly. The constructor is private, so use `fit()`. See [Linear regression](linear-regression.md).

### fit()

```php
public static function fit(Sample $x, Sample $y): self
```

Fits the line to paired values. When y is constant, the line is horizontal and `rSquared` is 1.0.

- **Parameters:** `$x` the x values, `$y` the y values, of the same size
- **Returns:** `LinearRegression`
- **Throws:** [SampleSizeMismatchException](errors.md) if the samples differ in size
- **Throws:** [ZeroVarianceException](errors.md) if all x values are equal

### predict()

```php
public function predict(float $x): float
```

Returns `intercept + slope * x`.

- **Parameters:** `$x` the x value to predict for
- **Returns:** `float`
