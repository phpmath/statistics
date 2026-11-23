---
title: Errors and exceptions
intro: What can go wrong and how to catch it.
group: Reference
order: 90
---

## Library exceptions

Every exception the library throws implements the marker interface `PHPMath\Statistics\Exception\StatisticsException`, so one `catch` block handles all of them. Each exception also extends an exception from PHP, which says what kind of problem it is:

- `InvalidArgumentException`: an argument has an invalid value or shape.
- `DomainException`: the arguments are fine in general, but the result is mathematically undefined for them.

All exceptions live in the `PHPMath\Statistics\Exception` namespace.

| Exception | Extends | Thrown when |
|---|---|---|
| `EmptySampleException` | `InvalidArgumentException` | A `Sample` is created without values. |
| `NonNumericValueException` | `InvalidArgumentException` | A value given to `new Sample()`, or returned by the callback of `map()`, is not an `int` or a `float`. |
| `NonFiniteValueException` | `InvalidArgumentException` | A value is `NAN` or infinite, a distribution mean is not finite, a quartile is not finite, or `x` is `NAN` for `pdf()` and `cdf()`. |
| `InvalidQuartilesException` | `InvalidArgumentException` | The quartiles passed to `new Quartiles()` are not in ascending order. |
| `InsufficientDataException` | `InvalidArgumentException` | A sample has too few values, which is fewer than two for the sample versions of variance, standard deviation and covariance. |
| `InvalidPercentileException` | `InvalidArgumentException` | A percentile is outside 0 to 100, or `NAN`. |
| `InvalidProbabilityException` | `InvalidArgumentException` | A probability is not strictly between 0 and 1, or is `NAN`. |
| `InvalidStandardDeviationException` | `InvalidArgumentException` | A standard deviation is not finite and greater than zero. |
| `SampleSizeMismatchException` | `InvalidArgumentException` | Two samples must have the same size but do not. |
| `ZeroVarianceException` | `DomainException` | A statistic is undefined because all values are equal. |

```php
use PHPMath\Statistics\Exception\StatisticsException;
use PHPMath\Statistics\Sample;

try {
    (new Sample([4, 4, 4]))->skewness();
} catch (StatisticsException $exception) {
    echo $exception->getMessage(); // The skewness is undefined when all values are equal.
}
```

## When each exception is thrown

This table lists every method that rejects its input.

| Method | Exception | Condition |
|---|---|---|
| `new Sample()` | `EmptySampleException` | The list is empty. |
| `new Sample()` | `NonNumericValueException` | A value is not an `int` or a `float`, for example a string. |
| `new Sample()` | `NonFiniteValueException` | A value is `NAN` or infinite. |
| `map()` | `NonNumericValueException` | The callback returns something other than an `int` or a `float`. |
| `map()`, `plus()`, `times()` | `NonFiniteValueException` | A result is `NAN` or infinite. |
| `new Quartiles()` | `NonFiniteValueException` | A quartile is `NAN` or infinite. |
| `new Quartiles()` | `InvalidQuartilesException` | The values do not satisfy `first <= second <= third`. |
| `sampleVariance()`, `sampleStandardDeviation()` | `InsufficientDataException` | The sample has one value. |
| `sampleCovariance()` | `InsufficientDataException` | The samples have one value. |
| `percentile()` | `InvalidPercentileException` | The percentile is outside 0 to 100, or `NAN`. |
| `skewness()`, `zScores()` | `ZeroVarianceException` | All values are equal. |
| `correlation()` | `ZeroVarianceException` | Either sample has all values equal. |
| `populationCovariance()`, `sampleCovariance()`, `correlation()` | `SampleSizeMismatchException` | The samples differ in size. |
| `new NormalDistribution()` | `NonFiniteValueException` | The mean is `NAN` or infinite. |
| `new NormalDistribution()` | `InvalidStandardDeviationException` | The standard deviation is `NAN`, infinite, zero or negative. |
| `pdf()`, `cdf()` | `NonFiniteValueException` | `x` is `NAN`. |
| `quantile()` | `InvalidProbabilityException` | The probability is 0, 1, outside 0 to 1, or `NAN`. |
| `LinearRegression::fit()` | `SampleSizeMismatchException` | The samples differ in size. |
| `LinearRegression::fit()` | `ZeroVarianceException` | The x values are all equal. |

## Things that do not throw

A few situations return a value instead of throwing, so they are worth knowing.

- Results of operations are not checked. `sum()` returns infinity when the sum is larger than the biggest float, for example for `[1e308, 1e308]`, whose `mean()` is still `1.0E+308`. The variances return infinity when the true result is larger than the biggest float.
- `pdf()` and `cdf()` accept infinite values and return the limits: `cdf(-INF)` is `0.0`, `cdf(INF)` is `1.0` and `pdf(-INF)` and `pdf(INF)` are `0.0`. Only `NAN` throws.
- `populationCovariance()` of samples with one value is `0.0`, which is the correct result.

## Handling untrusted input

Catch the marker interface to handle every statistics error in one place.

```php
use PHPMath\Statistics\Exception\StatisticsException;
use PHPMath\Statistics\Sample;

function average(array $values): ?float
{
    try {
        return (new Sample($values))->mean();
    } catch (StatisticsException) {
        return null; // empty list, or a value that is not a finite int or float
    }
}

average([1, 2, 3]); // 2.0
average([]);        // null
```
