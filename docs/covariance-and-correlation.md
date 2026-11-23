---
title: Covariance and correlation
intro: Measure how two samples of the same size move together.
group: Guides
order: 50
---

## Covariance

Covariance measures whether two samples tend to rise and fall together. It is positive when large values of one sample go with large values of the other, negative when they go opposite ways, and near zero when there is no linear link. The unit of the result is the product of both units, so it is hard to compare between data sets.

Both samples must have the same size, and the values are paired by position. `populationCovariance()` divides by `n`. `sampleCovariance()` divides by `n - 1` and needs at least two values.

```php
use PHPMath\Statistics\Sample;

$x = new Sample([1, 2, 3, 4, 5]);
$y = new Sample([2, 4, 5, 4, 5]);

$x->populationCovariance($y); // 1.2
$x->sampleCovariance($y);     // 1.5
```

## Correlation

The Pearson correlation coefficient rescales the covariance to a number between -1 and 1. A value of 1 means a perfect rising line, -1 means a perfect falling line, and 0 means no linear link. Because it has no unit, you can compare it between data sets. There is only one version of this coefficient, because the `n` and `n - 1` factors cancel out.

```php
use PHPMath\Statistics\Sample;

$x = new Sample([1, 2, 3, 4, 5]);
$y = new Sample([2, 4, 5, 4, 5]);

$x->correlation($y);                              // 0.7745966692414834
round($x->correlation($x->times(-2)), 10);     // -1.0
```

The result is clamped to the range -1 to 1, so rounding errors can never push it outside.

> [!NOTE]
> A high correlation shows that two samples move together. It does not show that one causes the other.

## When the input is not valid

Samples of different sizes throw [`SampleSizeMismatchException`](errors.md). A sample with zero variance has no direction, so `correlation()` throws [`ZeroVarianceException`](errors.md) when either sample has all values equal.

```php
use PHPMath\Statistics\Exception\ZeroVarianceException;
use PHPMath\Statistics\Sample;

try {
    (new Sample([1, 2, 3]))->correlation(new Sample([5, 5, 5]));
} catch (ZeroVarianceException $exception) {
    // The correlation is undefined when a sample has zero variance.
}
```

The sample covariance throws [`InsufficientDataException`](errors.md) for samples with one value. The population covariance of such samples is `0.0`, which is correct: a single pair has no spread, so the two values do not move together or apart.
