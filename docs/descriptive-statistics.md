---
title: Descriptive statistics
intro: Summarize a sample with its size, centre, spread and shape.
group: Guides
order: 30
---

## Size and extremes

These methods describe how much data you have and how far it reaches. `count()` is the number of values, `sum()` adds them up, `min()` and `max()` are the smallest and largest value, and `range()` is the distance between them.

```php
use PHPMath\Statistics\Sample;

$sample = new Sample([2, 4, 4, 4, 5, 5, 7, 9]);

$sample->count(); // 8
$sample->sum();   // 40.0
$sample->min();   // 2.0
$sample->max();   // 9.0
$sample->range(); // 7.0
```

## Mean, median and mode

These three numbers each describe the centre of the data in a different way. The mean is the sum divided by the count. The median is the middle value of the sorted data, or the mean of the two middle values when the count is even. The mode is the value that occurs most often.

```php
use PHPMath\Statistics\Sample;

$sample = new Sample([2, 4, 4, 4, 5, 5, 7, 9]);

$sample->mean();   // 5.0
$sample->median(); // 4.5
$sample->mode();   // [4.0]
```

`mode()` always returns a list, because a sample can have more than one mode. When two values tie, both are returned in ascending order. When every value is unique, every value is a mode.

```php
use PHPMath\Statistics\Sample;

(new Sample([1, 1, 2, 2, 3]))->mode(); // [1.0, 2.0]
(new Sample([3, 1, 2]))->mode();       // [1.0, 2.0, 3.0]
```

## Variance and standard deviation

The variance measures how far values spread around the mean. It is the average of the squared distances to the mean. The standard deviation is the square root of the variance, which puts the spread back in the unit of your data.

The population version divides by `n`. Use it when the sample is all the data there is. The sample version divides by `n - 1` (Bessel's correction). Use it when the sample is a small part of a larger group, because it corrects the tendency to underestimate the spread. The sample version needs at least two values.

```php
use PHPMath\Statistics\Sample;

$sample = new Sample([2, 4, 4, 4, 5, 5, 7, 9]);

$sample->populationVariance();          // 4.0
$sample->populationStandardDeviation(); // 2.0
$sample->sampleVariance();              // 4.571428571428571
$sample->sampleStandardDeviation();     // 2.138089935299395
```

> [!WARNING]
> `sampleVariance()` and `sampleStandardDeviation()` throw [`InsufficientDataException`](errors.md) for a sample with one value. The population versions return `0.0` in that case.

## Skewness

Skewness measures how lopsided the data is. A value near zero means the data is roughly symmetric. A positive value means a long tail to the right, and a negative value means a long tail to the left. The library returns the population skewness (the Fisher-Pearson coefficient g1).

```php
use PHPMath\Statistics\Sample;

(new Sample([2, 4, 4, 4, 5, 5, 7, 9]))->skewness(); // 0.6562500000000003
(new Sample([1, 2, 3, 10]))->skewness();            // 1.0182337649086284
```

When all values are equal there is no spread, so the skewness is undefined and [`ZeroVarianceException`](errors.md) is thrown. This includes a sample with a single value.

## Z-scores

A z-score says how many standard deviations a value lies above or below the mean. `zScores()` returns a new `Sample` with the z-score of every value, in the original order. It uses the population standard deviation.

```php
use PHPMath\Statistics\Sample;

$scores = (new Sample([2, 4, 4, 4, 5, 5, 7, 9]))->zScores();

$scores->toArray(); // [-1.5, -0.5, -0.5, -0.5, 0.0, 0.0, 1.0, 2.0]
$scores->mean();    // 0.0
```

## Deriving new samples

Four methods return a new `Sample` built from the old one. `plus()` adds a number to every value, `times()` multiplies every value, `sorted()` sorts ascending, and `map()` applies your own function to every value.

```php
use PHPMath\Statistics\Sample;

$sample = new Sample([3, 1, 2]);

$sample->plus(10)->toArray();                           // [13.0, 11.0, 12.0]
$sample->times(0.5)->toArray();                         // [1.5, 0.5, 1.0]
$sample->sorted()->toArray();                           // [1.0, 2.0, 3.0]
$sample->map(fn (float $value): float => $value ** 2)->toArray(); // [9.0, 1.0, 4.0]
```

The result goes through the same checks as the constructor. If the callback returns `NAN` or infinity, [`NonFiniteValueException`](errors.md) is thrown, and if it returns something other than an `int` or a `float`, [`NonNumericValueException`](errors.md) is thrown.

> [!NOTE]
> The values of a sample keep their original order. Only `sorted()` changes the order, and it returns a new sample.

## Very large values

The statistics are computed in floating point, so extreme data can overflow. The mean and the variances use Welford's running algorithm, which does not overflow for values that are themselves finite. `sum()` is a plain sum and can overflow to infinity. Results are not checked beyond that: the variance returns infinity when the true result is larger than the biggest float.

```php
use PHPMath\Statistics\Sample;

$sample = new Sample([1e308, 1e308]);

$sample->sum();                // INF
$sample->mean();               // 1.0E+308
$sample->populationVariance(); // 0.0

(new Sample([1e150, -1e150]))->populationVariance(); // 9.999999999999999E+299
(new Sample([1e200, -1e200]))->populationVariance(); // INF
```

The last variance is infinite because the true result, 1e400, is larger than the biggest float. Scale such data down with `times()` first.
