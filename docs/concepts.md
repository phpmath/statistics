---
title: Core concepts
intro: The few ideas you need to use the statistics library well.
group: Getting started
order: 20
---

## Values are immutable

`Sample`, `Quartiles`, `NormalDistribution` and `LinearRegression` are all `final readonly` classes. Once created, a value never changes. Methods such as `plus()` or `sorted()` return a new instance and leave the original untouched, so you can pass values around without defensive copies.

```php
use PHPMath\Statistics\Sample;

$prices = new Sample([10, 20, 30]);
$raised = $prices->plus(5);

$prices->toArray(); // [10.0, 20.0, 30.0]
$raised->toArray(); // [15.0, 25.0, 35.0]
```

Trying to write to a public property, such as `$quartiles->first`, throws a PHP `Error` because the property is readonly.

## Creating values

A `Sample` is created from a list of numbers. It must hold at least one value, and every value must be an `int` or a `float` and finite (not `NAN` and not infinite). Integers are converted to floats, and the order of the values is kept.

```php
use PHPMath\Statistics\Sample;

$sample = new Sample([3, 1, 2.5]);

$sample->toArray(); // [3.0, 1.0, 2.5]
count($sample);     // 3
```

`NormalDistribution` is created from a mean and a standard deviation. `LinearRegression` has no public constructor: you create one with `LinearRegression::fit()`. `Quartiles` is normally created for you by `Sample::quartiles()`.

```php
use PHPMath\Statistics\LinearRegression;
use PHPMath\Statistics\NormalDistribution;
use PHPMath\Statistics\Sample;

$distribution = new NormalDistribution(mean: 100.0, standardDeviation: 15.0);
$regression = LinearRegression::fit(new Sample([1, 2, 3]), new Sample([2, 4, 6]));

$distribution->mean;  // 100.0
$regression->slope;   // 2.0
```

> [!WARNING]
> The constructor accepts only `int` and `float` values. Any other type, including a numeric string such as `'1.5'`, `null` and `true`, throws [`NonNumericValueException`](errors.md) with a message that names the position of the value. Convert text input yourself, for example with `(float)` after you validated it, before you pass it in. The results of the callback of `map()` are checked in the same way.

## Reading values

Methods return plain floats, arrays or new objects. A `Sample` is `Countable`, and `toArray()` returns its values as a list of floats. `Quartiles`, `NormalDistribution` and `LinearRegression` expose their results as public readonly properties.

```php
use PHPMath\Statistics\Sample;

$sample = new Sample([4, 8, 6]);

$sample->toArray();  // [4.0, 8.0, 6.0]
$sample->count();    // 3
$sample->mean();     // 6.0
```

Because the properties are public, there is no getter such as `getSlope()`. Read `$regression->slope` directly.

## Sample and population statistics

Some statistics come in two versions. The population version treats your data as every value that exists. The sample version treats your data as a small part of a larger group, and corrects for that. Methods say which one they are in their name: `populationVariance()` and `sampleVariance()`, `populationCovariance()` and `sampleCovariance()`. The methods `skewness()`, `correlation()` and `zScores()` use the population version and have no sample twin.

The sample versions divide by `n - 1` instead of `n`, so they need at least two values. With one value they throw [`InsufficientDataException`](errors.md).

## Errors

The library throws specific exceptions for bad input. They all implement `StatisticsException`, so one `catch` block can handle every one of them. See [Errors and exceptions](errors.md) for the full list.

```php
use PHPMath\Statistics\Exception\EmptySampleException;
use PHPMath\Statistics\Sample;

try {
    new Sample([]);
} catch (EmptySampleException $exception) {
    // A sample requires at least one value.
}
```

## Floating point numbers

All results are PHP floats, so they carry the usual rounding limits of floating point arithmetic. The library reduces the error where it can: sums use compensated (Neumaier) summation, and variance, skewness and covariance use single pass Welford updates, which stay accurate when values are large and close together. Compare floats with a small tolerance, or round them before you show them.

Results of operations are not checked beyond the input. The mean and the variances use Welford's running algorithm, which does not overflow for values that are themselves finite, but `sum()` is a plain sum and can overflow to infinity, and a variance can overflow to infinity when the true result is larger than the biggest float.

```php
use PHPMath\Statistics\Sample;

$sample = new Sample([1, 2, 3, 4, 5]);

round($sample->sampleStandardDeviation(), 3); // 1.581
```
